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

class DocumentApprovalModalTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Role::firstOrCreate(['name' => 'committee']);
        Role::firstOrCreate(['name' => 'admin']);
        Role::firstOrCreate(['name' => 'student']);
    }

    public function test_document_viewer_prompts_approval_modal(): void
    {
        $committee = User::factory()->create(['is_active' => true]);
        $committee->assignRole('committee');

        $student = User::factory()->create(['is_active' => true]);
        $student->assignRole('student');

        $dept = Department::create(['name' => 'School of Nursing', 'is_active' => true]);
        $docType = DocumentType::create(['name' => 'Study Protocol', 'is_required' => true, 'order' => 1]);

        $request = EthicsRequest::create([
            'student_id' => $student->id,
            'department_id' => $dept->id,
            'title' => 'Nursing Ethics Study',
            'status' => RequestStatus::Submitted,
        ]);

        $requestDoc = RequestDocument::create([
            'ethics_request_id' => $request->id,
            'document_type_id' => $docType->id,
            'status' => DocumentStatus::Pending,
        ]);

        $version = DocumentVersion::create([
            'request_document_id' => $requestDoc->id,
            'version_number' => 1,
            'file_path' => "documents/{$request->id}/{$docType->id}/1/protocol.pdf",
            'file_name' => 'protocol.pdf',
            'uploaded_by' => $student->id,
        ]);

        Livewire::actingAs($committee)
            ->test(DocumentViewer::class, ['requestId' => $request->id, 'documentId' => $requestDoc->id])
            ->assertSet('showApproveConfirmationModal', false)
            ->call('promptApproveDocument')
            ->assertSet('showApproveConfirmationModal', true)
            ->assertSee('Confirm Document Approval')
            ->assertSee('Are you sure you want to approve this document?')
            ->call('confirmApproveDocument')
            ->assertSet('showApproveConfirmationModal', false);

        $this->assertEquals(DocumentStatus::Approved, $requestDoc->fresh()->status);
    }

    public function test_views_do_not_use_native_javascript_alerts_or_confirms(): void
    {
        $docViewerBlade = file_get_contents(resource_path('views/livewire/document-viewer.blade.php'));
        $requestDetailBlade = file_get_contents(resource_path('views/livewire/request-detail.blade.php'));

        $this->assertStringNotContainsString('wire:confirm', $docViewerBlade);
        $this->assertStringNotContainsString('alert(', $requestDetailBlade);
        $this->assertStringNotContainsString('confirm(', $requestDetailBlade);
    }
}
