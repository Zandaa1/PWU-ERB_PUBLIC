<?php

namespace Tests\Feature;

use App\Livewire\CreateEthicsRequest;
use App\Models\Department;
use App\Models\DocumentType;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Livewire\Livewire;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class FileExtensionValidationTest extends TestCase
{
    use RefreshDatabase;

    public function test_js_file_upload_is_strictly_rejected(): void
    {
        Role::firstOrCreate(['name' => 'student', 'guard_name' => 'web']);
        $student = User::factory()->create(['email' => 'student@example.test', 'is_active' => true]);
        $student->assignRole('student');

        $dept = Department::create(['name' => 'School of Nursing', 'code' => 'SN']);
        $docType = DocumentType::create(['name' => 'Study Protocol', 'is_required' => true, 'sort_order' => 1]);

        $jsFile = UploadedFile::fake()->create('malicious_script.js', 100, 'application/javascript');

        Livewire::actingAs($student)
            ->test(CreateEthicsRequest::class)
            ->set('title', 'Ethics Review Application Study')
            ->set('department_id', $dept->id)
            ->set('uploads.'.$docType->id, $jsFile)
            ->call('submit')
            ->assertHasErrors(['uploads.'.$docType->id]);
    }

    public function test_image_file_upload_is_accepted(): void
    {
        Role::firstOrCreate(['name' => 'student', 'guard_name' => 'web']);
        Role::firstOrCreate(['name' => 'committee', 'guard_name' => 'web']);
        $student = User::factory()->create(['email' => 'student@example.test', 'is_active' => true]);
        $student->assignRole('student');

        $dept = Department::create(['name' => 'School of Nursing', 'code' => 'SN']);
        $docType = DocumentType::create(['name' => 'Official Receipt of ethics review fee', 'is_required' => true, 'sort_order' => 1]);

        $imageFile = UploadedFile::fake()->image('receipt.png');

        Livewire::actingAs($student)
            ->test(CreateEthicsRequest::class)
            ->set('title', 'Ethics Review Application Study')
            ->set('department_id', $dept->id)
            ->set('uploads.'.$docType->id, $imageFile)
            ->call('submit')
            ->assertHasNoErrors(['uploads.'.$docType->id]);
    }
}
