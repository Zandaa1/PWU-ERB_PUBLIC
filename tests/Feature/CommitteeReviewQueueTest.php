<?php

namespace Tests\Feature;

use App\Enums\DocumentStatus;
use App\Enums\RequestStatus;
use App\Livewire\CommitteeReviewQueue;
use App\Models\Department;
use App\Models\DocumentType;
use App\Models\DocumentVersion;
use App\Models\EthicsRequest;
use App\Models\RequestDocument;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class CommitteeReviewQueueTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_filter_queue_by_resubmitted_documents()
    {
        Role::firstOrCreate(['name' => 'committee', 'guard_name' => 'web']);
        Role::firstOrCreate(['name' => 'student', 'guard_name' => 'web']);

        $committee = User::factory()->create();
        $committee->assignRole('committee');

        $dept = Department::create(['name' => 'Nursing', 'code' => 'NURS', 'is_active' => true]);
        $docType = DocumentType::create(['name' => 'Protocol', 'is_required' => true, 'order' => 1]);

        // Request 1: Only version 1 (Initial)
        $reqInitial = EthicsRequest::create([
            'student_id' => $committee->id,
            'department_id' => $dept->id,
            'title' => 'Initial Submission Study',
            'status' => RequestStatus::Submitted,
        ]);
        $doc1 = RequestDocument::create(['ethics_request_id' => $reqInitial->id, 'document_type_id' => $docType->id, 'status' => DocumentStatus::Pending]);
        DocumentVersion::create(['request_document_id' => $doc1->id, 'file_path' => 'f1.pdf', 'version_number' => 1, 'uploaded_by' => $committee->id]);

        // Request 2: Version 2 (Resubmitted)
        $reqResubmitted = EthicsRequest::create([
            'student_id' => $committee->id,
            'department_id' => $dept->id,
            'title' => 'Resubmitted Study',
            'status' => RequestStatus::UnderReview,
        ]);
        $doc2 = RequestDocument::create(['ethics_request_id' => $reqResubmitted->id, 'document_type_id' => $docType->id, 'status' => DocumentStatus::Pending]);
        DocumentVersion::create(['request_document_id' => $doc2->id, 'file_path' => 'f1.pdf', 'version_number' => 1, 'uploaded_by' => $committee->id]);
        DocumentVersion::create(['request_document_id' => $doc2->id, 'file_path' => 'f2.pdf', 'version_number' => 2, 'uploaded_by' => $committee->id]);

        $this->actingAs($committee);

        Livewire::test(CommitteeReviewQueue::class)
            ->set('resubmissionFilter', 'resubmitted')
            ->assertSee('Resubmitted Study')
            ->assertDontSee('Initial Submission Study')
            ->set('resubmissionFilter', 'initial')
            ->assertSee('Initial Submission Study')
            ->assertDontSee('Resubmitted Study');
    }

    public function test_admin_and_chairperson_can_access_dashboard_review_queue()
    {
        Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
        Role::firstOrCreate(['name' => 'committee', 'guard_name' => 'web']);
        Role::firstOrCreate(['name' => 'student', 'guard_name' => 'web']);

        $admin = User::factory()->create(['email' => 'admin@example.test']);
        $admin->assignRole('admin');

        $chair = User::factory()->create(['email' => 'chair@example.test', 'is_chairperson' => true]);

        $student = User::factory()->create(['email' => 'student_dash@example.test']);
        $student->assignRole('student');

        // Admin visiting /dashboard
        $responseAdmin = $this->actingAs($admin)->get('/dashboard');
        $responseAdmin->assertStatus(200);
        $responseAdmin->assertSee('Review Queue');

        // Chairperson visiting /dashboard
        $responseChair = $this->actingAs($chair)->get('/dashboard');
        $responseChair->assertStatus(200);
        $responseChair->assertSee('Review Queue');

        // Student visiting /dashboard
        $responseStudent = $this->actingAs($student)->get('/dashboard');
        $responseStudent->assertStatus(200);
        $responseStudent->assertSee('My Ethics Requests');
    }

    public function test_queue_shows_7_of_7_approved_when_optional_document_is_not_attached()
    {
        $this->seed();

        $committee = User::where('email', 'reviewer@example.test')->first();
        $dept = Department::first();

        // Clear any sample requests
        EthicsRequest::query()->delete();

        $req = EthicsRequest::create([
            'student_id' => $committee->id,
            'department_id' => $dept->id,
            'title' => 'Willingness to Pay for MTM Services',
            'status' => RequestStatus::Approved,
        ]);

        // Attach 7 approved required documents
        foreach (DocumentType::where('is_required', true)->get() as $reqType) {
            $rd = RequestDocument::create([
                'ethics_request_id' => $req->id,
                'document_type_id' => $reqType->id,
                'status' => DocumentStatus::Approved,
            ]);
            DocumentVersion::create([
                'request_document_id' => $rd->id,
                'file_path' => "file_{$reqType->id}.pdf",
                'version_number' => 1,
                'uploaded_by' => $committee->id,
            ]);
        }

        // Attach 8th optional document slot WITHOUT versions
        $optionalDocType = DocumentType::where('is_required', false)->first();
        if ($optionalDocType) {
            RequestDocument::create([
                'ethics_request_id' => $req->id,
                'document_type_id' => $optionalDocType->id,
                'status' => DocumentStatus::Pending,
            ]);
        }

        $this->actingAs($committee);

        Livewire::test(CommitteeReviewQueue::class)
            ->assertSee('7/7 approved')
            ->assertSee('7/7')
            ->assertDontSee('7/8');
    }
}
