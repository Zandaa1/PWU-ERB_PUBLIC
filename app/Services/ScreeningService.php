<?php

namespace App\Services;

use App\Enums\RequestStatus;
use App\Models\EthicsRequest;
use App\Models\StatusLog;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

class ScreeningService
{
    public function passScreening(EthicsRequest $request, User $secretary, float $plagScore, float $aiScore, ?string $notes = null): void
    {
        if (! in_array($request->status, [RequestStatus::Submitted, RequestStatus::Screening])) {
            throw new InvalidArgumentException("Cannot pass screening for request in {$request->status->value} status.");
        }

        DB::transaction(function () use ($request, $secretary, $plagScore, $aiScore, $notes) {
            $oldStatus = $request->status;

            $request->update([
                'status' => RequestStatus::ReviewRouting,
                'plagiarism_similarity_score' => $plagScore,
                'ai_similarity_score' => $aiScore,
                'plagiarism_verified_at' => now(),
                'screened_by' => $secretary->id,
                'screening_notes' => $notes,
                'last_activity_at' => now(),
            ]);

            StatusLog::create([
                'ethics_request_id' => $request->id,
                'old_status' => $oldStatus->value,
                'new_status' => RequestStatus::ReviewRouting->value,
                'changed_by' => $secretary->id,
            ]);
        });
    }

    public function requestScreeningRevision(EthicsRequest $request, User $secretary, string $notes): void
    {
        if (! in_array($request->status, [RequestStatus::Submitted, RequestStatus::Screening])) {
            throw new InvalidArgumentException("Cannot request screening revision for request in {$request->status->value} status.");
        }

        DB::transaction(function () use ($request, $secretary, $notes) {
            $oldStatus = $request->status;

            $request->update([
                'status' => RequestStatus::ScreeningRevision,
                'screened_by' => $secretary->id,
                'screening_notes' => $notes,
                'last_activity_at' => now(),
            ]);

            StatusLog::create([
                'ethics_request_id' => $request->id,
                'old_status' => $oldStatus->value,
                'new_status' => RequestStatus::ScreeningRevision->value,
                'changed_by' => $secretary->id,
            ]);
        });
    }
}
