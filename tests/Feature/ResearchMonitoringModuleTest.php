<?php

namespace Tests\Feature;

use App\Enums\MonitoringStatus;
use App\Enums\RequestStatus;
use App\Livewire\ResearchMonitoring;
use App\Models\Department;
use App\Models\EthicsRequest;
use App\Models\ResearchMonitor;
use App\Models\User;
use Database\Seeders\DepartmentSeeder;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class ResearchMonitoringModuleTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed([RoleSeeder::class, DepartmentSeeder::class]);
    }

    public function test_secretary_can_access_monitoring_and_log_progress_report(): void
    {
        $secretary = User::factory()->create();
        $secretary->assignRole('secretary');

        $student = User::factory()->create();
        $dept = Department::first();
        $request = EthicsRequest::create([
            'student_id' => $student->id,
            'department_id' => $dept->id,
            'title' => 'Longitudinal Cohort Study',
            'status' => RequestStatus::Approved,
            'is_local' => true,
        ]);

        $monitor = ResearchMonitor::create([
            'ethics_request_id' => $request->id,
            'monitoring_status' => MonitoringStatus::Active,
            'approved_at' => now()->subMonths(6),
            'expires_at' => now()->addMonths(6),
        ]);

        Livewire::actingAs($secretary)
            ->test(ResearchMonitoring::class)
            ->assertSee('Longitudinal Cohort Study')
            ->call('recordProgressReport', $monitor->id, 'Mid-term progress report submitted, no protocol deviations.')
            ->assertHasNoErrors();

        $this->assertNotNull($monitor->fresh()->last_report_submitted_at);
        $this->assertStringContainsString('Mid-term progress report', $monitor->fresh()->notes);
    }

    public function test_route_access_requires_authentication_and_view_monitoring_permission(): void
    {
        // Guest redirected to login
        $this->get('/monitoring')
            ->assertRedirect(route('login'));

        // Student without view-monitoring permission gets 403 Forbidden
        $student = User::factory()->create();
        $student->assignRole('student');

        $this->actingAs($student)
            ->get('/monitoring')
            ->assertForbidden();

        // Secretary with view-monitoring permission gets 200 OK
        $secretary = User::factory()->create();
        $secretary->assignRole('secretary');

        $this->actingAs($secretary)
            ->get('/monitoring')
            ->assertOk();
    }

    public function test_can_switch_tabs_and_filter_monitoring_statuses(): void
    {
        $secretary = User::factory()->create();
        $secretary->assignRole('secretary');

        $student = User::factory()->create();
        $dept = Department::first();

        // 1. Active protocol
        $reqActive = EthicsRequest::create([
            'student_id' => $student->id,
            'department_id' => $dept->id,
            'title' => 'Active Clinical Protocol',
            'status' => RequestStatus::Approved,
            'is_local' => true,
        ]);
        $monActive = ResearchMonitor::create([
            'ethics_request_id' => $reqActive->id,
            'monitoring_status' => MonitoringStatus::Active,
            'approved_at' => now()->subMonths(1),
            'expires_at' => now()->addMonths(11),
        ]);

        // 2. Host country pending protocol
        $reqHost = EthicsRequest::create([
            'student_id' => $student->id,
            'department_id' => $dept->id,
            'title' => 'International Field Study In Thailand',
            'status' => RequestStatus::Approved,
            'is_local' => false,
        ]);
        $monHost = ResearchMonitor::create([
            'ethics_request_id' => $reqHost->id,
            'monitoring_status' => MonitoringStatus::HostCountryPending,
            'approved_at' => now()->subMonths(2),
            'expires_at' => now()->addMonths(10),
        ]);

        // 3. Completed protocol
        $reqCompleted = EthicsRequest::create([
            'student_id' => $student->id,
            'department_id' => $dept->id,
            'title' => 'Archived Behavioral Survey',
            'status' => RequestStatus::Approved,
            'is_local' => true,
        ]);
        $monCompleted = ResearchMonitor::create([
            'ethics_request_id' => $reqCompleted->id,
            'monitoring_status' => MonitoringStatus::Completed,
            'approved_at' => now()->subYear(),
            'expires_at' => now(),
        ]);

        // Default 'active' tab should see active protocol, not host country or completed
        $component = Livewire::actingAs($secretary)
            ->test(ResearchMonitoring::class)
            ->assertSee('Active Clinical Protocol')
            ->assertDontSee('International Field Study In Thailand')
            ->assertDontSee('Archived Behavioral Survey');

        // Switch to 'host_country' tab
        $component->call('setTab', 'host_country')
            ->assertSee('International Field Study In Thailand')
            ->assertDontSee('Active Clinical Protocol')
            ->assertDontSee('Archived Behavioral Survey');

        // Switch to 'completed' tab
        $component->call('setTab', 'completed')
            ->assertSee('Archived Behavioral Survey')
            ->assertDontSee('Active Clinical Protocol')
            ->assertDontSee('International Field Study In Thailand');
    }

    public function test_can_search_by_protocol_title_or_control_number(): void
    {
        $secretary = User::factory()->create();
        $secretary->assignRole('secretary');

        $student = User::factory()->create();
        $dept = Department::first();

        $req1 = EthicsRequest::create([
            'student_id' => $student->id,
            'department_id' => $dept->id,
            'title' => 'Genomic Sequencing of Flora',
            'control_number' => 'ERB2026_00100',
            'status' => RequestStatus::Approved,
            'is_local' => true,
        ]);
        ResearchMonitor::create([
            'ethics_request_id' => $req1->id,
            'monitoring_status' => MonitoringStatus::Active,
            'approved_at' => now(),
            'expires_at' => now()->addYear(),
        ]);

        $req2 = EthicsRequest::create([
            'student_id' => $student->id,
            'department_id' => $dept->id,
            'title' => 'Marine Biodiversity Assessment',
            'control_number' => 'ERB2026_00200',
            'status' => RequestStatus::Approved,
            'is_local' => true,
        ]);
        ResearchMonitor::create([
            'ethics_request_id' => $req2->id,
            'monitoring_status' => MonitoringStatus::Active,
            'approved_at' => now(),
            'expires_at' => now()->addYear(),
        ]);

        // Search by title
        Livewire::actingAs($secretary)
            ->test(ResearchMonitoring::class)
            ->set('search', 'Genomic')
            ->assertSee('Genomic Sequencing of Flora')
            ->assertDontSee('Marine Biodiversity Assessment');

        // Search by control number
        Livewire::actingAs($secretary)
            ->test(ResearchMonitoring::class)
            ->set('search', 'ERB2026_00200')
            ->assertSee('Marine Biodiversity Assessment')
            ->assertDontSee('Genomic Sequencing of Flora');
    }

    public function test_can_mark_protocol_completed(): void
    {
        $secretary = User::factory()->create();
        $secretary->assignRole('secretary');

        $student = User::factory()->create();
        $dept = Department::first();

        $req = EthicsRequest::create([
            'student_id' => $student->id,
            'department_id' => $dept->id,
            'title' => 'Finishing Research Protocol',
            'status' => RequestStatus::Approved,
            'is_local' => true,
        ]);
        $monitor = ResearchMonitor::create([
            'ethics_request_id' => $req->id,
            'monitoring_status' => MonitoringStatus::Active,
            'approved_at' => now()->subMonths(10),
            'expires_at' => now()->addMonths(2),
        ]);

        Livewire::actingAs($secretary)
            ->test(ResearchMonitoring::class)
            ->call('markCompleted', $monitor->id)
            ->assertSee('Protocol marked as completed.')
            ->assertHasNoErrors();

        $this->assertEquals(MonitoringStatus::Completed, $monitor->fresh()->monitoring_status);
    }

    public function test_navigation_displays_research_monitoring_link_for_authorized_users(): void
    {
        $secretary = User::factory()->create();
        $secretary->assignRole('secretary');

        $this->actingAs($secretary)
            ->get('/dashboard')
            ->assertSee('Research Monitoring')
            ->assertSee('/monitoring');

        $student = User::factory()->create();
        $student->assignRole('student');

        $this->actingAs($student)
            ->get('/dashboard')
            ->assertDontSee('Research Monitoring');
    }

    public function test_monitoring_table_renders_wire_keys_and_badges_exact_day_expirations_as_expired(): void
    {
        $secretary = User::factory()->create();
        $secretary->assignRole('secretary');

        $student = User::factory()->create();
        $dept = Department::first();

        $req = EthicsRequest::create([
            'student_id' => $student->id,
            'department_id' => $dept->id,
            'title' => 'Exact Day Expired Protocol',
            'status' => RequestStatus::Approved,
            'is_local' => true,
        ]);
        $monitor = ResearchMonitor::create([
            'ethics_request_id' => $req->id,
            'monitoring_status' => MonitoringStatus::Active,
            'approved_at' => now()->subYear(),
            'expires_at' => now()->subMinutes(30),
        ]);

        Livewire::actingAs($secretary)
            ->test(ResearchMonitoring::class)
            ->assertSeeHtml('wire:key="monitor-'.$monitor->id.'"')
            ->assertSee('Expired');
    }
}
