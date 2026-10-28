<?php

namespace Tests\Feature;

use App\Enums\MonitoringStatus;
use App\Enums\RequestStatus;
use App\Enums\ResearchDomain;
use App\Livewire\RequestDetail;
use App\Models\ControlNumberCounter;
use App\Models\Department;
use App\Models\EthicsRequest;
use App\Models\User;
use Database\Seeders\DepartmentSeeder;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use Tests\TestCase;

class ChairFinalDecisionWorkflowTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed([RoleSeeder::class, DepartmentSeeder::class]);
        Storage::fake('private');
        ControlNumberCounter::firstOrCreate(['scope' => 'ERB'.date('Y')], ['last_sequence' => 0]);
    }

    public function test_chair_approves_and_generates_certificate_and_monitoring(): void
    {
        $chair = User::factory()->create(['name' => 'Dean Chair', 'is_chairperson' => true]);
        $chair->assignRole('admin');

        $student = User::factory()->create();
        $dept = Department::first();
        $request = EthicsRequest::create([
            'student_id' => $student->id,
            'department_id' => $dept->id,
            'title' => 'Approved Pediatric Protocol',
            'status' => RequestStatus::UnderReview,
            'research_domain' => ResearchDomain::Health,
            'is_local' => true,
        ]);

        $mockSig = 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mNk+M9QDwADhgGAWjR9awAAAABJRU5ErkJggg==';

        Livewire::actingAs($chair)
            ->test(RequestDetail::class, ['id' => $request->id])
            ->call('approveAndIssueCertificate', $mockSig)
            ->assertHasNoErrors();

        $fresh = $request->fresh();
        $this->assertEquals(RequestStatus::Approved, $fresh->status);
        $this->assertNotNull($fresh->control_number);
        $this->assertNotNull($fresh->certificate_path);
        $this->assertNotNull($fresh->researchMonitor);
        $this->assertEquals(MonitoringStatus::Active, $fresh->researchMonitor->monitoring_status);
    }

    public function test_chair_disapproves_local_protocol_routes_to_revisions(): void
    {
        $chair = User::factory()->create(['is_chairperson' => true]);
        $chair->assignRole('admin');

        $student = User::factory()->create();
        $dept = Department::first();
        $request = EthicsRequest::create([
            'student_id' => $student->id,
            'department_id' => $dept->id,
            'title' => 'Local Protocol with Issues',
            'status' => RequestStatus::UnderReview,
            'research_domain' => ResearchDomain::Social,
            'is_local' => true,
        ]);

        Livewire::actingAs($chair)
            ->test(RequestDetail::class, ['id' => $request->id])
            ->set('decision_revision_notes', 'Revise consent form and clarify risk mitigation.')
            ->call('requestLocalRevisions')
            ->assertHasNoErrors();

        $fresh = $request->fresh();
        $this->assertEquals(RequestStatus::RevisionsRequested, $fresh->status);
    }

    public function test_chair_disapproves_non_local_protocol_issues_host_country_certification(): void
    {
        $chair = User::factory()->create(['name' => 'Dean Chair', 'is_chairperson' => true]);
        $chair->assignRole('admin');

        $student = User::factory()->create();
        $dept = Department::first();
        $request = EthicsRequest::create([
            'student_id' => $student->id,
            'department_id' => $dept->id,
            'title' => 'Study Conducted in Vietnam',
            'status' => RequestStatus::UnderReview,
            'research_domain' => ResearchDomain::Health,
            'is_local' => false,
            'host_country' => 'Vietnam',
        ]);

        $mockSig = 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mNk+M9QDwADhgGAWjR9awAAAABJRU5ErkJggg==';

        Livewire::actingAs($chair)
            ->test(RequestDetail::class, ['id' => $request->id])
            ->call('issueHostCountryRequirement', $mockSig)
            ->assertHasNoErrors();

        $fresh = $request->fresh();
        $this->assertEquals(RequestStatus::HostCountryRequired, $fresh->status);
        $this->assertNotNull($fresh->host_country_cert_path);
        $this->assertNotNull($fresh->researchMonitor);
        $this->assertEquals(MonitoringStatus::HostCountryPending, $fresh->researchMonitor->monitoring_status);
    }

    public function test_host_country_certificate_can_be_downloaded_by_authorized_user(): void
    {
        $student = User::factory()->create();
        $dept = Department::first();
        $request = EthicsRequest::create([
            'student_id' => $student->id,
            'department_id' => $dept->id,
            'title' => 'International Research Project',
            'status' => RequestStatus::HostCountryRequired,
            'control_number' => 'ERB2026_00001',
            'host_country_cert_path' => 'certificates/HOST_REQ_ERB2026_00001.pdf',
        ]);

        Storage::disk('private')->put('certificates/HOST_REQ_ERB2026_00001.pdf', '%PDF-1.4 Mock PDF Content');

        $response = $this->actingAs($student)->get("/requests/{$request->id}/host-country-certificate");
        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'application/pdf');

        $unauthorizedStudent = User::factory()->create();
        $unauthorizedStudent->assignRole('student');
        $unauthResponse = $this->actingAs($unauthorizedStudent)->get("/requests/{$request->id}/host-country-certificate");
        $unauthResponse->assertStatus(403);
    }

    public function test_unauthorized_user_cannot_execute_chairperson_decision_actions(): void
    {
        $student = User::factory()->create();
        $student->assignRole('student');

        $dept = Department::first();
        $request = EthicsRequest::create([
            'student_id' => $student->id,
            'department_id' => $dept->id,
            'title' => 'Protected Protocol',
            'status' => RequestStatus::UnderReview,
            'research_domain' => ResearchDomain::Health,
            'is_local' => true,
        ]);

        $mockSig = 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mNk+M9QDwADhgGAWjR9awAAAABJRU5ErkJggg==';

        Livewire::actingAs($student)
            ->test(RequestDetail::class, ['id' => $request->id])
            ->call('approveAndIssueCertificate', $mockSig)
            ->assertForbidden();

        Livewire::actingAs($student)
            ->test(RequestDetail::class, ['id' => $request->id])
            ->call('issueHostCountryRequirement', $mockSig)
            ->assertForbidden();

        Livewire::actingAs($student)
            ->test(RequestDetail::class, ['id' => $request->id])
            ->set('decision_revision_notes', 'Unauthorized notes')
            ->call('requestLocalRevisions')
            ->assertForbidden();
    }

    public function test_decision_revision_notes_validation_requires_content(): void
    {
        $chair = User::factory()->create(['is_chairperson' => true]);
        $chair->assignRole('admin');

        $student = User::factory()->create();
        $dept = Department::first();
        $request = EthicsRequest::create([
            'student_id' => $student->id,
            'department_id' => $dept->id,
            'title' => 'Protocol Needing Revisions',
            'status' => RequestStatus::UnderReview,
            'research_domain' => ResearchDomain::Social,
            'is_local' => true,
        ]);

        Livewire::actingAs($chair)
            ->test(RequestDetail::class, ['id' => $request->id])
            ->set('decision_revision_notes', '')
            ->call('requestLocalRevisions')
            ->assertHasErrors(['decision_revision_notes' => 'required']);
    }

    public function test_chair_final_decision_panel_renders_conditional_options_for_local_and_international(): void
    {
        $chair = User::factory()->create(['is_chairperson' => true]);
        $chair->assignRole('admin');

        $student = User::factory()->create();
        $dept = Department::first();

        // Local protocol
        $localRequest = EthicsRequest::create([
            'student_id' => $student->id,
            'department_id' => $dept->id,
            'title' => 'Local Ethics Protocol',
            'status' => RequestStatus::UnderReview,
            'research_domain' => ResearchDomain::Health,
            'is_local' => true,
        ]);

        Livewire::actingAs($chair)
            ->test(RequestDetail::class, ['id' => $localRequest->id])
            ->assertSee('Chairperson Final Decision & Protocol Determination')
            ->assertSee('Approve & Issue Certificate')
            ->assertSee('Request Revisions')
            ->assertDontSee('Issue Requirement to Review in Host Countries');

        // International protocol
        $intlRequest = EthicsRequest::create([
            'student_id' => $student->id,
            'department_id' => $dept->id,
            'title' => 'International Ethics Protocol',
            'status' => RequestStatus::UnderReview,
            'research_domain' => ResearchDomain::Health,
            'is_local' => false,
            'host_country' => 'Indonesia',
        ]);

        Livewire::actingAs($chair)
            ->test(RequestDetail::class, ['id' => $intlRequest->id])
            ->assertSee('Chairperson Final Decision & Protocol Determination')
            ->assertSee('Approve & Issue Certificate')
            ->assertSee('Issue Requirement to Review in Host Countries')
            ->assertDontSee('Request Revisions');
    }

    public function test_chairperson_or_committee_cannot_approve_or_screen_own_thesis_request(): void
    {
        $mockSig = 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mNk+M9QDwADhgGAWjR9awAAAABJRU5ErkJggg==';
        $dept = Department::first();

        // 1. Chairperson submitting their own thesis
        $chairApplicant = User::factory()->create([
            'name' => 'Chairperson Researcher',
            'is_chairperson' => true,
        ]);
        $chairApplicant->assignRole('admin');

        $ownRequest = EthicsRequest::create([
            'student_id' => $chairApplicant->id,
            'department_id' => $dept->id,
            'title' => 'Chair Own Protocol Study',
            'status' => RequestStatus::UnderReview,
            'research_domain' => ResearchDomain::Health,
            'is_local' => true,
        ]);

        // Assert 403 Forbidden for chair decisions on own protocol
        Livewire::actingAs($chairApplicant)
            ->test(RequestDetail::class, ['id' => $ownRequest->id])
            ->call('approveAndIssueCertificate', $mockSig)
            ->assertForbidden();

        Livewire::actingAs($chairApplicant)
            ->test(RequestDetail::class, ['id' => $ownRequest->id])
            ->call('issueHostCountryRequirement', $mockSig)
            ->assertForbidden();

        Livewire::actingAs($chairApplicant)
            ->test(RequestDetail::class, ['id' => $ownRequest->id])
            ->set('decision_revision_notes', 'Self revisions')
            ->call('requestLocalRevisions')
            ->assertForbidden();

        Livewire::actingAs($chairApplicant)
            ->test(RequestDetail::class, ['id' => $ownRequest->id])
            ->call('approveWithSignature', $mockSig)
            ->assertForbidden();

        // Own protocol in screening state cannot be screened by the applicant
        $ownScreeningRequest = EthicsRequest::create([
            'student_id' => $chairApplicant->id,
            'department_id' => $dept->id,
            'title' => 'Chair Own Screening Protocol',
            'status' => RequestStatus::Submitted,
            'research_domain' => ResearchDomain::Health,
            'is_local' => true,
        ]);

        Livewire::actingAs($chairApplicant)
            ->test(RequestDetail::class, ['id' => $ownScreeningRequest->id])
            ->set('plagiarism_similarity_score', '10')
            ->set('ai_similarity_score', '5')
            ->call('passScreening')
            ->assertForbidden();

        Livewire::actingAs($chairApplicant)
            ->test(RequestDetail::class, ['id' => $ownScreeningRequest->id])
            ->set('screening_notes', 'Return to self')
            ->call('returnScreeningRevision')
            ->assertForbidden();

        // Ensure panels are hidden from applicant even if they hold chair/admin roles
        Livewire::actingAs($chairApplicant)
            ->test(RequestDetail::class, ['id' => $ownRequest->id])
            ->assertDontSee('Chairperson Final Decision & Protocol Determination')
            ->assertDontSee('Secretary Screening & Intake Verification')
            ->assertDontSee('Approve Ethics Request & Sign Certificate');

        // 2. Committee member submitting their own thesis
        $committeeApplicant = User::factory()->create([
            'name' => 'Committee Member Researcher',
        ]);
        $committeeApplicant->assignRole('committee');

        $committeeRequest = EthicsRequest::create([
            'student_id' => $committeeApplicant->id,
            'department_id' => $dept->id,
            'title' => 'Committee Own Protocol Study',
            'status' => RequestStatus::UnderReview,
            'research_domain' => ResearchDomain::Social,
            'is_local' => true,
        ]);

        Livewire::actingAs($committeeApplicant)
            ->test(RequestDetail::class, ['id' => $committeeRequest->id])
            ->call('approveWithSignature', $mockSig)
            ->assertForbidden();

        Livewire::actingAs($committeeApplicant)
            ->test(RequestDetail::class, ['id' => $committeeRequest->id])
            ->call('openDisapproveModal')
            ->assertForbidden();
    }

    public function test_chair_actions_reject_non_actionable_or_already_approved_requests(): void
    {
        $mockSig = 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mNk+M9QDwADhgGAWjR9awAAAABJRU5ErkJggg==';
        $chair = User::factory()->create(['is_chairperson' => true]);
        $chair->assignRole('admin');

        $student = User::factory()->create();
        $dept = Department::first();

        // 1. Request already Approved
        $approvedRequest = EthicsRequest::create([
            'student_id' => $student->id,
            'department_id' => $dept->id,
            'title' => 'Already Approved Protocol',
            'status' => RequestStatus::Approved,
            'control_number' => 'ERB2026_00001',
            'research_domain' => ResearchDomain::Health,
            'is_local' => true,
        ]);

        Livewire::actingAs($chair)
            ->test(RequestDetail::class, ['id' => $approvedRequest->id])
            ->call('approveAndIssueCertificate', $mockSig)
            ->assertSee('Protocol is not in an actionable review status for approval.');

        Livewire::actingAs($chair)
            ->test(RequestDetail::class, ['id' => $approvedRequest->id])
            ->call('issueHostCountryRequirement', $mockSig)
            ->assertSee('Protocol is not in an actionable review status for host country requirement.');

        Livewire::actingAs($chair)
            ->test(RequestDetail::class, ['id' => $approvedRequest->id])
            ->set('decision_revision_notes', 'Revise already approved')
            ->call('requestLocalRevisions')
            ->assertSee('Protocol is not in an actionable review status for requesting revisions.');

        $this->assertEquals('ERB2026_00001', $approvedRequest->fresh()->control_number);

        // 2. Request Archived
        $archivedRequest = EthicsRequest::create([
            'student_id' => $student->id,
            'department_id' => $dept->id,
            'title' => 'Archived Protocol',
            'status' => RequestStatus::Archived,
            'research_domain' => ResearchDomain::Social,
            'is_local' => true,
        ]);

        Livewire::actingAs($chair)
            ->test(RequestDetail::class, ['id' => $archivedRequest->id])
            ->call('approveAndIssueCertificate', $mockSig)
            ->assertSee('Protocol is not in an actionable review status for approval.');

        $this->assertEquals(RequestStatus::Archived, $archivedRequest->fresh()->status);
    }
}
