<?php

namespace Tests\Feature;

use App\Enums\DocumentStatus;
use App\Enums\RequestStatus;
use App\Livewire\Admin\ArchiveQueue;
use App\Livewire\Admin\DepartmentManagement;
use App\Livewire\Admin\UserManagement;
use App\Livewire\CommitteeReviewQueue;
use App\Livewire\CreateEthicsRequest;
use App\Livewire\DocumentViewer;
use App\Livewire\GlobalSearch;
use App\Models\Department;
use App\Models\DocumentType;
use App\Models\EthicsRequest;
use App\Models\RequestDocument;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class LoadingStatesAndButtonUXTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Role::firstOrCreate(['name' => 'student', 'guard_name' => 'web']);
        Role::firstOrCreate(['name' => 'committee', 'guard_name' => 'web']);
        Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
    }

    public function test_layout_renders_global_pwu_maroon_top_loading_bar(): void
    {
        $student = User::factory()->create(['email' => 'student@example.test', 'is_active' => true]);
        $student->assignRole('student');

        $response = $this->actingAs($student)->get('/dashboard');
        $response->assertStatus(200);
        $response->assertSee('Global DEMO Maroon Top Loading Indicator Bar', false);
        $response->assertSee('background-color: #800000', false);
        $response->assertSee('livewire:navigating', false);
        $response->assertSee('Livewire.hook(\'request\'', false);
    }

    public function test_global_search_includes_query_loading_spinner(): void
    {
        $student = User::factory()->create(['email' => 'student@example.test', 'is_active' => true]);
        $student->assignRole('student');

        $dept = Department::create(['name' => 'School of Nursing', 'code' => 'SN']);
        EthicsRequest::create([
            'student_id' => $student->id,
            'title' => 'Nursing Ethics Study',
            'department_id' => $dept->id,
            'status' => RequestStatus::Submitted,
        ]);

        Livewire::actingAs($student)
            ->test(GlobalSearch::class)
            ->assertSeeHtml('wire:loading wire:target="query"')
            ->assertSeeHtml('animate-spin');
    }

    public function test_committee_review_queue_includes_loading_overlays_and_search_spinner(): void
    {
        $reviewer = User::factory()->create(['email' => 'reviewer@example.test', 'is_active' => true]);
        $reviewer->assignRole('committee');

        $dept = Department::create(['name' => 'School of Information Technology', 'code' => 'SIT']);
        $student = User::factory()->create(['email' => 'student2@example.test', 'is_active' => true]);
        $student->assignRole('student');

        EthicsRequest::create([
            'student_id' => $student->id,
            'title' => 'AI in Healthcare Ethics Study',
            'department_id' => $dept->id,
            'status' => RequestStatus::Submitted,
        ]);

        Livewire::actingAs($reviewer)
            ->test(CommitteeReviewQueue::class)
            ->assertSeeHtml('wire:loading wire:target="search,statusFilter,departmentFilter,resubmissionFilter"')
            ->assertSeeHtml('wire:loading.class="opacity-60 pointer-events-none transition-opacity"')
            ->assertSeeHtml('Review Request');
    }

    public function test_create_ethics_request_submission_modal_has_disabled_and_loading_states(): void
    {
        $student = User::factory()->create(['email' => 'student@example.test', 'is_active' => true]);
        $student->assignRole('student');

        $dept = Department::create(['name' => 'School of Business', 'code' => 'SB']);
        DocumentType::create(['name' => 'Protocol Form', 'code' => 'protocol_form', 'is_required' => true, 'sort_order' => 1]);

        Livewire::actingAs($student)
            ->test(CreateEthicsRequest::class)
            ->set('showDataPrivacyModal', true)
            ->assertSeeHtml('wire:target="confirmSubmission"')
            ->assertSeeHtml('wire:loading.attr="disabled"')
            ->assertSeeHtml('Submitting Ethics Request...');
    }

    public function test_document_viewer_has_pdf_stream_loading_placeholder_and_revision_loading_feedback(): void
    {
        $student = User::factory()->create(['email' => 'student@example.test', 'is_active' => true]);
        $student->assignRole('student');

        $dept = Department::create(['name' => 'School of Pharmacy', 'code' => 'SP']);
        $docType = DocumentType::create(['name' => 'Study Protocol', 'code' => 'study_protocol', 'is_required' => true, 'sort_order' => 1]);

        $request = EthicsRequest::create([
            'student_id' => $student->id,
            'title' => 'Pharmacological Study',
            'department_id' => $dept->id,
            'status' => RequestStatus::UnderReview,
        ]);

        $reqDoc = RequestDocument::create([
            'ethics_request_id' => $request->id,
            'document_type_id' => $docType->id,
            'status' => DocumentStatus::UnderReview,
        ]);

        $reqDoc->versions()->create([
            'version_number' => 1,
            'file_path' => 'documents/sample.pdf',
            'file_name' => 'sample.pdf',
            'file_size' => 1024,
            'mime_type' => 'application/pdf',
            'uploaded_by' => $student->id,
        ]);

        Livewire::actingAs($student)
            ->test(DocumentViewer::class, ['requestId' => $request->id, 'documentId' => $reqDoc->id])
            ->assertSeeHtml('Loading Document Stream...')
            ->assertSeeHtml('Fetching PDF...')
            ->set('showRevisionModal', true)
            ->assertSeeHtml('wire:target="submitRevision"')
            ->assertSeeHtml('Uploading & Resubmitting...');
    }

    public function test_admin_user_management_has_guardrails_and_loading_states(): void
    {
        $admin = User::factory()->create(['email' => 'admin@example.test', 'is_active' => true]);
        $admin->assignRole('admin');

        Livewire::actingAs($admin)
            ->test(UserManagement::class)
            ->assertSeeHtml('wire:loading wire:target="search,roleFilter"')
            ->assertSeeHtml('wire:loading.class="opacity-60 pointer-events-none transition-opacity"')
            ->assertSeeHtml('wire:confirm="Are you sure you want to change this user account\'s active status?"');
    }

    public function test_admin_department_management_has_submit_loading_state(): void
    {
        $admin = User::factory()->create(['email' => 'admin@example.test', 'is_active' => true]);
        $admin->assignRole('admin');

        Livewire::actingAs($admin)
            ->test(DepartmentManagement::class)
            ->assertSeeHtml('wire:target="addDepartment"')
            ->assertSeeHtml('wire:loading.attr="disabled"')
            ->assertSeeHtml('Adding...');
    }

    public function test_admin_archive_queue_has_confirm_dialog_and_loading_feedback(): void
    {
        $admin = User::factory()->create(['email' => 'admin@example.test', 'is_active' => true]);
        $admin->assignRole('admin');

        $student = User::factory()->create(['email' => 'student3@example.test', 'is_active' => true]);
        $student->assignRole('student');

        $dept = Department::create(['name' => 'School of Education', 'code' => 'SE']);

        // Create an inactive request flagged for archiving
        EthicsRequest::create([
            'student_id' => $student->id,
            'title' => 'Ancient Education Study',
            'department_id' => $dept->id,
            'status' => RequestStatus::Draft,
            'last_activity_at' => now()->subYears(2),
            'created_at' => now()->subYears(2),
            'updated_at' => now()->subYears(2),
        ]);

        Livewire::actingAs($admin)
            ->test(ArchiveQueue::class)
            ->assertSeeHtml('wire:loading wire:target="search"')
            ->assertSeeHtml('wire:loading.class="opacity-60 pointer-events-none transition-opacity"')
            ->assertSeeHtml('wire:confirm="Are you sure you want to permanently archive this ethics request?"');
    }

    public function test_strict_zero_emoji_mandate_across_all_blade_views(): void
    {
        $viewsDir = resource_path('views');
        $files = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($viewsDir));

        $emojiPattern = '/[\x{1F600}-\x{1F64F}\x{1F300}-\x{1F5FF}\x{1F680}-\x{1F6FF}\x{1F700}-\x{1F77F}\x{1F780}-\x{1F7FF}\x{1F800}-\x{1F8FF}\x{1F900}-\x{1F9FF}\x{1FA00}-\x{1FA6F}\x{1FA70}-\x{1FAFF}\x{2600}-\x{26FF}\x{2700}-\x{27BF}]/u';

        foreach ($files as $file) {
            if ($file->isFile() && str_ends_with($file->getFilename(), '.blade.php')) {
                // Ignore vendor templates if any
                if (str_contains($file->getPathname(), 'vendor')) {
                    continue;
                }

                $content = file_get_contents($file->getPathname());
                $this->assertEquals(
                    0,
                    preg_match($emojiPattern, $content),
                    'Found emoji in blade file: '.$file->getPathname()
                );
            }
        }
    }
}
