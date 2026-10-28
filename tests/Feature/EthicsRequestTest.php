<?php

namespace Tests\Feature;

use App\Enums\DocumentStatus;
use App\Enums\RequestStatus;
use App\Livewire\DocumentViewer;
use App\Livewire\RequestDetail;
use App\Livewire\StudentDashboard;
use App\Models\Department;
use App\Models\DocumentType;
use App\Models\DocumentVersion;
use App\Models\EthicsRequest;
use App\Models\RequestDocument;
use App\Models\User;
use App\Services\ControlNumberService;
use App\Services\DocumentService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class EthicsRequestTest extends TestCase
{
    use RefreshDatabase;

    public function test_control_number_generation_is_atomic_and_padded()
    {
        $service = app(ControlNumberService::class);
        $number1 = $service->generateNext();
        $number2 = $service->generateNext();

        $year = date('Y');
        $this->assertEquals("ERB{$year}_00001", $number1);
        $this->assertEquals("ERB{$year}_00002", $number2);
    }

    public function test_request_approval_flow()
    {
        $this->seed();

        $student = User::where('email', 'student@example.test')->first();
        $committee = User::where('email', 'reviewer@example.test')->first();
        $chairperson = User::where('email', 'dean@example.test')->first();
        $department = Department::first();

        $request = EthicsRequest::create([
            'student_id' => $student->id,
            'title' => 'Test Study on Nursing Ethics',
            'department_id' => $department->id,
            'status' => RequestStatus::Submitted,
        ]);

        $types = DocumentType::all();
        foreach ($types as $t) {
            RequestDocument::create([
                'ethics_request_id' => $request->id,
                'document_type_id' => $t->id,
                'status' => DocumentStatus::Approved, // Mark all approved for testing final approval
            ]);
        }

        $service = app(DocumentService::class);
        $mockSignature = 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mNk+M9QDwADhgGAWjR9awAAAABJRU5ErkJggg==';

        $certPath = $service->approveEntireRequest($request, $chairperson, $mockSignature);

        $request->refresh();
        $this->assertEquals(RequestStatus::Approved, $request->status);
        $this->assertNotNull($request->control_number);
        $this->assertNotNull($request->certificate_path);
        $this->assertNotNull($request->approval_date);
    }

    public function test_ethical_considerations_page_is_accessible()
    {
        $this->seed();
        $student = User::where('email', 'student@example.test')->first();

        $response = $this->actingAs($student)->get('/requests/ethical-considerations');
        $response->assertStatus(200);
        $response->assertSee('Chapter 2: Ethical Considerations Guidelines');
    }

    public function test_document_viewer_guards_and_no_comment_revision_confirmation()
    {
        $this->seed();
        $committee = User::where('email', 'reviewer@example.test')->first();
        $student = User::where('email', 'student@example.test')->first();
        $department = Department::first();

        $request = EthicsRequest::create([
            'student_id' => $student->id,
            'title' => 'Test Study Guard',
            'department_id' => $department->id,
            'status' => RequestStatus::Submitted,
        ]);

        $type = DocumentType::first();
        $doc = RequestDocument::create([
            'ethics_request_id' => $request->id,
            'document_type_id' => $type->id,
            'status' => DocumentStatus::Pending,
        ]);

        $version = DocumentVersion::create([
            'request_document_id' => $doc->id,
            'file_path' => 'documents/test.pdf',
            'version_number' => 1,
            'uploaded_by' => $student->id,
        ]);

        Livewire::actingAs($committee)
            ->test(DocumentViewer::class, ['requestId' => $request->id, 'documentId' => $doc->id])
            ->call('requestRevision')
            ->assertSet('showNoCommentWarningModal', true);
    }

    public function test_student_dashboard_triggers_revision_feedback_popup()
    {
        $this->seed();
        $student = User::where('email', 'student@example.test')->first();
        $department = Department::first();

        $request = EthicsRequest::create([
            'student_id' => $student->id,
            'title' => 'Test Study with Revisions',
            'department_id' => $department->id,
            'status' => RequestStatus::RevisionsRequested,
            'last_activity_at' => now(),
        ]);

        Livewire::actingAs($student)
            ->test(StudentDashboard::class)
            ->assertSet('showFeedbackPopup', true)
            ->assertSee('Review Committee Feedback Available');
    }

    public function test_committee_reviewer_can_formally_disapprove_protocol_with_remarks()
    {
        $this->seed();
        $reviewer = User::role('committee')->first();
        $student = User::role('student')->first();
        $department = Department::first();

        $request = EthicsRequest::create([
            'student_id' => $student->id,
            'title' => 'Unethical Clinical Study',
            'department_id' => $department->id,
            'status' => RequestStatus::Submitted,
            'last_activity_at' => now(),
        ]);

        Livewire::actingAs($reviewer)
            ->test(RequestDetail::class, ['id' => $request->id])
            ->call('openDisapproveModal')
            ->assertSet('showDisapproveModal', true)
            ->set('disapprovalRemarks', 'Severe violation of participant informed consent standards.')
            ->call('confirmDisapprove')
            ->assertSet('showDisapproveModal', false);

        $this->assertEquals(RequestStatus::Disapproved, $request->fresh()->status);
        $this->assertDatabaseHas('status_logs', [
            'ethics_request_id' => $request->id,
            'new_status' => RequestStatus::Disapproved->value,
        ]);
        $this->assertDatabaseHas('notifications', [
            'user_id' => $student->id,
            'type' => 'status_updated',
        ]);
    }
}
