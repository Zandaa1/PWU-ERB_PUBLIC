<?php

namespace Tests\Feature;

use App\Enums\RequestStatus;
use App\Models\Department;
use App\Models\EthicsRequest;
use App\Models\User;
use App\Services\EthicsRequestQueueService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class WaitingUXFeaturesTest extends TestCase
{
    use RefreshDatabase;

    public function test_queue_service_calculates_position_and_sla(): void
    {
        Role::firstOrCreate(['name' => 'student', 'guard_name' => 'web']);
        $student = User::factory()->create(['email' => 'student@example.test', 'is_active' => true]);
        $student->assignRole('student');

        $dept = Department::create(['name' => 'School of Nursing', 'code' => 'SN']);

        $request1 = EthicsRequest::create([
            'student_id' => $student->id,
            'title' => 'First Nursing Study',
            'department_id' => $dept->id,
            'status' => RequestStatus::Submitted,
            'created_at' => now()->subHours(2),
        ]);

        $request2 = EthicsRequest::create([
            'student_id' => $student->id,
            'title' => 'Second Nursing Study',
            'department_id' => $dept->id,
            'status' => RequestStatus::Submitted,
            'created_at' => now(),
        ]);

        $service = app(EthicsRequestQueueService::class);

        $this->assertEquals(1, $service->getQueuePosition($request1));
        $this->assertEquals(2, $service->getQueuePosition($request2));
        $this->assertNotNull($service->getEstimatedDecisionDate($request2));
    }
}
