<?php

namespace Tests\Unit;

use App\Enums\MonitoringStatus;
use App\Enums\RequestStatus;
use App\Enums\ResearchDomain;
use App\Enums\ReviewerAssignmentStatus;
use App\Enums\ReviewType;
use App\Models\Department;
use App\Models\EthicsRequest;
use App\Models\ResearchMonitor;
use App\Models\ReviewerAssignment;
use App\Models\User;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class WorkflowEnumsAndModelsTest extends TestCase
{
    use RefreshDatabase;

    public function test_workflow_enums_have_correct_labels(): void
    {
        $this->assertEquals('Medical / Health', ResearchDomain::Health->label());
        $this->assertEquals('Social Research', ResearchDomain::Social->label());
        $this->assertEquals('Full Review', ReviewType::FullReview->label());
        $this->assertEquals('Expedited Review', ReviewType::ExpeditedReview->label());
        $this->assertEquals('Screening', RequestStatus::Screening->label());
        $this->assertEquals('Screening Revision', RequestStatus::ScreeningRevision->label());
        $this->assertEquals('Full Review Pending', RequestStatus::FullReviewPending->label());
        $this->assertEquals('Host Country Required', RequestStatus::HostCountryRequired->label());
        $this->assertEquals('Out of Scope', RequestStatus::OutOfScope->label());
        $this->assertEquals('Active', MonitoringStatus::Active->label());
        $this->assertEquals('Invited', ReviewerAssignmentStatus::Invited->label());
    }

    public function test_models_and_relationships_work(): void
    {
        $this->seed(RoleSeeder::class);

        $secretary = User::factory()->create();
        $secretary->assignRole('secretary');
        $this->assertTrue($secretary->hasRole('secretary'));

        $reviewer = User::factory()->create([
            'specialty_domains' => ['health', 'social'],
        ]);
        $reviewer->assignRole('committee');

        $student = User::factory()->create();
        $dept = Department::create(['name' => 'School of Nursing', 'is_active' => true]);

        $request = EthicsRequest::create([
            'student_id' => $student->id,
            'department_id' => $dept->id,
            'title' => 'Test Medical Protocol',
            'status' => RequestStatus::Screening,
            'research_domain' => ResearchDomain::Health,
            'is_local' => true,
            'plagiarism_similarity_score' => 14.50,
            'ai_similarity_score' => 3.20,
            'screened_by' => $secretary->id,
        ]);

        $assignment = ReviewerAssignment::create([
            'ethics_request_id' => $request->id,
            'user_id' => $reviewer->id,
            'status' => ReviewerAssignmentStatus::Invited,
        ]);

        $monitor = ResearchMonitor::create([
            'ethics_request_id' => $request->id,
            'monitoring_status' => MonitoringStatus::Active,
            'approved_at' => now(),
            'expires_at' => now()->addYear(),
        ]);

        $this->assertCount(1, $request->reviewerAssignments);
        $this->assertEquals($reviewer->id, $request->reviewerAssignments->first()->user_id);
        $this->assertNotNull($request->researchMonitor);
        $this->assertEquals(MonitoringStatus::Active, $request->researchMonitor->monitoring_status);
        $this->assertContains('health', $reviewer->fresh()->specialty_domains);
    }
}
