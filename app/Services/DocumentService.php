<?php

namespace App\Services;

use App\Enums\DocumentStatus;
use App\Enums\RequestStatus;
use App\Mail\EthicsRequestStatusUpdatedMail;
use App\Models\EthicsRequest;
use App\Models\Notification;
use App\Models\RequestDocument;
use App\Models\StatusLog;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;

/**
 * Service managing document lifecycle transitions, individual document approvals,
 * revision requests, and overall institutional ethics clearance certificate issuance.
 *
 * Domain Rules:
 * - Each required document must be approved individually before the request can be approved.
 * - Approval of the overall request generates an official PDF certificate with embedded signatures.
 * - Transactions guarantee database integrity; notification emails are dispatched post-commit.
 */
class DocumentService
{
    public function __construct(protected ControlNumberService $controlNumberService) {}

    /**
     * Mark an individual document as Approved.
     * Automatically transitions the overall request from Submitted to UnderReview if applicable.
     */
    public function approveDocument(RequestDocument $doc, User $reviewer): void
    {
        DB::transaction(function () use ($doc, $reviewer) {
            $oldStatus = $doc->status->value;
            $doc->update(['status' => DocumentStatus::Approved]);

            StatusLog::create([
                'ethics_request_id' => $doc->ethics_request_id,
                'old_status' => $oldStatus,
                'new_status' => DocumentStatus::Approved->value,
                'changed_by' => $reviewer->id,
                'notes' => 'Document approved: '.$doc->documentType->name,
            ]);

            $this->updateOverallRequestStatusIfUnderReview($doc->ethicsRequest);
        });
    }

    /**
     * Request a revision for a specific document.
     * Transitions both the document and overall request to RevisionsRequested and notifies the student.
     */
    public function requestRevision(RequestDocument $doc, User $reviewer): void
    {
        $student = $doc->ethicsRequest->student;

        DB::transaction(function () use ($doc, $reviewer) {
            $oldStatus = $doc->status->value;
            $doc->update(['status' => DocumentStatus::RevisionsRequested]);

            $doc->ethicsRequest->update([
                'status' => RequestStatus::RevisionsRequested,
                'last_activity_at' => now(),
            ]);

            StatusLog::create([
                'ethics_request_id' => $doc->ethics_request_id,
                'old_status' => $oldStatus,
                'new_status' => DocumentStatus::RevisionsRequested->value,
                'changed_by' => $reviewer->id,
                'notes' => 'Revision requested for document: '.$doc->documentType->name,
            ]);

            Notification::create([
                'user_id' => $doc->ethicsRequest->student_id,
                'type' => 'revision_requested',
                'message' => 'Revision requested for document: '.$doc->documentType->name,
                'link' => "/requests/{$doc->ethics_request_id}/documents/{$doc->id}",
            ]);
        });

        // Dispatch email notification outside the transaction
        if ($student && ! empty($student->email)) {
            try {
                Mail::to($student->email)->send(new EthicsRequestStatusUpdatedMail(
                    $doc->ethicsRequest,
                    'Revision requested for document: '.$doc->documentType->name
                ));
            } catch (\Throwable $e) {
                report($e);
            }
        }
    }

    /**
     * Fully approve an ethics review request and issue the official ERB Clearance Certificate.
     *
     * Invariants:
     * - Generates an atomic control number in sequence (e.g. ERB2026_00001).
     * - Sets 1-year expiration date (start of day).
     * - Renders 8.5" x 13" legal portrait DomPDF certificate with Chairperson signature.
     * - Persists PDF certificate in private storage (`storage/app/certificates/{control_number}.pdf`).
     * - Dispatches transactional status update email and in-app notification.
     */
    public function approveEntireRequest(EthicsRequest $request, User $reviewer, string $signatureData): string
    {
        $fileName = '';
        $controlNumber = '';

        DB::transaction(function () use ($request, $reviewer, $signatureData, &$fileName, &$controlNumber) {
            // 1. Generate atomic control number
            $controlNumber = $this->controlNumberService->generateNext();

            $approvalDate = now()->startOfDay();
            $expiryDate = now()->addYear()->startOfDay();

            $request->control_number = $controlNumber;
            $request->approval_date = $approvalDate;
            $request->expiry_date = $expiryDate;

            // 2. Resolve Chairperson designation
            $chairperson = User::where('is_chairperson', true)->first() ?? $reviewer;

            // 3. Render DomPDF (8.5 x 13 inches legal portrait format: 612 x 936 pt)
            $pdf = Pdf::loadView('certificates.erb-certificate', [
                'request' => $request,
                'chairperson' => $chairperson,
                'signatureData' => $signatureData,
            ])->setPaper([0, 0, 612, 936], 'portrait');

            $fileName = "certificates/{$controlNumber}.pdf";
            Storage::disk('local')->put($fileName, $pdf->output());

            // 4. Update request status to Approved
            $oldStatus = $request->status->value;
            $request->status = RequestStatus::Approved;
            $request->certificate_path = $fileName;
            $request->last_activity_at = now();
            $request->save();

            // 5. Create immutable audit status log
            StatusLog::create([
                'ethics_request_id' => $request->id,
                'old_status' => $oldStatus,
                'new_status' => RequestStatus::Approved->value,
                'changed_by' => $reviewer->id,
                'notes' => "Ethics Request fully approved. Control Number {$controlNumber} issued.",
            ]);

            // 6. Create in-app student notification
            Notification::create([
                'user_id' => $request->student_id,
                'type' => 'request_approved',
                'message' => "Congratulations! Your ethics review request has been APPROVED (Control No. {$controlNumber}). Download your Certificate of Approval now.",
                'link' => "/requests/{$request->id}",
            ]);
        });

        // 7. Dispatch celebratory approval email post-commit
        $student = $request->student;
        if ($student && ! empty($student->email)) {
            try {
                Mail::to($student->email)->send(new EthicsRequestStatusUpdatedMail(
                    $request,
                    "Ethics Request fully approved. Control Number {$controlNumber} issued."
                ));
            } catch (\Throwable $e) {
                report($e);
            }
        }

        return $fileName;
    }

    /**
     * Transition a submitted request to UnderReview upon committee member activity.
     */
    private function updateOverallRequestStatusIfUnderReview(EthicsRequest $request): void
    {
        if ($request->status === RequestStatus::Submitted) {
            $request->update([
                'status' => RequestStatus::UnderReview,
                'last_activity_at' => now(),
            ]);
        }
    }
}
