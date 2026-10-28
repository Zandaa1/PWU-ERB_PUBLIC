<?php

namespace Tests\Feature;

use App\Livewire\CreateEthicsRequest;
use App\Models\Department;
use App\Models\DocumentType;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class SubmissionPopupTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Role::firstOrCreate(['name' => 'student']);
        Role::firstOrCreate(['name' => 'committee']);
        Role::firstOrCreate(['name' => 'admin']);
    }

    public function test_validation_error_popup_modal_opens_when_submit_fails(): void
    {
        $student = User::factory()->create(['is_active' => true]);
        $student->assignRole('student');

        $dept = Department::create(['name' => 'School of Nursing', 'is_active' => true]);
        DocumentType::create(['name' => 'Study Protocol', 'is_required' => true, 'order' => 1]);

        // Attempt submit without filling required fields
        Livewire::actingAs($student)
            ->test(CreateEthicsRequest::class)
            ->call('openDataPrivacyModal')
            ->assertHasErrors(['title', 'department_id'])
            ->assertSet('showValidationErrorModal', true)
            ->assertSet('showDataPrivacyModal', false)
            ->assertSee('Submission Incomplete')
            ->assertSee('Please address the required items below');
    }

    public function test_success_popup_modal_opens_when_submit_passes(): void
    {
        Storage::fake('local');

        $student = User::factory()->create(['name' => 'Jane Doe', 'is_active' => true]);
        $student->assignRole('student');

        $dept = Department::firstOrCreate(['name' => 'School of Pharmacy'], ['is_active' => true]);

        $test = Livewire::actingAs($student)
            ->test(CreateEthicsRequest::class)
            ->set('title', 'Comparative Study on Healthcare Logistics in Urban Centers')
            ->set('department_id', $dept->id)
            ->set('research_domain', 'health');

        $requiredTypes = DocumentType::where('is_active', true)->where('is_required', true)->get();
        foreach ($requiredTypes as $type) {
            $test->set('uploads.'.$type->id, UploadedFile::fake()->create("doc_{$type->id}.pdf", 500, 'application/pdf'));
        }

        $test->call('openDataPrivacyModal')
            ->assertHasNoErrors()
            ->assertSet('showValidationErrorModal', false)
            ->assertSet('showDataPrivacyModal', true)
            ->set('privacyConsentAccepted', true)
            ->call('confirmSubmission')
            ->assertHasNoErrors()
            ->assertSet('showSuccessModal', true)
            ->assertSee('Submitted Successfully!')
            ->assertSee('Comparative Study on Healthcare Logistics in Urban Centers')
            ->assertSee('Go to Dashboard');
    }

    public function test_global_notification_toast_container_exists_in_app_layout(): void
    {
        $user = User::factory()->create(['is_active' => true]);
        $user->assignRole('student');

        $response = $this->actingAs($user)->get('/dashboard');

        $response->assertStatus(200);
        $response->assertSee('Global Popups & Toast Notifications Container', false);
    }
}
