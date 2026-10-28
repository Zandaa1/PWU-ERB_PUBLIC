<?php

namespace Tests\Feature;

use App\Enums\RequestStatus;
use App\Enums\ResearchDomain;
use App\Livewire\CreateEthicsRequest;
use App\Livewire\RequestDetail;
use App\Models\Department;
use App\Models\DocumentType;
use App\Models\EthicsRequest;
use App\Models\User;
use App\Services\ScreeningService;
use Database\Seeders\DepartmentSeeder;
use Database\Seeders\DocumentTypeSeeder;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Livewire\Livewire;
use Tests\TestCase;

class ScreeningWorkflowTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed([RoleSeeder::class, DepartmentSeeder::class, DocumentTypeSeeder::class]);
    }

    public function test_student_submits_with_research_domain_and_locality(): void
    {
        $student = User::factory()->create();
        $student->assignRole('student');
        $dept = Department::where('is_active', true)->first();

        $component = Livewire::actingAs($student)
            ->test(CreateEthicsRequest::class)
            ->set('title', 'Study on Adolescent Mental Health')
            ->set('department_id', $dept->id)
            ->set('research_domain', 'health')
            ->set('is_local', true)
            ->set('privacyConsentAccepted', true);

        foreach (DocumentType::where('is_required', true)->get() as $docType) {
            $prop = 'doc_'.$docType->id;
            $component->set($prop, UploadedFile::fake()->create('doc_'.$docType->id.'.pdf', 200, 'application/pdf'));
        }

        $component->call('submit')
            ->assertHasNoErrors()
            ->assertSet('showSuccessModal', true);

        $request = EthicsRequest::latest()->first();
        $this->assertEquals(RequestStatus::Submitted, $request->status);
        $this->assertEquals(ResearchDomain::Health, $request->research_domain);
        $this->assertTrue($request->is_local);
    }

    public function test_secretary_can_return_submission_for_screening_revision(): void
    {
        $secretary = User::factory()->create();
        $secretary->assignRole('secretary');

        $student = User::factory()->create();
        $dept = Department::where('is_active', true)->first();
        $request = EthicsRequest::create([
            'student_id' => $student->id,
            'department_id' => $dept->id,
            'title' => 'Incomplete Submission',
            'status' => RequestStatus::Submitted,
            'research_domain' => ResearchDomain::Social,
            'is_local' => true,
        ]);

        Livewire::actingAs($secretary)
            ->test(RequestDetail::class, ['id' => $request->id])
            ->set('screening_notes', 'Document 2 is missing signature.')
            ->call('returnScreeningRevision')
            ->assertHasNoErrors();

        $this->assertEquals(RequestStatus::ScreeningRevision, $request->fresh()->status);
        $this->assertEquals('Document 2 is missing signature.', $request->fresh()->screening_notes);
    }

    public function test_secretary_can_pass_screening_with_plagiarism_scores(): void
    {
        $secretary = User::factory()->create();
        $secretary->assignRole('secretary');

        $student = User::factory()->create();
        $dept = Department::where('is_active', true)->first();
        $request = EthicsRequest::create([
            'student_id' => $student->id,
            'department_id' => $dept->id,
            'title' => 'Verified Submission',
            'status' => RequestStatus::Submitted,
            'research_domain' => ResearchDomain::Health,
            'is_local' => true,
        ]);

        Livewire::actingAs($secretary)
            ->test(RequestDetail::class, ['id' => $request->id])
            ->set('plagiarism_similarity_score', '11.50')
            ->set('ai_similarity_score', '2.00')
            ->set('screening_notes', 'All documents intact and Turnitin within thresholds.')
            ->call('passScreening')
            ->assertHasNoErrors();

        $fresh = $request->fresh();
        $this->assertEquals(RequestStatus::ReviewRouting, $fresh->status);
        $this->assertEquals(11.50, (float) $fresh->plagiarism_similarity_score);
        $this->assertEquals(2.00, (float) $fresh->ai_similarity_score);
        $this->assertNotNull($fresh->plagiarism_verified_at);
        $this->assertEquals($secretary->id, $fresh->screened_by);
    }

    public function test_screening_panel_visible_to_secretary_on_submitted_request(): void
    {
        $secretary = User::factory()->create();
        $secretary->assignRole('secretary');

        $student = User::factory()->create();
        $dept = Department::where('is_active', true)->first();
        $request = EthicsRequest::create([
            'student_id' => $student->id,
            'department_id' => $dept->id,
            'title' => 'Pending Verification Submission',
            'status' => RequestStatus::Submitted,
            'research_domain' => ResearchDomain::Health,
            'is_local' => true,
        ]);

        Livewire::actingAs($secretary)
            ->test(RequestDetail::class, ['id' => $request->id])
            ->assertSee('Secretary Screening &amp; Intake Verification', false)
            ->assertSee('Plagiarism Similarity Score')
            ->assertSee('AI Similarity Score')
            ->assertSee('Screening Notes / Justification Remarks')
            ->assertSee('Return for Screening Revision')
            ->assertSee('Approve Screening &amp; Forward to Chair', false);
    }

    public function test_screening_panel_hidden_from_student(): void
    {
        $student = User::factory()->create();
        $student->assignRole('student');
        $dept = Department::where('is_active', true)->first();
        $request = EthicsRequest::create([
            'student_id' => $student->id,
            'department_id' => $dept->id,
            'title' => 'My Student Submission',
            'status' => RequestStatus::Submitted,
            'research_domain' => ResearchDomain::Health,
            'is_local' => true,
        ]);

        $this->actingAs($student);

        Livewire::actingAs($student)
            ->test(RequestDetail::class, ['id' => $request->id])
            ->assertDontSee('Secretary Screening & Intake Verification', false)
            ->assertDontSee('Approve Screening & Forward to Chair', false);
    }

    public function test_student_cannot_call_screening_actions(): void
    {
        $student = User::factory()->create();
        $student->assignRole('student');
        $dept = Department::where('is_active', true)->first();
        $request = EthicsRequest::create([
            'student_id' => $student->id,
            'department_id' => $dept->id,
            'title' => 'Restricted Action Submission',
            'status' => RequestStatus::Submitted,
            'research_domain' => ResearchDomain::Health,
            'is_local' => true,
        ]);

        $this->actingAs($student);

        Livewire::actingAs($student)
            ->test(RequestDetail::class, ['id' => $request->id])
            ->call('passScreening')
            ->assertStatus(403);

        Livewire::actingAs($student)
            ->test(RequestDetail::class, ['id' => $request->id])
            ->call('returnScreeningRevision')
            ->assertStatus(403);
    }

    public function test_pass_screening_requires_similarity_scores(): void
    {
        $secretary = User::factory()->create();
        $secretary->assignRole('secretary');

        $student = User::factory()->create();
        $dept = Department::where('is_active', true)->first();
        $request = EthicsRequest::create([
            'student_id' => $student->id,
            'department_id' => $dept->id,
            'title' => 'Missing Scores Submission',
            'status' => RequestStatus::Submitted,
            'research_domain' => ResearchDomain::Health,
            'is_local' => true,
        ]);

        Livewire::actingAs($secretary)
            ->test(RequestDetail::class, ['id' => $request->id])
            ->call('passScreening')
            ->assertHasErrors(['plagiarism_similarity_score', 'ai_similarity_score']);
    }

    public function test_return_screening_revision_requires_notes(): void
    {
        $secretary = User::factory()->create();
        $secretary->assignRole('secretary');

        $student = User::factory()->create();
        $dept = Department::where('is_active', true)->first();
        $request = EthicsRequest::create([
            'student_id' => $student->id,
            'department_id' => $dept->id,
            'title' => 'Missing Notes Submission',
            'status' => RequestStatus::Submitted,
            'research_domain' => ResearchDomain::Health,
            'is_local' => true,
        ]);

        Livewire::actingAs($secretary)
            ->test(RequestDetail::class, ['id' => $request->id])
            ->call('returnScreeningRevision')
            ->assertHasErrors(['screening_notes']);
    }

    public function test_cannot_screen_request_in_invalid_status(): void
    {
        $secretary = User::factory()->create();
        $secretary->assignRole('secretary');

        $student = User::factory()->create();
        $dept = Department::where('is_active', true)->first();
        $request = EthicsRequest::create([
            'student_id' => $student->id,
            'department_id' => $dept->id,
            'title' => 'Under Review Submission',
            'status' => RequestStatus::UnderReview,
            'research_domain' => ResearchDomain::Health,
            'is_local' => true,
        ]);

        Livewire::actingAs($secretary)
            ->test(RequestDetail::class, ['id' => $request->id])
            ->set('plagiarism_similarity_score', '10.00')
            ->set('ai_similarity_score', '2.00')
            ->call('passScreening')
            ->assertSee('This request is not in a screenable state.');

        Livewire::actingAs($secretary)
            ->test(RequestDetail::class, ['id' => $request->id])
            ->set('screening_notes', 'Needs major revisions.')
            ->call('returnScreeningRevision')
            ->assertSee('This request is not in a screenable state.');
    }

    public function test_screening_service_enforces_status_guards(): void
    {
        $secretary = User::factory()->create();
        $secretary->assignRole('secretary');

        $student = User::factory()->create();
        $dept = Department::where('is_active', true)->first();
        $request = EthicsRequest::create([
            'student_id' => $student->id,
            'department_id' => $dept->id,
            'title' => 'Approved Submission',
            'status' => RequestStatus::Approved,
            'research_domain' => ResearchDomain::Health,
            'is_local' => true,
        ]);

        $this->expectException(\InvalidArgumentException::class);
        app(ScreeningService::class)->passScreening($request, $secretary, 10.0, 2.0);
    }
}
