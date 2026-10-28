<?php

namespace Tests\Unit;

use App\Enums\DocumentStatus;
use App\Enums\RequestStatus;
use App\Models\Department;
use App\Models\DocumentType;
use App\Models\DocumentVersion;
use App\Models\EthicsRequest;
use App\Models\RequestDocument;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ResubmissionHelperTest extends TestCase
{
    use RefreshDatabase;

    public function test_has_resubmitted_documents_returns_true_when_version_greater_than_1_is_pending()
    {
        $student = User::factory()->create();
        $dept = Department::create(['name' => 'Computer Science', 'code' => 'CS', 'is_active' => true]);
        $docType = DocumentType::create(['name' => 'Endorsement Letter', 'is_required' => true, 'order' => 1]);

        $request = EthicsRequest::create([
            'student_id' => $student->id,
            'department_id' => $dept->id,
            'title' => 'Sample Research Study',
            'status' => RequestStatus::UnderReview,
        ]);

        $reqDoc = RequestDocument::create([
            'ethics_request_id' => $request->id,
            'document_type_id' => $docType->id,
            'status' => DocumentStatus::Pending,
        ]);

        DocumentVersion::create([
            'request_document_id' => $reqDoc->id,
            'file_path' => 'documents/v1.pdf',
            'version_number' => 1,
            'uploaded_by' => $student->id,
        ]);

        $this->assertFalse($request->hasResubmittedDocuments());
        $this->assertFalse($reqDoc->isResubmission());

        // Upload v2
        DocumentVersion::create([
            'request_document_id' => $reqDoc->id,
            'file_path' => 'documents/v2.pdf',
            'version_number' => 2,
            'uploaded_by' => $student->id,
        ]);

        $this->assertTrue($request->fresh()->hasResubmittedDocuments());
        $this->assertTrue($reqDoc->fresh()->isResubmission());
    }
}
