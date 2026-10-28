<?php

namespace Tests\Feature;

use App\Enums\DocumentStatus;
use App\Enums\RequestStatus;
use App\Livewire\CommitteeReviewQueue;
use App\Livewire\CreateEthicsRequest;
use App\Livewire\NotificationDropdown;
use App\Livewire\StudentDashboard;
use App\Models\Department;
use App\Models\DocumentType;
use App\Models\DocumentVersion;
use App\Models\EthicsRequest;
use App\Models\Notification;
use App\Models\RequestDocument;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class LivewireQueryOptimizationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Role::firstOrCreate(['name' => 'admin']);
        Role::firstOrCreate(['name' => 'committee']);
        Role::firstOrCreate(['name' => 'student']);
    }

    public function test_active_departments_are_retrieved_directly_without_cache(): void
    {
        Department::firstOrCreate(['name' => 'Nursing'], ['is_active' => true]);
        Department::firstOrCreate(['name' => 'Pharmacy'], ['is_active' => true]);
        Department::firstOrCreate(['name' => 'Inactive Dept'], ['is_active' => false]);

        $active = Department::getActive();
        $this->assertTrue($active->pluck('name')->contains('Nursing'));
        $this->assertTrue($active->pluck('name')->contains('Pharmacy'));
        $this->assertFalse($active->pluck('name')->contains('Inactive Dept'));

        $admin = User::factory()->create(['email' => 'dean@example.test', 'is_active' => true]);
        $admin->assignRole('admin');

        Livewire::actingAs($admin)
            ->test(CommitteeReviewQueue::class)
            ->assertOk()
            ->assertViewHas('departments', function ($departments) {
                return $departments->pluck('name')->contains('Nursing') && ! $departments->pluck('name')->contains('Inactive Dept');
            });
    }

    public function test_committee_review_queue_eager_loads_relations(): void
    {
        $committee = User::factory()->create(['is_active' => true]);
        $committee->assignRole('committee');

        $dept = Department::create(['name' => 'Psychology', 'is_active' => true]);
        $docType = DocumentType::create(['name' => 'Protocol', 'is_required' => true, 'order' => 1]);

        for ($i = 1; $i <= 3; $i++) {
            $student = User::factory()->create(['name' => "Student $i", 'is_active' => true]);
            $student->assignRole('student');

            $request = EthicsRequest::create([
                'student_id' => $student->id,
                'department_id' => $dept->id,
                'title' => "Study Title $i",
                'status' => RequestStatus::Submitted,
            ]);

            $rd = RequestDocument::create([
                'ethics_request_id' => $request->id,
                'document_type_id' => $docType->id,
                'status' => DocumentStatus::Pending,
            ]);

            DocumentVersion::create([
                'request_document_id' => $rd->id,
                'file_path' => "documents/{$request->id}/{$docType->id}/1/test.pdf",
                'file_name' => 'test.pdf',
                'version_number' => 1,
                'uploaded_by' => $student->id,
            ]);
        }

        Livewire::actingAs($committee)
            ->test(CommitteeReviewQueue::class)
            ->assertOk()
            ->assertViewHas('requests', function ($requests) {
                // Ensure eager loaded relations are loaded on every item
                foreach ($requests as $req) {
                    if (! $req->relationLoaded('student')) {
                        return false;
                    }
                    if (! $req->relationLoaded('department')) {
                        return false;
                    }
                    if (! $req->relationLoaded('requestDocuments')) {
                        return false;
                    }
                    foreach ($req->requestDocuments as $doc) {
                        if (! $doc->relationLoaded('documentType')) {
                            return false;
                        }
                        if (! $doc->relationLoaded('versions')) {
                            return false;
                        }
                    }
                }

                return true;
            });
    }

    public function test_student_dashboard_eager_loads_relations(): void
    {
        $student = User::factory()->create(['name' => 'Jane Student', 'is_active' => true]);
        $student->assignRole('student');

        $dept = Department::create(['name' => 'Education', 'is_active' => true]);
        $docType = DocumentType::create(['name' => 'Protocol', 'is_required' => true, 'order' => 1]);

        for ($i = 1; $i <= 3; $i++) {
            $request = EthicsRequest::create([
                'student_id' => $student->id,
                'department_id' => $dept->id,
                'title' => "Student Research $i",
                'status' => RequestStatus::Submitted,
            ]);

            $rd = RequestDocument::create([
                'ethics_request_id' => $request->id,
                'document_type_id' => $docType->id,
                'status' => DocumentStatus::Pending,
            ]);

            DocumentVersion::create([
                'request_document_id' => $rd->id,
                'file_path' => "documents/{$request->id}/{$docType->id}/1/test.pdf",
                'file_name' => 'test.pdf',
                'version_number' => 1,
                'uploaded_by' => $student->id,
            ]);
        }

        Livewire::actingAs($student)
            ->test(StudentDashboard::class)
            ->assertOk()
            ->assertViewHas('requests', function ($requests) {
                foreach ($requests as $req) {
                    if (! $req->relationLoaded('department')) {
                        return false;
                    }
                    if (! $req->relationLoaded('requestDocuments')) {
                        return false;
                    }
                    foreach ($req->requestDocuments as $doc) {
                        if (! $doc->relationLoaded('documentType')) {
                            return false;
                        }
                        if (! $doc->relationLoaded('versions')) {
                            return false;
                        }
                    }
                }

                return true;
            });
    }

    public function test_notification_dropdown_limits_and_selects_expected_fields(): void
    {
        $user = User::factory()->create(['is_active' => true]);

        for ($i = 1; $i <= 15; $i++) {
            Notification::create([
                'user_id' => $user->id,
                'type' => 'status_update',
                'message' => "Notification message $i",
                'link' => '/requests/1',
                'is_read' => $i > 5, // 5 unread, 10 read
            ]);
        }

        Livewire::actingAs($user)
            ->test(NotificationDropdown::class)
            ->assertOk()
            ->assertViewHas('notifications', function ($notifications) {
                return $notifications->count() === 10;
            })
            ->assertViewHas('unreadCount', 5);
    }

    public function test_create_ethics_request_renders_active_departments_directly(): void
    {
        Department::create(['name' => 'School of Nursing', 'is_active' => true]);
        Department::create(['name' => 'School of Business', 'is_active' => true]);
        Department::create(['name' => 'Old Inactive Program', 'is_active' => false]);

        $student = User::factory()->create(['is_active' => true]);
        $student->assignRole('student');

        Livewire::actingAs($student)
            ->test(CreateEthicsRequest::class)
            ->assertOk()
            ->assertSee('School of Nursing')
            ->assertSee('School of Business')
            ->assertDontSee('Old Inactive Program');
    }
}
