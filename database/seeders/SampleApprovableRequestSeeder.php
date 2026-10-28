<?php

namespace Database\Seeders;

use App\Enums\DocumentStatus;
use App\Enums\RequestStatus;
use App\Models\Department;
use App\Models\DocumentType;
use App\Models\DocumentVersion;
use App\Models\EthicsRequest;
use App\Models\RequestDocument;
use App\Models\StatusLog;
use App\Models\User;
use App\Services\ControlNumberService;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;

class SampleApprovableRequestSeeder extends Seeder
{
    public function run(): void
    {
        $student = User::where('email', 'student@example.test')->first();
        $dept = Department::first();
        $docTypes = DocumentType::where('is_active', true)->get();
        $controlNumberService = app(ControlNumberService::class);

        // Mock PDF sample content
        $mockPdfContent = "%PDF-1.4\n1 0 obj<</Type/Catalog/Pages 2 0 R>>endobj 2 0 obj<</Type/Pages/Count 1/Kids[3 0 R]>>endobj 3 0 obj<</Type/Page/MediaBox[0 0 612 792]/Resources<</Font<var>>>>endobj\nxref\n0 4\n0000000000 65535 f\n0000000009 00000 n\n0000000052 00000 n\n0000000102 00000 n\ntrailer<</Size 4/Root 1 0 R>>\nstartxref\n178\n%%EOF";

        // ==========================================
        // Study 1: Ready for Review (All 7 Documents Uploaded - Pending Committee Action)
        // ==========================================
        $controlNo1 = $controlNumberService->generateNext('ERB');

        $request1 = EthicsRequest::firstOrCreate(
            ['title' => 'Synthetic Study One: Example Research Workflow'],
            [
                'student_id' => $student->id,
                'department_id' => $dept->id,
                'control_number' => $controlNo1,
                'status' => RequestStatus::UnderReview,
            ]
        );

        foreach ($docTypes as $type) {
            $filePath = "documents/test_study1_doc_{$type->id}.pdf";
            Storage::disk('local')->put($filePath, $mockPdfContent);

            $reqDoc = RequestDocument::firstOrCreate(
                [
                    'ethics_request_id' => $request1->id,
                    'document_type_id' => $type->id,
                ],
                [
                    'status' => DocumentStatus::UnderReview,
                ]
            );

            DocumentVersion::firstOrCreate(
                [
                    'request_document_id' => $reqDoc->id,
                    'version_number' => 1,
                ],
                [
                    'file_path' => $filePath,
                    'change_notes' => 'Initial submission for ethics review protocol evaluation.',
                    'uploaded_by' => $student->id,
                ]
            );
        }

        StatusLog::firstOrCreate([
            'ethics_request_id' => $request1->id,
            'notes' => 'Initial thesis submission received with all required protocol documents attached.',
        ], [
            'old_status' => RequestStatus::Draft->value,
            'new_status' => RequestStatus::UnderReview->value,
            'changed_by' => $student->id,
        ]);

        // ==========================================
        // Study 2: Fully Approved Documents (Ready for Live Signature & Certificate Issuance)
        // ==========================================
        $controlNo2 = $controlNumberService->generateNext('ERB');

        $request2 = EthicsRequest::firstOrCreate(
            ['title' => 'Synthetic Study Two: Review Demonstration'],
            [
                'student_id' => $student->id,
                'department_id' => $dept->id,
                'control_number' => $controlNo2,
                'status' => RequestStatus::UnderReview,
            ]
        );

        foreach ($docTypes as $type) {
            $filePath = "documents/test_study2_doc_{$type->id}.pdf";
            Storage::disk('local')->put($filePath, $mockPdfContent);

            $reqDoc = RequestDocument::firstOrCreate(
                [
                    'ethics_request_id' => $request2->id,
                    'document_type_id' => $type->id,
                ],
                [
                    'status' => DocumentStatus::Approved,
                ]
            );

            DocumentVersion::firstOrCreate(
                [
                    'request_document_id' => $reqDoc->id,
                    'version_number' => 1,
                ],
                [
                    'file_path' => $filePath,
                    'change_notes' => 'Initial protocol submission.',
                    'uploaded_by' => $student->id,
                ]
            );
        }

        StatusLog::firstOrCreate([
            'ethics_request_id' => $request2->id,
            'notes' => 'All 7 required protocol documents individually evaluated and approved by review committee.',
        ], [
            'old_status' => RequestStatus::Draft->value,
            'new_status' => RequestStatus::UnderReview->value,
            'changed_by' => $student->id,
        ]);
    }
}
