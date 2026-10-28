<?php

namespace Tests\Feature;

use App\Models\DocumentType;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class TemplateDownloadTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_download_document_template(): void
    {
        Storage::fake('public');
        Role::firstOrCreate(['name' => 'student', 'guard_name' => 'web']);

        $user = User::factory()->create(['email' => 'student@example.test', 'is_active' => true]);
        $user->assignRole('student');

        $file = UploadedFile::fake()->create('template.pdf', 500, 'application/pdf');
        $path = $file->store('templates', 'public');

        $type = DocumentType::create([
            'name' => 'Study Protocol',
            'is_required' => true,
            'is_active' => true,
            'sort_order' => 1,
            'template_file_path' => $path,
        ]);

        $response = $this->actingAs($user)->get("/templates/{$type->id}/download");

        $response->assertStatus(200);
    }
}
