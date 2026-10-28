<?php

namespace App\Services;

use App\Enums\RequestStatus;
use App\Enums\ReviewType;
use App\Models\EthicsRequest;
use App\Models\StatusLog;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

class ReviewRoutingService
{
    public function routeToFullReview(EthicsRequest $request, User $chair): void
    {
        if ($request->status !== RequestStatus::ReviewRouting) {
            throw new InvalidArgumentException("Cannot route request in {$request->status->value} status to Full Review.");
        }

        DB::transaction(function () use ($request, $chair) {
            $oldStatus = $request->status;

            $request->update([
                'review_type' => ReviewType::FullReview,
                'status' => RequestStatus::FullReviewPending,
                'last_activity_at' => now(),
            ]);

            StatusLog::create([
                'ethics_request_id' => $request->id,
                'old_status' => $oldStatus->value,
                'new_status' => RequestStatus::FullReviewPending->value,
                'changed_by' => $chair->id,
                'notes' => 'Routed to Full Committee Review.',
            ]);
        });
    }

    public function routeToExpeditedReview(EthicsRequest $request, User $chair): void
    {
        if ($request->status !== RequestStatus::ReviewRouting) {
            throw new InvalidArgumentException("Cannot route request in {$request->status->value} status to Expedited Review.");
        }

        DB::transaction(function () use ($request, $chair) {
            $oldStatus = $request->status;

            $request->update([
                'review_type' => ReviewType::ExpeditedReview,
                'status' => RequestStatus::AssigningReviewers,
                'last_activity_at' => now(),
            ]);

            StatusLog::create([
                'ethics_request_id' => $request->id,
                'old_status' => $oldStatus->value,
                'new_status' => RequestStatus::AssigningReviewers->value,
                'changed_by' => $chair->id,
                'notes' => 'Routed to Expedited Review. Awaiting reviewer assignment.',
            ]);
        });
    }

    public function markOutOfScope(EthicsRequest $request, User $chair, string $reason): void
    {
        if ($request->status !== RequestStatus::ReviewRouting) {
            throw new InvalidArgumentException("Cannot mark request in {$request->status->value} status as Out of Scope.");
        }

        DB::transaction(function () use ($request, $chair, $reason) {
            $oldStatus = $request->status;

            $request->update([
                'status' => RequestStatus::OutOfScope,
                'screening_notes' => $reason,
                'last_activity_at' => now(),
            ]);

            StatusLog::create([
                'ethics_request_id' => $request->id,
                'old_status' => $oldStatus->value,
                'new_status' => RequestStatus::OutOfScope->value,
                'changed_by' => $chair->id,
                'notes' => $reason,
            ]);
        });
    }

    public function completeFullReviewDeliberation(EthicsRequest $request, User $chair, string $deliberationNotes): void
    {
        if ($request->status !== RequestStatus::FullReviewPending) {
            throw new InvalidArgumentException("Cannot record deliberation notes for request in {$request->status->value} status.");
        }

        DB::transaction(function () use ($request, $deliberationNotes) {
            $request->update([
                'deliberation_notes' => $deliberationNotes,
                'last_activity_at' => now(),
            ]);
        });
    }
}
