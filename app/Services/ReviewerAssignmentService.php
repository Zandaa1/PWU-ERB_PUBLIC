<?php

namespace App\Services;

use App\Enums\RequestStatus;
use App\Enums\ReviewerAssignmentStatus;
use App\Mail\ReviewerInvitationMail;
use App\Models\EthicsRequest;
use App\Models\ReviewerAssignment;
use App\Models\StatusLog;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use InvalidArgumentException;

class ReviewerAssignmentService
{
    /**
     * Assign committee members as reviewers for an ethics request.
     *
     * @param  array<int, int|string>  $reviewerUserIds
     */
    public function assignReviewers(EthicsRequest $request, array $reviewerUserIds, User $chair): void
    {
        if (empty($reviewerUserIds)) {
            throw new InvalidArgumentException('At least one reviewer must be provided.');
        }

        $assignmentsToNotify = [];

        DB::transaction(function () use ($request, $reviewerUserIds, $chair, &$assignmentsToNotify) {
            foreach ($reviewerUserIds as $userId) {
                $reviewer = User::findOrFail($userId);

                if (! $reviewer->hasRole('committee')) {
                    throw new InvalidArgumentException("User {$reviewer->id} is not an active committee member.");
                }

                if ($reviewer->id === $request->student_id) {
                    throw new InvalidArgumentException('The applicant cannot be assigned as a reviewer.');
                }

                $assignment = ReviewerAssignment::updateOrCreate(
                    [
                        'ethics_request_id' => $request->id,
                        'user_id' => $reviewer->id,
                    ],
                    [
                        'status' => ReviewerAssignmentStatus::Invited,
                        'invited_at' => now(),
                    ]
                );

                $assignmentsToNotify[] = [
                    'reviewer' => $reviewer,
                    'assignment' => $assignment,
                ];
            }

            $oldStatus = $request->status;
            $request->update([
                'status' => RequestStatus::UnderReview,
                'last_activity_at' => now(),
            ]);

            StatusLog::create([
                'ethics_request_id' => $request->id,
                'old_status' => $oldStatus->value,
                'new_status' => RequestStatus::UnderReview->value,
                'changed_by' => $chair->id,
                'notes' => 'Assigned committee reviewers and initiated evaluation phase.',
            ]);
        });

        // Dispatch invitation emails outside DB transaction to prevent side-effects on rollback
        foreach ($assignmentsToNotify as $item) {
            try {
                if ($item['reviewer']->email) {
                    Mail::to($item['reviewer']->email)->send(new ReviewerInvitationMail($request, $item['assignment']));
                }
            } catch (\Throwable $e) {
                // Ignore mail failure in local testing or non-blocking environments
            }
        }
    }

    public function respondToInvitation(ReviewerAssignment $assignment, bool $accepted, ?string $declineReason = null): void
    {
        DB::transaction(function () use ($assignment, $accepted, $declineReason) {
            $assignment->update([
                'status' => $accepted ? ReviewerAssignmentStatus::Accepted : ReviewerAssignmentStatus::Declined,
                'responded_at' => now(),
                'decline_reason' => $accepted ? null : $declineReason,
            ]);
        });
    }

    public function submitEvaluation(
        ReviewerAssignment $assignment,
        UploadedFile $consentForm,
        UploadedFile $protocolForm,
        string $recommendation,
        ?string $notes = null
    ): void {
        $consentPath = $consentForm->store('documents/'.$assignment->ethics_request_id.'/evaluations', 'private');
        $protocolPath = $protocolForm->store('documents/'.$assignment->ethics_request_id.'/evaluations', 'private');

        DB::transaction(function () use ($assignment, $consentPath, $protocolPath, $recommendation, $notes) {
            $assignment->update([
                'consent_assessment_path' => $consentPath,
                'protocol_assessment_path' => $protocolPath,
                'recommendation' => $recommendation,
                'reviewer_notes' => $notes,
                'status' => ReviewerAssignmentStatus::Completed,
                'completed_at' => now(),
            ]);
        });
    }

    public function checkBothReviewersCompleted(EthicsRequest $request): bool
    {
        $completedCount = $request->reviewerAssignments()
            ->where('status', ReviewerAssignmentStatus::Completed)
            ->count();

        return $completedCount >= 2;
    }
}
