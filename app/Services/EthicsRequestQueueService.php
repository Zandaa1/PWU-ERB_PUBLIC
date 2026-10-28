<?php

namespace App\Services;

use App\Enums\RequestStatus;
use App\Models\EthicsRequest;
use Carbon\Carbon;

class EthicsRequestQueueService
{
    /**
     * Get active queue position for a given ethics request within its department.
     */
    public function getQueuePosition(EthicsRequest $request): int
    {
        if ($request->status === RequestStatus::Approved) {
            return 0;
        }

        return EthicsRequest::where('department_id', $request->department_id)
            ->whereIn('status', [RequestStatus::Submitted, RequestStatus::UnderReview, RequestStatus::RevisionsRequested])
            ->where('id', '<=', $request->id)
            ->count();
    }

    /**
     * Get estimated decision date (3 to 5 business days after submission).
     */
    public function getEstimatedDecisionDate(EthicsRequest $request): Carbon
    {
        $submittedAt = Carbon::parse($request->created_at);
        $businessDaysAdded = 0;
        $currentDate = $submittedAt->copy();

        while ($businessDaysAdded < 4) {
            $currentDate->addDay();
            if (! $currentDate->isWeekend()) {
                $businessDaysAdded++;
            }
        }

        return $currentDate;
    }

    /**
     * Get total active pending queue count for a department.
     */
    public function getDepartmentActiveQueueCount(int $departmentId): int
    {
        return EthicsRequest::where('department_id', $departmentId)
            ->whereIn('status', [RequestStatus::Submitted, RequestStatus::UnderReview, RequestStatus::RevisionsRequested])
            ->count();
    }
}
