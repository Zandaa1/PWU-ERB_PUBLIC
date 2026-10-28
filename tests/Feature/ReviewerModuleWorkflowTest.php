<?php

namespace Tests\Feature;

use App\Enums\RequestStatus;
use App\Enums\ResearchDomain;
use App\Enums\ReviewerAssignmentStatus;
use App\Livewire\RequestDetail;
use App\Models\Department;
use App\Models\DocumentType;
use App\Models\DocumentVersion;
use App\Models\EthicsRequest;
use App\Models\RequestDocument;
use App\Models\ReviewerAssignment;
use App\Models\User;
use Database\Seeders\DepartmentSeeder;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use Tests\TestCase;

class ReviewerModuleWorkflowTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed([RoleSeeder::class, DepartmentSeeder::class]);
        Storage::fake('private');
    }

    public function test_reviewer_can_accept_and_submit_dual_assessments(): void
    {
        $rev1 = User::factory()->create(['name' => 'Dr. Reviewer One']);
        $rev1->assignRole('committee');

        $student = User::factory()->create();
        $dept = Department::first();
        $request = EthicsRequest::create([
            'student_id' => $student->id,
            'department_id' => $dept->id,
            'title' => 'Pediatric Pain Management',
            'status' => RequestStatus::UnderReview,
            'research_domain' => ResearchDomain::Health,
            'is_local' => true,
        ]);

        $assignment1 = ReviewerAssignment::create([
            'ethics_request_id' => $request->id,
            'user_id' => $rev1->id,
            'status' => ReviewerAssignmentStatus::Invited,
        ]);

        // Reviewer sees invitation banner and accepts invitation
        Livewire::actingAs($rev1)
            ->test(RequestDetail::class, ['id' => $request->id])
            ->assertSee('Committee Review Invitation')
            ->assertSee('Accept Invitation')
            ->assertSee('Decline Invitation')
            ->call('acceptReviewInvitation', $assignment1->id)
            ->assertHasNoErrors();

        $this->assertEquals(ReviewerAssignmentStatus::Accepted, $assignment1->fresh()->status);

        // Reviewer sees assessment form, downloads, and uploads both completed assessment forms
        $consentFile = UploadedFile::fake()->create('filled_consent_assessment.docx', 150, 'application/vnd.openxmlformats-officedocument.wordprocessingml.document');
        $protocolFile = UploadedFile::fake()->create('filled_protocol_assessment.docx', 180, 'application/vnd.openxmlformats-officedocument.wordprocessingml.document');

        Livewire::actingAs($rev1)
            ->test(RequestDetail::class, ['id' => $request->id])
            ->assertSee('Reviewer Assessment & Dual Form Submission', false)
            ->assertSee('Informed Consent Assessment Form')
            ->assertSee('Study Protocol Assessment Form')
            ->assertSee('/templates/assessment-consent/download', false)
            ->assertSee('/templates/assessment-protocol/download', false)
            ->set('assessment_consent_file', $consentFile)
            ->set('assessment_protocol_file', $protocolFile)
            ->set('assessment_recommendation', 'approved')
            ->set('assessment_notes', 'All ethical criteria satisfied.')
            ->call('submitReviewerEvaluation', $assignment1->id)
            ->assertHasNoErrors();

        $freshAss = $assignment1->fresh();
        $this->assertEquals(ReviewerAssignmentStatus::Completed, $freshAss->status);
        $this->assertEquals('approved', $freshAss->recommendation);
        $this->assertNotNull($freshAss->consent_assessment_path);
        $this->assertNotNull($freshAss->protocol_assessment_path);

        // Reviewer now sees completed summary card
        Livewire::actingAs($rev1)
            ->test(RequestDetail::class, ['id' => $request->id])
            ->assertSee('Your Review Evaluation')
            ->assertSee('Completed')
            ->assertSee('Informed Consent Assessment submitted')
            ->assertSee('Protocol Assessment submitted')
            ->assertSee('All ethical criteria satisfied.');
    }

    public function test_reviewer_can_decline_invitation(): void
    {
        $rev = User::factory()->create(['name' => 'Busy Reviewer']);
        $rev->assignRole('committee');

        $student = User::factory()->create();
        $dept = Department::first();
        $request = EthicsRequest::create([
            'student_id' => $student->id,
            'department_id' => $dept->id,
            'title' => 'Conflict of Interest Protocol',
            'status' => RequestStatus::UnderReview,
            'research_domain' => ResearchDomain::Social,
            'is_local' => true,
        ]);

        $assignment = ReviewerAssignment::create([
            'ethics_request_id' => $request->id,
            'user_id' => $rev->id,
            'status' => ReviewerAssignmentStatus::Invited,
        ]);

        Livewire::actingAs($rev)
            ->test(RequestDetail::class, ['id' => $request->id])
            ->set('decline_reason', 'Direct conflict of interest with PI.')
            ->call('declineReviewInvitation', $assignment->id)
            ->assertHasNoErrors();

        $this->assertEquals(ReviewerAssignmentStatus::Declined, $assignment->fresh()->status);
        $this->assertEquals('Direct conflict of interest with PI.', $assignment->fresh()->decline_reason);
    }

    public function test_reviewer_cannot_submit_without_required_files(): void
    {
        $rev = User::factory()->create(['name' => 'Test Reviewer']);
        $rev->assignRole('committee');

        $student = User::factory()->create();
        $dept = Department::first();
        $request = EthicsRequest::create([
            'student_id' => $student->id,
            'department_id' => $dept->id,
            'title' => 'Validation Test Protocol',
            'status' => RequestStatus::UnderReview,
            'research_domain' => ResearchDomain::Health,
            'is_local' => true,
        ]);

        $assignment = ReviewerAssignment::create([
            'ethics_request_id' => $request->id,
            'user_id' => $rev->id,
            'status' => ReviewerAssignmentStatus::Accepted,
        ]);

        Livewire::actingAs($rev)
            ->test(RequestDetail::class, ['id' => $request->id])
            ->call('submitReviewerEvaluation', $assignment->id)
            ->assertHasErrors(['assessment_consent_file', 'assessment_protocol_file', 'assessment_recommendation']);
    }

    public function test_unauthorized_user_cannot_respond_or_submit_evaluation(): void
    {
        $rev1 = User::factory()->create(['name' => 'Reviewer One']);
        $rev1->assignRole('committee');

        $unauthorized = User::factory()->create(['name' => 'Reviewer Two']);
        $unauthorized->assignRole('committee');

        $student = User::factory()->create();
        $dept = Department::first();
        $request = EthicsRequest::create([
            'student_id' => $student->id,
            'department_id' => $dept->id,
            'title' => 'Auth Test Protocol',
            'status' => RequestStatus::UnderReview,
            'research_domain' => ResearchDomain::Health,
            'is_local' => true,
        ]);

        $assignment = ReviewerAssignment::create([
            'ethics_request_id' => $request->id,
            'user_id' => $rev1->id,
            'status' => ReviewerAssignmentStatus::Invited,
        ]);

        // Unauthorized committee member tries to accept
        Livewire::actingAs($unauthorized)
            ->test(RequestDetail::class, ['id' => $request->id])
            ->call('acceptReviewInvitation', $assignment->id)
            ->assertForbidden();

        // Unauthorized committee member tries to decline
        Livewire::actingAs($unauthorized)
            ->test(RequestDetail::class, ['id' => $request->id])
            ->call('declineReviewInvitation', $assignment->id)
            ->assertForbidden();

        // Unauthorized committee member tries to submit evaluation
        $consentFile = UploadedFile::fake()->create('filled_consent_assessment.docx', 150, 'application/vnd.openxmlformats-officedocument.wordprocessingml.document');
        $protocolFile = UploadedFile::fake()->create('filled_protocol_assessment.docx', 180, 'application/vnd.openxmlformats-officedocument.wordprocessingml.document');

        Livewire::actingAs($unauthorized)
            ->test(RequestDetail::class, ['id' => $request->id])
            ->set('assessment_consent_file', $consentFile)
            ->set('assessment_protocol_file', $protocolFile)
            ->set('assessment_recommendation', 'approved')
            ->call('submitReviewerEvaluation', $assignment->id)
            ->assertForbidden();
    }

    public function test_missing_optional_assessment_templates_return_not_found(): void
    {
        $reviewer = User::factory()->create();
        $reviewer->assignRole('committee');

        $consentResponse = $this->actingAs($reviewer)->get('/templates/assessment-consent/download');
        $consentResponse->assertStatus(404);

        $protocolResponse = $this->actingAs($reviewer)->get('/templates/assessment-protocol/download');
        $protocolResponse->assertStatus(404);
    }

    public function test_operator_supplied_assessment_template_can_be_downloaded(): void
    {
        $reviewer = User::factory()->create();
        $reviewer->assignRole('committee');
        $path = storage_path('app/templates/DEMO_ERB_Form_Informed_Consent_Assessment.docx');
        if (! is_dir(dirname($path))) {
            mkdir(dirname($path), 0755, true);
        }
        // Synthetic bytes only: this checks response handling, not Word rendering.
        file_put_contents($path, 'synthetic assessment template test fixture');
        try {
            $this->actingAs($reviewer)->get('/templates/assessment-consent/download')
                ->assertOk()
                ->assertDownload('DEMO_ERB_Form_Informed_Consent_Assessment.docx');
        } finally {
            unlink($path);
        }
    }

    public function test_reviewer_cannot_accept_or_decline_non_invited_assignment(): void
    {
        $rev = User::factory()->create();
        $rev->assignRole('committee');

        $student = User::factory()->create();
        $dept = Department::first();
        $request = EthicsRequest::create([
            'student_id' => $student->id,
            'department_id' => $dept->id,
            'title' => 'State Guard Test Protocol',
            'status' => RequestStatus::UnderReview,
            'research_domain' => ResearchDomain::Health,
            'is_local' => true,
        ]);

        $assignment = ReviewerAssignment::create([
            'ethics_request_id' => $request->id,
            'user_id' => $rev->id,
            'status' => ReviewerAssignmentStatus::Accepted,
        ]);

        Livewire::actingAs($rev)
            ->test(RequestDetail::class, ['id' => $request->id])
            ->call('acceptReviewInvitation', $assignment->id)
            ->assertStatus(400);

        Livewire::actingAs($rev)
            ->test(RequestDetail::class, ['id' => $request->id])
            ->call('declineReviewInvitation', $assignment->id)
            ->assertStatus(400);
    }

    public function test_reviewer_cannot_submit_evaluation_for_non_accepted_assignment(): void
    {
        $rev = User::factory()->create();
        $rev->assignRole('committee');

        $student = User::factory()->create();
        $dept = Department::first();
        $request = EthicsRequest::create([
            'student_id' => $student->id,
            'department_id' => $dept->id,
            'title' => 'Evaluation State Guard Protocol',
            'status' => RequestStatus::UnderReview,
            'research_domain' => ResearchDomain::Health,
            'is_local' => true,
        ]);

        $assignment = ReviewerAssignment::create([
            'ethics_request_id' => $request->id,
            'user_id' => $rev->id,
            'status' => ReviewerAssignmentStatus::Invited,
        ]);

        $consentFile = UploadedFile::fake()->create('filled_consent.docx', 150, 'application/vnd.openxmlformats-officedocument.wordprocessingml.document');
        $protocolFile = UploadedFile::fake()->create('filled_protocol.docx', 180, 'application/vnd.openxmlformats-officedocument.wordprocessingml.document');

        Livewire::actingAs($rev)
            ->test(RequestDetail::class, ['id' => $request->id])
            ->set('assessment_consent_file', $consentFile)
            ->set('assessment_protocol_file', $protocolFile)
            ->set('assessment_recommendation', 'approved')
            ->call('submitReviewerEvaluation', $assignment->id)
            ->assertStatus(400);
    }

    public function test_reviewer_recommendation_must_be_valid(): void
    {
        $rev = User::factory()->create();
        $rev->assignRole('committee');

        $student = User::factory()->create();
        $dept = Department::first();
        $request = EthicsRequest::create([
            'student_id' => $student->id,
            'department_id' => $dept->id,
            'title' => 'Recommendation Enum Validation Protocol',
            'status' => RequestStatus::UnderReview,
            'research_domain' => ResearchDomain::Health,
            'is_local' => true,
        ]);

        $assignment = ReviewerAssignment::create([
            'ethics_request_id' => $request->id,
            'user_id' => $rev->id,
            'status' => ReviewerAssignmentStatus::Accepted,
        ]);

        $consentFile = UploadedFile::fake()->create('filled_consent.docx', 150, 'application/vnd.openxmlformats-officedocument.wordprocessingml.document');
        $protocolFile = UploadedFile::fake()->create('filled_protocol.docx', 180, 'application/vnd.openxmlformats-officedocument.wordprocessingml.document');

        Livewire::actingAs($rev)
            ->test(RequestDetail::class, ['id' => $request->id])
            ->set('assessment_consent_file', $consentFile)
            ->set('assessment_protocol_file', $protocolFile)
            ->set('assessment_recommendation', 'invalid_recommendation')
            ->call('submitReviewerEvaluation', $assignment->id)
            ->assertHasErrors(['assessment_recommendation']);
    }

    public function test_document_download_route_is_accessible(): void
    {
        Storage::fake('local');

        $student = User::factory()->create(['is_active' => true]);
        $student->assignRole('student');

        $dept = Department::first();
        $request = EthicsRequest::create([
            'student_id' => $student->id,
            'department_id' => $dept->id,
            'title' => 'Download Route Test Protocol',
            'status' => RequestStatus::Submitted,
            'research_domain' => ResearchDomain::Health,
            'is_local' => true,
        ]);

        $docType = DocumentType::firstOrCreate(
            ['name' => 'Informed Consent Form'],
            ['is_required' => true, 'is_active' => true, 'sort_order' => 1]
        );

        $requestDoc = RequestDocument::create([
            'ethics_request_id' => $request->id,
            'document_type_id' => $docType->id,
        ]);

        $file = UploadedFile::fake()->create('test_protocol.pdf', 200, 'application/pdf');
        $path = $file->store('documents/'.$request->id.'/1/1', 'local');

        $version = DocumentVersion::create([
            'request_document_id' => $requestDoc->id,
            'version_number' => 1,
            'file_path' => $path,
            'file_name' => 'test_protocol.pdf',
            'file_size' => 200 * 1024,
            'uploaded_by' => $student->id,
        ]);

        $response = $this->actingAs($student)->get("/documents/download/{$version->id}");
        $response->assertStatus(200);
    }
}
