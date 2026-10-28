<?php

namespace Tests\Feature;

use App\Enums\DocumentStatus;
use App\Enums\RequestStatus;
use App\Livewire\DocumentViewer;
use App\Models\Department;
use App\Models\DocumentType;
use App\Models\DocumentVersion;
use App\Models\EthicsRequest;
use App\Models\RequestDocument;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class UploadRevisionTest extends TestCase
{
    use RefreshDatabase;

    public function test_student_can_upload_revised_document_version(): void
    {
        Storage::fake('local');
        Role::firstOrCreate(['name' => 'student', 'guard_name' => 'web']);

        $student = User::factory()->create(['email' => 'student@example.test', 'is_active' => true]);
        $student->assignRole('student');

        $dept = Department::create(['name' => 'School of Nursing', 'code' => 'SN']);
        $docType = DocumentType::create(['name' => 'Study Protocol', 'is_required' => true, 'sort_order' => 1]);

        $request = EthicsRequest::create([
            'student_id' => $student->id,
            'title' => 'Nursing Ethics Study',
            'department_id' => $dept->id,
            'status' => RequestStatus::RevisionsRequested,
        ]);

        $doc = RequestDocument::create([
            'ethics_request_id' => $request->id,
            'document_type_id' => $docType->id,
            'status' => DocumentStatus::RevisionsRequested,
        ]);

        DocumentVersion::create([
            'request_document_id' => $doc->id,
            'file_path' => 'documents/v1.pdf',
            'version_number' => 1,
            'uploaded_by' => $student->id,
        ]);

        $newFile = UploadedFile::fake()->create('revised_protocol_v2.pdf', 1024, 'application/pdf');

        Livewire::actingAs($student)
            ->test(DocumentViewer::class, ['requestId' => $request->id, 'documentId' => $doc->id])
            ->set('newVersionFile', $newFile)
            ->set('changeNotes', 'Updated section 3 based on reviewer feedback.')
            ->call('submitRevision')
            ->assertHasNoErrors()
            ->assertSet('showRevisionModal', false);

        $doc->refresh();
        $this->assertEquals(DocumentStatus::Pending, $doc->status);
        $this->assertCount(2, $doc->versions);
        $this->assertEquals(2, $doc->latestVersion->version_number);
        $this->assertEquals('Updated section 3 based on reviewer feedback.', $doc->latestVersion->change_notes);
        $this->assertEquals(RequestStatus::UnderReview, $request->fresh()->status);
        $this->assertDatabaseHas('status_logs', [
            'ethics_request_id' => $request->id,
            'new_status' => RequestStatus::UnderReview->value,
        ]);
    }
}
