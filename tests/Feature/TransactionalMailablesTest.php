<?php

namespace Tests\Feature;

use App\Enums\DocumentStatus;
use App\Enums\RequestStatus;
use App\Mail\DocumentCommentAddedMail;
use App\Mail\EthicsRequestStatusUpdatedMail;
use App\Mail\EthicsRequestSubmittedMail;
use App\Models\Comment;
use App\Models\Department;
use App\Models\DocumentType;
use App\Models\EthicsRequest;
use App\Models\RequestDocument;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class TransactionalMailablesTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Role::firstOrCreate(['name' => 'student', 'guard_name' => 'web']);
        Role::firstOrCreate(['name' => 'committee', 'guard_name' => 'web']);
    }

    public function test_submission_confirmation_mailable_renders_details_and_control_no(): void
    {
        $student = User::factory()->create(['name' => 'Demo Student', 'email' => 'student@example.test']);
        $dept = Department::create(['name' => 'School of Nursing', 'code' => 'SN']);
        $request = EthicsRequest::create([
            'control_number' => 'ERB2026_00012',
            'student_id' => $student->id,
            'department_id' => $dept->id,
            'title' => 'Digital Health Intervention Study',
            'status' => RequestStatus::Submitted,
        ]);

        $mailable = new EthicsRequestSubmittedMail($request);
        $mailable->assertSeeInHtml('ERB2026_00012');
        $mailable->assertSeeInHtml('Digital Health Intervention Study');
        $mailable->assertSeeInHtml('School of Nursing');
        $mailable->assertSeeInHtml('Demo Student');
        $mailable->assertHasSubject('Ethics Review Submission Acknowledgment - ERB2026_00012');

        $html = $mailable->render();
        $this->assertDoesNotMatchRegularExpression('/[\x{1F600}-\x{1F64F}\x{1F300}-\x{1F5FF}]/u', $html);
    }

    public function test_status_updated_mailable_renders_approved_status(): void
    {
        $student = User::factory()->create(['name' => 'Demo Student', 'email' => 'student@example.test']);
        $dept = Department::create(['name' => 'School of Nursing', 'code' => 'SN']);
        $request = EthicsRequest::create([
            'control_number' => 'ERB2026_00012',
            'student_id' => $student->id,
            'department_id' => $dept->id,
            'title' => 'Digital Health Intervention Study',
            'status' => RequestStatus::Approved,
        ]);

        $mailable = new EthicsRequestStatusUpdatedMail($request, 'Certificate of Approval is ready for download.');
        $mailable->assertSeeInHtml('Approved');
        $mailable->assertSeeInHtml('Certificate of Approval is ready for download.');
        $mailable->assertHasSubject('Ethics Review Decision Update: APPROVED - ERB2026_00012');

        $html = $mailable->render();
        $this->assertDoesNotMatchRegularExpression('/[\x{1F600}-\x{1F64F}\x{1F300}-\x{1F5FF}]/u', $html);
    }

    public function test_status_updated_mailable_renders_revisions_requested_without_remarks(): void
    {
        $student = User::factory()->create(['name' => 'Demo Researcher', 'email' => 'student2@example.test']);
        $dept = Department::create(['name' => 'School of Pharmacy', 'code' => 'SP']);
        $request = EthicsRequest::create([
            'control_number' => 'ERB2026_00015',
            'student_id' => $student->id,
            'department_id' => $dept->id,
            'title' => 'Community Pharmacy Protocol',
            'status' => RequestStatus::RevisionsRequested,
        ]);

        $mailable = new EthicsRequestStatusUpdatedMail($request);
        $mailable->assertSeeInHtml('Revisions Requested');
        $mailable->assertSeeInHtml('ERB2026_00015');
        $mailable->assertHasSubject('Ethics Review Decision Update: REVISIONS REQUESTED - ERB2026_00015');

        $html = $mailable->render();
        $this->assertDoesNotMatchRegularExpression('/[\x{1F600}-\x{1F64F}\x{1F300}-\x{1F5FF}]/u', $html);
    }

    public function test_comment_added_mailable_renders_comment_text(): void
    {
        $student = User::factory()->create(['name' => 'Demo Student', 'email' => 'student@example.test']);
        $reviewer = User::factory()->create(['name' => 'Demo Reviewer', 'email' => 'reviewer@example.test']);
        $dept = Department::create(['name' => 'School of Nursing', 'code' => 'SN']);
        $request = EthicsRequest::create([
            'control_number' => 'ERB2026_00012',
            'student_id' => $student->id,
            'department_id' => $dept->id,
            'title' => 'Digital Health Intervention Study',
            'status' => RequestStatus::UnderReview,
        ]);
        $docType = DocumentType::create(['name' => 'Study Protocol', 'code' => 'protocol', 'is_required' => true, 'sort_order' => 1]);
        $reqDoc = RequestDocument::create([
            'ethics_request_id' => $request->id,
            'document_type_id' => $docType->id,
            'status' => DocumentStatus::UnderReview,
        ]);
        $version = $reqDoc->versions()->create([
            'version_number' => 1,
            'file_path' => 'documents/protocol.pdf',
            'file_name' => 'protocol.pdf',
            'file_size' => 2048,
            'mime_type' => 'application/pdf',
            'uploaded_by' => $student->id,
        ]);
        $comment = Comment::create([
            'document_version_id' => $version->id,
            'commenter_id' => $reviewer->id,
            'comment_text' => 'Please clarify participant recruitment safeguards in Section 3.',
        ]);

        $mailable = new DocumentCommentAddedMail($reqDoc, $comment);
        $mailable->assertSeeInHtml('Study Protocol');
        $mailable->assertSeeInHtml('Please clarify participant recruitment safeguards in Section 3.');
        $mailable->assertSeeInHtml('Demo Reviewer');
        $mailable->assertHasSubject('New Reviewer Feedback: Study Protocol - ERB2026_00012');

        $html = $mailable->render();
        $this->assertDoesNotMatchRegularExpression('/[\x{1F600}-\x{1F64F}\x{1F300}-\x{1F5FF}]/u', $html);
    }
}
