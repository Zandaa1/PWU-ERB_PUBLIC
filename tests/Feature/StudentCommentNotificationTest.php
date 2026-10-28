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
use Livewire\Livewire;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class StudentCommentNotificationTest extends TestCase
{
    use RefreshDatabase;

    public function test_student_comment_triggers_notification_for_committee_members(): void
    {
        Role::firstOrCreate(['name' => 'student', 'guard_name' => 'web']);
        Role::firstOrCreate(['name' => 'committee', 'guard_name' => 'web']);
        Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);

        $student = User::factory()->create();
        $student->assignRole('student');

        $committee = User::factory()->create();
        $committee->assignRole('committee');

        $department = Department::create(['name' => 'School of Arts & Sciences', 'code' => 'SAS']);
        $docType = DocumentType::create(['name' => 'Study Protocol', 'is_required' => true, 'order' => 1]);

        $request = EthicsRequest::create([
            'student_id' => $student->id,
            'title' => 'Test Student Comment Request',
            'department_id' => $department->id,
            'status' => RequestStatus::Submitted,
        ]);

        $reqDoc = RequestDocument::create([
            'ethics_request_id' => $request->id,
            'document_type_id' => $docType->id,
            'status' => DocumentStatus::Pending,
        ]);

        DocumentVersion::create([
            'request_document_id' => $reqDoc->id,
            'version_number' => 1,
            'file_path' => 'documents/test.pdf',
            'file_name' => 'test.pdf',
            'uploaded_by' => $student->id,
        ]);

        $this->actingAs($student);

        Livewire::test(DocumentViewer::class, ['requestId' => $request->id, 'documentId' => $reqDoc->id])
            ->set('newComment', 'Student clarification comment')
            ->call('addComment');

        $this->assertDatabaseHas('notifications', [
            'user_id' => $committee->id,
            'type' => 'student_comment',
            'message' => 'Student '.$student->name.' commented on Study Protocol',
        ]);
    }
}
