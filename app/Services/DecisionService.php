<?php

namespace App\Services;

use App\Enums\MonitoringStatus;
use App\Enums\RequestStatus;
use App\Mail\EthicsRequestStatusUpdatedMail;
use App\Models\EthicsRequest;
use App\Models\Notification;
use App\Models\ResearchMonitor;
use App\Models\StatusLog;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;

class DecisionService
{
    public function approveAndIssueCertificate(EthicsRequest $request, User $chair, string $signatureData): void
    {
        $oldStatus = $request->status ? $request->status->value : RequestStatus::UnderReview->value;

        DB::transaction(function () use ($request, $chair, $signatureData, $oldStatus) {
            $controlNumber = app(ControlNumberService::class)->generateNext();
            $approvalDate = now();
            $expiryDate = now()->addYear();

            $request->update([
                'status' => RequestStatus::Approved,
                'control_number' => $controlNumber,
                'approval_date' => $approvalDate,
                'expiry_date' => $expiryDate,
                'last_activity_at' => now(),
            ]);

            $pdf = Pdf::loadView('certificates.erb-certificate', [
                'request' => $request,
                'chairperson' => $chair,
                'signatureData' => $signatureData,
            ])->setPaper([0, 0, 612, 936], 'portrait');

            $certPath = 'certificates/'.$controlNumber.'.pdf';
            Storage::disk('private')->put($certPath, $pdf->output());

            $request->update(['certificate_path' => $certPath]);

            ResearchMonitor::updateOrCreate(
                ['ethics_request_id' => $request->id],
                [
                    'monitoring_status' => MonitoringStatus::Active,
                    'approved_at' => $approvalDate,
                    'expires_at' => $expiryDate,
                    'next_report_due_at' => $approvalDate->copy()->addMonths(6),
                ]
            );

            StatusLog::create([
                'ethics_request_id' => $request->id,
                'old_status' => $oldStatus,
                'new_status' => RequestStatus::Approved->value,
                'changed_by' => $chair->id,
                'notes' => 'Ethics Request approved by Chairperson. Control number '.$controlNumber.' issued.',
            ]);

            Notification::create([
                'user_id' => $request->student_id,
                'type' => 'request_approved',
                'message' => "Congratulations! Your ethics review request has been APPROVED (Control No. {$controlNumber}). Download your Certificate of Approval now.",
                'link' => "/requests/{$request->id}",
            ]);
        });

        $student = $request->student;
        if ($student && ! empty($student->email)) {
            try {
                Mail::to($student->email)->send(new EthicsRequestStatusUpdatedMail(
                    $request,
                    "Ethics Request fully approved. Control Number {$request->control_number} issued."
                ));
            } catch (\Throwable $e) {
                report($e);
            }
        }
    }

    public function issueHostCountryRequirement(EthicsRequest $request, User $chair, string $signatureData): void
    {
        $oldStatus = $request->status ? $request->status->value : RequestStatus::UnderReview->value;

        DB::transaction(function () use ($request, $chair, $signatureData, $oldStatus) {
            $controlNumber = app(ControlNumberService::class)->generateNext();
            $issuedDate = now();

            $request->update([
                'status' => RequestStatus::HostCountryRequired,
                'control_number' => $controlNumber,
                'approval_date' => $issuedDate,
                'last_activity_at' => now(),
            ]);

            $pdf = Pdf::loadView('certificates.host-country-requirement', [
                'request' => $request,
                'chairperson' => $chair,
                'signatureData' => $signatureData,
            ])->setPaper([0, 0, 612, 936], 'portrait');

            $certPath = 'certificates/HOST_REQ_'.$controlNumber.'.pdf';
            Storage::disk('private')->put($certPath, $pdf->output());

            $request->update(['host_country_cert_path' => $certPath]);

            ResearchMonitor::updateOrCreate(
                ['ethics_request_id' => $request->id],
                [
                    'monitoring_status' => MonitoringStatus::HostCountryPending,
                    'approved_at' => $issuedDate,
                    'expires_at' => $issuedDate->copy()->addYear(),
                ]
            );

            StatusLog::create([
                'ethics_request_id' => $request->id,
                'old_status' => $oldStatus,
                'new_status' => RequestStatus::HostCountryRequired->value,
                'changed_by' => $chair->id,
                'notes' => 'Requirement to review in host country issued by Chairperson.',
            ]);

            Notification::create([
                'user_id' => $request->student_id,
                'type' => 'status_updated',
                'message' => "Ethics Protocol (Control No. {$controlNumber}) requires institutional ethics clearance from host country: ".($request->host_country ?? 'Host Country').'.',
                'link' => "/requests/{$request->id}",
            ]);
        });

        $student = $request->student;
        if ($student && ! empty($student->email)) {
            try {
                Mail::to($student->email)->send(new EthicsRequestStatusUpdatedMail(
                    $request,
                    'Ethics Protocol requires institutional review in host country: '.($request->host_country ?? 'Host Country').'.'
                ));
            } catch (\Throwable $e) {
                report($e);
            }
        }
    }

    public function requestLocalRevisions(EthicsRequest $request, User $chair, string $notes): void
    {
        $oldStatus = $request->status ? $request->status->value : RequestStatus::UnderReview->value;

        DB::transaction(function () use ($request, $chair, $notes, $oldStatus) {
            $request->update([
                'status' => RequestStatus::RevisionsRequested,
                'screening_notes' => $notes,
                'last_activity_at' => now(),
            ]);

            StatusLog::create([
                'ethics_request_id' => $request->id,
                'old_status' => $oldStatus,
                'new_status' => RequestStatus::RevisionsRequested->value,
                'changed_by' => $chair->id,
                'notes' => 'Chairperson requested protocol revisions: '.$notes,
            ]);

            Notification::create([
                'user_id' => $request->student_id,
                'type' => 'status_updated',
                'message' => 'Chairperson requested revisions for your protocol. Please review feedback.',
                'link' => "/requests/{$request->id}",
            ]);
        });

        $student = $request->student;
        if ($student && ! empty($student->email)) {
            try {
                Mail::to($student->email)->send(new EthicsRequestStatusUpdatedMail(
                    $request,
                    'Chairperson requested revisions for your protocol: '.$notes
                ));
            } catch (\Throwable $e) {
                report($e);
            }
        }
    }
}
