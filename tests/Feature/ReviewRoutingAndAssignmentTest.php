<?php

namespace Tests\Feature;

use App\Enums\RequestStatus;
use App\Enums\ResearchDomain;
use App\Enums\ReviewerAssignmentStatus;
use App\Enums\ReviewType;
use App\Livewire\RequestDetail;
use App\Mail\ReviewerInvitationMail;
use App\Models\Department;
use App\Models\EthicsRequest;
use App\Models\ReviewerAssignment;
use App\Models\User;
use App\Services\ReviewerAssignmentService;
use App\Services\ReviewRoutingService;
use Database\Seeders\DepartmentSeeder;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use InvalidArgumentException;
use Livewire\Livewire;
use Tests\TestCase;

class ReviewRoutingAndAssignmentTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed([RoleSeeder::class, DepartmentSeeder::class]);
    }

    public function test_chair_routes_to_full_review(): void
    {
        $chair = User::factory()->create(['is_chairperson' => true]);
        $chair->assignRole('admin');

        $student = User::factory()->create();
        $dept = Department::first();
        $request = EthicsRequest::create([
            'student_id' => $student->id,
            'department_id' => $dept->id,
            'title' => 'High Risk Clinical Study',
            'status' => RequestStatus::ReviewRouting,
            'research_domain' => ResearchDomain::Health,
            'is_local' => true,
        ]);

        Livewire::actingAs($chair)
            ->test(RequestDetail::class, ['id' => $request->id])
            ->call('routeReviewType', 'full_review')
            ->assertHasNoErrors();

        $fresh = $request->fresh();
        $this->assertEquals(RequestStatus::FullReviewPending, $fresh->status);
        $this->assertEquals(ReviewType::FullReview, $fresh->review_type);

        // Chair completes quarterly deliberation
        Livewire::actingAs($chair)
            ->test(RequestDetail::class, ['id' => $request->id])
            ->set('deliberation_notes', 'Committee deliberated during Q3 meeting. Unanimous consent.')
            ->call('completeFullReviewDeliberation')
            ->assertHasNoErrors();

        $this->assertEquals('Committee deliberated during Q3 meeting. Unanimous consent.', $request->fresh()->deliberation_notes);
    }

    public function test_chair_marks_out_of_scope(): void
    {
        $chair = User::factory()->create(['is_chairperson' => true]);
        $chair->assignRole('admin');

        $student = User::factory()->create();
        $dept = Department::first();
        $request = EthicsRequest::create([
            'student_id' => $student->id,
            'department_id' => $dept->id,
            'title' => 'Commercial Product Survey',
            'status' => RequestStatus::ReviewRouting,
            'research_domain' => ResearchDomain::Other,
            'is_local' => true,
        ]);

        Livewire::actingAs($chair)
            ->test(RequestDetail::class, ['id' => $request->id])
            ->set('out_of_scope_reason', 'Not involving human subjects or thesis scope.')
            ->call('markOutOfScope')
            ->assertHasNoErrors();

        $this->assertEquals(RequestStatus::OutOfScope, $request->fresh()->status);
    }

    public function test_chair_routes_to_expedited_and_assigns_two_domain_reviewers(): void
    {
        Mail::fake();

        $chair = User::factory()->create(['is_chairperson' => true]);
        $chair->assignRole('admin');

        $rev1 = User::factory()->create(['name' => 'Reviewer 1', 'specialty_domains' => ['health']]);
        $rev1->assignRole('committee');
        $rev2 = User::factory()->create(['name' => 'Reviewer 2', 'specialty_domains' => ['health']]);
        $rev2->assignRole('committee');

        $student = User::factory()->create();
        $dept = Department::first();
        $request = EthicsRequest::create([
            'student_id' => $student->id,
            'department_id' => $dept->id,
            'title' => 'Minimal Risk Health Protocol',
            'status' => RequestStatus::ReviewRouting,
            'research_domain' => ResearchDomain::Health,
            'is_local' => true,
        ]);

        Livewire::actingAs($chair)
            ->test(RequestDetail::class, ['id' => $request->id])
            ->call('routeReviewType', 'expedited_review')
            ->assertHasNoErrors();

        $this->assertEquals(RequestStatus::AssigningReviewers, $request->fresh()->status);
        $this->assertEquals(ReviewType::ExpeditedReview, $request->fresh()->review_type);

        Livewire::actingAs($chair)
            ->test(RequestDetail::class, ['id' => $request->id])
            ->set('selected_reviewer_1', (string) $rev1->id)
            ->set('selected_reviewer_2', (string) $rev2->id)
            ->call('assignReviewers')
            ->assertHasNoErrors();

        $fresh = $request->fresh();
        $this->assertEquals(RequestStatus::UnderReview, $fresh->status);
        $this->assertCount(2, $fresh->reviewerAssignments);
        $this->assertEquals(ReviewerAssignmentStatus::Invited, $fresh->reviewerAssignments->first()->status);

        Mail::assertSent(ReviewerInvitationMail::class, 2);
    }

    public function test_reviewer_assignment_service_handles_response_and_evaluations(): void
    {
        Storage::fake('private');
        Mail::fake();

        $chair = User::factory()->create(['is_chairperson' => true]);
        $rev1 = User::factory()->create(['specialty_domains' => ['health']]);
        $rev1->assignRole('committee');
        $rev2 = User::factory()->create(['specialty_domains' => ['health']]);
        $rev2->assignRole('committee');
        $student = User::factory()->create();
        $dept = Department::first();

        $request = EthicsRequest::create([
            'student_id' => $student->id,
            'department_id' => $dept->id,
            'title' => 'Health Evaluation Study',
            'status' => RequestStatus::AssigningReviewers,
            'research_domain' => ResearchDomain::Health,
            'is_local' => true,
        ]);

        $service = app(ReviewerAssignmentService::class);
        $service->assignReviewers($request, [$rev1->id, $rev2->id], $chair);

        $assignments = $request->fresh()->reviewerAssignments;
        $this->assertCount(2, $assignments);

        // Test responding to invitation
        $assignment1 = $assignments[0];
        $assignment2 = $assignments[1];

        $service->respondToInvitation($assignment1, true);
        $this->assertEquals(ReviewerAssignmentStatus::Accepted, $assignment1->fresh()->status);

        $service->respondToInvitation($assignment2, false, 'Conflict of interest.');
        $this->assertEquals(ReviewerAssignmentStatus::Declined, $assignment2->fresh()->status);
        $this->assertEquals('Conflict of interest.', $assignment2->fresh()->decline_reason);

        // Submit evaluation for reviewer 1
        $consentFile = UploadedFile::fake()->create('consent.pdf', 100, 'application/pdf');
        $protocolFile = UploadedFile::fake()->create('protocol.pdf', 100, 'application/pdf');

        $service->submitEvaluation($assignment1, $consentFile, $protocolFile, 'Approved', 'Protocol is sound.');
        $assignment1->refresh();

        $this->assertEquals(ReviewerAssignmentStatus::Completed, $assignment1->status);
        $this->assertEquals('Approved', $assignment1->recommendation);
        $this->assertEquals('Protocol is sound.', $assignment1->reviewer_notes);
        $this->assertNotNull($assignment1->consent_assessment_path);
        $this->assertNotNull($assignment1->protocol_assessment_path);

        $this->assertFalse($service->checkBothReviewersCompleted($request));
    }

    public function test_routing_service_rejects_invalid_initial_status(): void
    {
        $this->expectException(InvalidArgumentException::class);

        $chair = User::factory()->create(['is_chairperson' => true]);
        $student = User::factory()->create();
        $dept = Department::first();

        $request = EthicsRequest::create([
            'student_id' => $student->id,
            'department_id' => $dept->id,
            'title' => 'Draft Study',
            'status' => RequestStatus::Draft,
            'research_domain' => ResearchDomain::Health,
            'is_local' => true,
        ]);

        $service = app(ReviewRoutingService::class);
        $service->routeToFullReview($request, $chair);
    }

    public function test_unauthorized_user_cannot_route_or_assign(): void
    {
        $student = User::factory()->create();
        $student->assignRole('student');
        $dept = Department::first();

        $request = EthicsRequest::create([
            'student_id' => $student->id,
            'department_id' => $dept->id,
            'title' => 'Unauthorized Access Test',
            'status' => RequestStatus::ReviewRouting,
            'research_domain' => ResearchDomain::Health,
            'is_local' => true,
        ]);

        Livewire::actingAs($student)
            ->test(RequestDetail::class, ['id' => $request->id])
            ->call('routeReviewType', 'full_review')
            ->assertForbidden();
    }

    public function test_student_applicant_cannot_see_appointed_reviewer_identities_or_deliberation(): void
    {
        $student = User::factory()->create(['name' => 'Applicant Student']);
        $student->assignRole('student');
        $dept = Department::first();

        $rev1 = User::factory()->create(['name' => 'Dr. Secret Reviewer', 'email' => 'secret.reviewer@example.test', 'specialty_domains' => ['health']]);
        $rev1->assignRole('committee');

        $request = EthicsRequest::create([
            'student_id' => $student->id,
            'department_id' => $dept->id,
            'title' => 'Confidential Protocol',
            'status' => RequestStatus::UnderReview,
            'research_domain' => ResearchDomain::Health,
            'is_local' => true,
            'deliberation_notes' => 'Confidential committee deliberation notes that student must not see.',
        ]);

        ReviewerAssignment::create([
            'ethics_request_id' => $request->id,
            'user_id' => $rev1->id,
            'status' => ReviewerAssignmentStatus::Invited,
            'invited_at' => now(),
        ]);

        Livewire::actingAs($student)
            ->test(RequestDetail::class, ['id' => $request->id])
            ->assertDontSee('Dr. Secret Reviewer')
            ->assertDontSee('secret.reviewer@example.test')
            ->assertDontSee('Appointed Domain Reviewers')
            ->assertDontSee('Confidential committee deliberation notes that student must not see.');
    }

    public function test_cannot_assign_non_committee_member_or_applicant_as_reviewer(): void
    {
        $chair = User::factory()->create(['is_chairperson' => true]);
        $chair->assignRole('admin');

        $student = User::factory()->create();
        $student->assignRole('student');
        $dept = Department::first();

        $nonCommitteeUser = User::factory()->create();
        $nonCommitteeUser->assignRole('student');

        $rev1 = User::factory()->create(['specialty_domains' => ['health']]);
        $rev1->assignRole('committee');

        $request = EthicsRequest::create([
            'student_id' => $student->id,
            'department_id' => $dept->id,
            'title' => 'Committee Validation Test',
            'status' => RequestStatus::AssigningReviewers,
            'research_domain' => ResearchDomain::Health,
            'is_local' => true,
        ]);

        // Attempt assigning non-committee member
        Livewire::actingAs($chair)
            ->test(RequestDetail::class, ['id' => $request->id])
            ->set('selected_reviewer_1', (string) $nonCommitteeUser->id)
            ->set('selected_reviewer_2', (string) $rev1->id)
            ->call('assignReviewers')
            ->assertHasErrors(['selected_reviewer_1']);

        // Attempt assigning non-committee member as reviewer 2
        Livewire::actingAs($chair)
            ->test(RequestDetail::class, ['id' => $request->id])
            ->set('selected_reviewer_1', (string) $rev1->id)
            ->set('selected_reviewer_2', (string) $nonCommitteeUser->id)
            ->call('assignReviewers')
            ->assertHasErrors(['selected_reviewer_2']);

        // Attempt assigning the student applicant as reviewer 1
        Livewire::actingAs($chair)
            ->test(RequestDetail::class, ['id' => $request->id])
            ->set('selected_reviewer_1', (string) $student->id)
            ->set('selected_reviewer_2', (string) $rev1->id)
            ->call('assignReviewers')
            ->assertHasErrors(['selected_reviewer_1']);

        // Attempt assigning the student applicant as reviewer 2
        Livewire::actingAs($chair)
            ->test(RequestDetail::class, ['id' => $request->id])
            ->set('selected_reviewer_1', (string) $rev1->id)
            ->set('selected_reviewer_2', (string) $student->id)
            ->call('assignReviewers')
            ->assertHasErrors(['selected_reviewer_2']);

        // Service level check throws InvalidArgumentException
        $this->expectException(InvalidArgumentException::class);
        app(ReviewerAssignmentService::class)->assignReviewers($request, [$nonCommitteeUser->id, $rev1->id], $chair);
    }
}
