<?php

namespace App\Livewire;

use App\Enums\DocumentStatus;
use App\Enums\RequestStatus;
use App\Enums\ReviewerAssignmentStatus;
use App\Mail\EthicsRequestStatusUpdatedMail;
use App\Models\EthicsRequest;
use App\Models\Notification;
use App\Models\ReviewerAssignment;
use App\Models\StatusLog;
use App\Models\User;
use App\Services\DecisionService;
use App\Services\DocumentService;
use App\Services\EthicsRequestQueueService;
use App\Services\ReviewerAssignmentService;
use App\Services\ReviewRoutingService;
use App\Services\ScreeningService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Livewire\Component;
use Livewire\WithFileUploads;

class RequestDetail extends Component
{
    use WithFileUploads;

    public EthicsRequest $request;

    public bool $showSignatureModal = false;

    public bool $showChairApprovalModal = false;

    public bool $showHostCountryModal = false;

    public string $decision_revision_notes = '';

    public bool $showDisapproveModal = false;

    public string $disapprovalRemarks = '';

    public string $screening_notes = '';

    public string $plagiarism_similarity_score = '';

    public string $ai_similarity_score = '';

    public ?string $selected_reviewer_1 = null;

    public ?string $selected_reviewer_2 = null;

    public string $out_of_scope_reason = '';

    public string $deliberation_notes = '';

    public bool $showOutOfScopeModal = false;

    public $assessment_consent_file;

    public $assessment_protocol_file;

    public string $assessment_recommendation = '';

    public string $assessment_notes = '';

    public string $decline_reason = '';

    public bool $showDeclineModal = false;

    public ?int $decliningAssignmentId = null;

    public function mount(int $id)
    {
        $this->request = EthicsRequest::with([
            'student',
            'department',
            'requestDocuments.documentType',
            'requestDocuments.latestVersion',
            'reviewerAssignments.user',
            'statusLogs.user',
        ])->findOrFail($id);

        $this->deliberation_notes = $this->request->deliberation_notes ?? '';
        $this->out_of_scope_reason = $this->request->screening_notes ?? '';
    }

    public function openSignatureModal()
    {
        if (! $this->canApproveRequest()) {
            session()->flash('error', 'All required documents must be individually approved before final approval and certificate issuance.');

            return;
        }

        $this->showSignatureModal = true;
    }

    public function closeSignatureModal()
    {
        $this->showSignatureModal = false;
    }

    public function openDisapproveModal()
    {
        $user = auth()->user();
        if (! $user || $user->id === $this->request->student_id || (! $user->hasAnyRole(['committee', 'admin']) && ! $user->is_chairperson && ! $user->is_developer)) {
            abort(403, 'Unauthorized.');
        }

        if (in_array($this->request->status, [RequestStatus::Approved, RequestStatus::Disapproved])) {
            session()->flash('info', 'This request is already finalized.');

            return;
        }

        $this->disapprovalRemarks = '';
        $this->showDisapproveModal = true;
        $this->resetErrorBag();
    }

    public function closeDisapproveModal()
    {
        $this->showDisapproveModal = false;
        $this->disapprovalRemarks = '';
        $this->resetErrorBag();
    }

    public function confirmDisapprove()
    {
        $user = auth()->user();
        if (! $user || $user->id === $this->request->student_id || (! $user->hasAnyRole(['committee', 'admin']) && ! $user->is_chairperson && ! $user->is_developer)) {
            abort(403, 'Unauthorized.');
        }

        $this->validate([
            'disapprovalRemarks' => 'required|string|min:10|max:1000',
        ], [
            'disapprovalRemarks.required' => 'Please provide an official justification/reason for disapproving this protocol.',
            'disapprovalRemarks.min' => 'Disapproval reason must be at least 10 characters.',
        ]);

        $oldStatus = $this->request->status;

        DB::transaction(function () use ($oldStatus) {
            $this->request->update([
                'status' => RequestStatus::Disapproved,
                'last_activity_at' => now(),
            ]);

            StatusLog::create([
                'ethics_request_id' => $this->request->id,
                'old_status' => $oldStatus->value,
                'new_status' => RequestStatus::Disapproved->value,
                'changed_by' => auth()->id(),
                'notes' => 'Protocol Disapproved: '.$this->disapprovalRemarks,
            ]);

            Notification::create([
                'user_id' => $this->request->student_id,
                'type' => 'status_updated',
                'message' => 'Your ethics review application was Disapproved by the ERB Committee. Reason: '.$this->disapprovalRemarks,
                'link' => '/requests/'.$this->request->id,
                'is_read' => false,
            ]);
        });

        // Dispatch notification email
        try {
            if ($this->request->student && $this->request->student->email) {
                Mail::to($this->request->student->email)->send(
                    new EthicsRequestStatusUpdatedMail($this->request, $this->disapprovalRemarks)
                );
            }
        } catch (\Throwable $e) {
            Log::error('Disapproval email dispatch error: '.$e->getMessage());
        }

        $this->showDisapproveModal = false;
        $this->request->refresh();
        session()->flash('info', 'Ethics Request has been formally Disapproved and the researcher notified.');
    }

    public function canApproveRequest(): bool
    {
        $user = auth()->user();
        if (! $user || $user->id === $this->request->student_id) {
            return false;
        }

        if (! $user->hasAnyRole(['committee', 'admin']) && ! $user->is_chairperson && ! $user->is_developer) {
            return false;
        }

        $unapproved = $this->request->requestDocuments
            ->filter(function ($doc) {
                if (! $doc->documentType->is_required && ! $doc->latestVersion) {
                    return false;
                }

                return $doc->status !== DocumentStatus::Approved;
            });

        return $unapproved->count() === 0 && $this->request->status !== RequestStatus::Approved;
    }

    public function approveWithSignature(string $signatureData)
    {
        if (auth()->id() === $this->request->student_id) {
            abort(403, 'Applicants cannot approve their own protocols.');
        }

        if (! $this->canApproveRequest()) {
            session()->flash('error', 'Cannot approve request until all documents are individually approved.');

            return;
        }

        app(DocumentService::class)->approveEntireRequest($this->request, auth()->user(), $signatureData);

        $this->showSignatureModal = false;
        $this->request->refresh();
        session()->flash('success', 'Ethics Request approved and Certificate generated successfully!');
    }

    public function canMakeChairDecision(): bool
    {
        $user = auth()->user();

        if (! $user || $user->id === $this->request->student_id) {
            return false;
        }

        return $user->is_chairperson || $user->hasRole('admin') || $user->is_developer;
    }

    public function openChairApprovalModal(): void
    {
        if (! $this->canMakeChairDecision()) {
            abort(403, 'Unauthorized.');
        }

        if (! in_array($this->request->status, [RequestStatus::UnderReview, RequestStatus::FullReviewPending, RequestStatus::ReviewRouting])) {
            session()->flash('error', 'Protocol is not in an actionable review status.');

            return;
        }

        $this->showChairApprovalModal = true;
    }

    public function closeChairApprovalModal(): void
    {
        $this->showChairApprovalModal = false;
    }

    public function openHostCountryModal(): void
    {
        if (! $this->canMakeChairDecision()) {
            abort(403, 'Unauthorized.');
        }

        if (! in_array($this->request->status, [RequestStatus::UnderReview, RequestStatus::FullReviewPending, RequestStatus::ReviewRouting])) {
            session()->flash('error', 'Protocol is not in an actionable review status.');

            return;
        }

        $this->showHostCountryModal = true;
    }

    public function closeHostCountryModal(): void
    {
        $this->showHostCountryModal = false;
    }

    public function approveAndIssueCertificate(string $signatureData): void
    {
        $user = auth()->user();
        if (! $this->canMakeChairDecision()) {
            abort(403, 'Unauthorized.');
        }

        if (! in_array($this->request->status, [RequestStatus::UnderReview, RequestStatus::FullReviewPending, RequestStatus::ReviewRouting])) {
            session()->flash('error', 'Protocol is not in an actionable review status for approval.');

            return;
        }

        app(DecisionService::class)->approveAndIssueCertificate($this->request, $user, $signatureData);

        $this->showChairApprovalModal = false;
        $this->showSignatureModal = false;
        $this->refresh();
        session()->flash('success', 'Ethics Protocol approved and Certificate of Approval issued successfully.');
    }

    public function issueHostCountryRequirement(string $signatureData): void
    {
        $user = auth()->user();
        if (! $this->canMakeChairDecision()) {
            abort(403, 'Unauthorized.');
        }

        if (! in_array($this->request->status, [RequestStatus::UnderReview, RequestStatus::FullReviewPending, RequestStatus::ReviewRouting])) {
            session()->flash('error', 'Protocol is not in an actionable review status for host country requirement.');

            return;
        }

        app(DecisionService::class)->issueHostCountryRequirement($this->request, $user, $signatureData);

        $this->showHostCountryModal = false;
        $this->refresh();
        session()->flash('success', 'Requirement to Review in Host Countries issued successfully.');
    }

    public function requestLocalRevisions(): void
    {
        $user = auth()->user();
        if (! $this->canMakeChairDecision()) {
            abort(403, 'Unauthorized.');
        }

        if (! in_array($this->request->status, [RequestStatus::UnderReview, RequestStatus::FullReviewPending, RequestStatus::ReviewRouting])) {
            session()->flash('error', 'Protocol is not in an actionable review status for requesting revisions.');

            return;
        }

        $this->validate([
            'decision_revision_notes' => ['required', 'string', 'min:5', 'max:2000'],
        ], [
            'decision_revision_notes.required' => 'Please enter revision instructions or notes for the researcher.',
            'decision_revision_notes.min' => 'Revision instructions must be at least 5 characters.',
        ]);

        app(DecisionService::class)->requestLocalRevisions(
            $this->request,
            $user,
            $this->decision_revision_notes
        );

        $this->decision_revision_notes = '';
        $this->refresh();
        session()->flash('status', 'Protocol revisions requested and researcher notified.');
    }

    public function passScreening(): void
    {
        if (! $this->canScreen()) {
            abort(403, 'Unauthorized.');
        }

        if (! in_array($this->request->status, [RequestStatus::Submitted, RequestStatus::Screening])) {
            session()->flash('error', 'This request is not in a screenable state.');

            return;
        }

        $this->validate([
            'plagiarism_similarity_score' => ['required', 'numeric', 'min:0', 'max:100'],
            'ai_similarity_score' => ['required', 'numeric', 'min:0', 'max:100'],
            'screening_notes' => ['nullable', 'string', 'max:2000'],
        ]);

        app(ScreeningService::class)->passScreening(
            $this->request,
            auth()->user(),
            (float) $this->plagiarism_similarity_score,
            (float) $this->ai_similarity_score,
            $this->screening_notes
        );

        $this->refresh();
        session()->flash('status', 'Screening approved and forwarded to Chairperson.');
    }

    public function returnScreeningRevision(): void
    {
        if (! $this->canScreen()) {
            abort(403, 'Unauthorized.');
        }

        if (! in_array($this->request->status, [RequestStatus::Submitted, RequestStatus::Screening])) {
            session()->flash('error', 'This request is not in a screenable state.');

            return;
        }

        $this->validate([
            'screening_notes' => ['required', 'string', 'min:5', 'max:2000'],
        ]);

        app(ScreeningService::class)->requestScreeningRevision(
            $this->request,
            auth()->user(),
            $this->screening_notes
        );

        $this->refresh();
        session()->flash('status', 'Dossier returned to applicant for screening revision.');
    }

    public function refresh(): void
    {
        $this->request->refresh();
        $this->request->load([
            'student',
            'department',
            'requestDocuments.documentType',
            'requestDocuments.latestVersion',
            'reviewerAssignments.user',
            'statusLogs.user',
        ]);
    }

    public function canScreen(): bool
    {
        $user = auth()->user();

        if (! $user || $user->id === $this->request->student_id) {
            return false;
        }

        return $user->hasRole('secretary') || $user->hasRole('admin');
    }

    public function canReview(): bool
    {
        $user = auth()->user();

        if (! $user || $user->id === $this->request->student_id) {
            return false;
        }

        return $user->hasAnyRole(['committee', 'admin']) || $user->is_chairperson || $user->is_developer;
    }

    public function isChairpersonOrAdmin(): bool
    {
        $user = auth()->user();

        if (! $user || $user->id === $this->request->student_id) {
            return false;
        }

        return $user->is_chairperson || $user->hasRole('admin') || $user->is_developer;
    }

    public function routeReviewType(string $type): void
    {
        if (! $this->isChairpersonOrAdmin()) {
            abort(403, 'Unauthorized.');
        }

        if ($type === 'full_review') {
            app(ReviewRoutingService::class)->routeToFullReview($this->request, auth()->user());
            session()->flash('status', 'Request routed to Full Committee Review.');
        } elseif ($type === 'expedited_review') {
            app(ReviewRoutingService::class)->routeToExpeditedReview($this->request, auth()->user());
            session()->flash('status', 'Request routed to Expedited Review. Please assign domain reviewers.');
        } else {
            session()->flash('error', 'Invalid review type specified.');

            return;
        }

        $this->refresh();
    }

    public function markOutOfScope(): void
    {
        if (! $this->isChairpersonOrAdmin()) {
            abort(403, 'Unauthorized.');
        }

        $this->validate([
            'out_of_scope_reason' => ['required', 'string', 'min:5', 'max:2000'],
        ]);

        app(ReviewRoutingService::class)->markOutOfScope(
            $this->request,
            auth()->user(),
            $this->out_of_scope_reason
        );

        $this->showOutOfScopeModal = false;
        $this->refresh();
        session()->flash('status', 'Request has been formally marked as Out of Scope.');
    }

    public function completeFullReviewDeliberation(): void
    {
        if (! $this->isChairpersonOrAdmin()) {
            abort(403, 'Unauthorized.');
        }

        $this->validate([
            'deliberation_notes' => ['required', 'string', 'min:5', 'max:5000'],
        ]);

        app(ReviewRoutingService::class)->completeFullReviewDeliberation(
            $this->request,
            auth()->user(),
            $this->deliberation_notes
        );

        $this->refresh();
        session()->flash('success', 'Quarterly meeting deliberation notes successfully recorded.');
    }

    public function assignReviewers(): void
    {
        if (! $this->isChairpersonOrAdmin()) {
            abort(403, 'Unauthorized.');
        }

        $this->validate([
            'selected_reviewer_1' => ['required', 'exists:users,id', 'different:selected_reviewer_2'],
            'selected_reviewer_2' => ['required', 'exists:users,id', 'different:selected_reviewer_1'],
        ], [
            'selected_reviewer_1.required' => 'Please select the primary reviewer.',
            'selected_reviewer_2.required' => 'Please select the secondary reviewer.',
            'selected_reviewer_1.different' => 'Primary and secondary reviewers must be different committee members.',
            'selected_reviewer_2.different' => 'Primary and secondary reviewers must be different committee members.',
        ]);

        $rev1 = User::findOrFail($this->selected_reviewer_1);
        $rev2 = User::findOrFail($this->selected_reviewer_2);

        if (! $rev1->hasRole('committee')) {
            $this->addError('selected_reviewer_1', 'Selected reviewers must have the committee role.');

            return;
        }

        if (! $rev2->hasRole('committee')) {
            $this->addError('selected_reviewer_2', 'Selected reviewers must have the committee role.');

            return;
        }

        if ($rev1->id === $this->request->student_id) {
            $this->addError('selected_reviewer_1', 'The applicant cannot be assigned as a reviewer.');

            return;
        }

        if ($rev2->id === $this->request->student_id) {
            $this->addError('selected_reviewer_2', 'The applicant cannot be assigned as a reviewer.');

            return;
        }

        app(ReviewerAssignmentService::class)->assignReviewers(
            $this->request,
            [(int) $this->selected_reviewer_1, (int) $this->selected_reviewer_2],
            auth()->user()
        );

        $this->refresh();
        session()->flash('success', 'Domain reviewers assigned successfully and review invitations sent.');
    }

    public function acceptReviewInvitation(int $assignmentId): void
    {
        $assignment = ReviewerAssignment::where('id', $assignmentId)
            ->where('ethics_request_id', $this->request->id)
            ->firstOrFail();

        $user = auth()->user();
        if (! $user || $user->id === $this->request->student_id || ($user->id !== $assignment->user_id && ! $user->hasRole('admin') && ! $user->is_developer)) {
            abort(403, 'Unauthorized.');
        }

        if ($assignment->status !== ReviewerAssignmentStatus::Invited) {
            abort(400, 'This review invitation is no longer pending.');
        }

        app(ReviewerAssignmentService::class)->respondToInvitation($assignment, true);
        $this->refresh();
        session()->flash('success', 'Review invitation accepted.');
    }

    public function openDeclineModal(int $assignmentId): void
    {
        $this->decliningAssignmentId = $assignmentId;
        $this->decline_reason = '';
        $this->showDeclineModal = true;
    }

    public function closeDeclineModal(): void
    {
        $this->showDeclineModal = false;
        $this->decliningAssignmentId = null;
        $this->decline_reason = '';
    }

    public function declineReviewInvitation(int $assignmentId): void
    {
        $assignment = ReviewerAssignment::where('id', $assignmentId)
            ->where('ethics_request_id', $this->request->id)
            ->firstOrFail();

        $user = auth()->user();
        if (! $user || $user->id === $this->request->student_id || ($user->id !== $assignment->user_id && ! $user->hasRole('admin') && ! $user->is_developer)) {
            abort(403, 'Unauthorized.');
        }

        if ($assignment->status !== ReviewerAssignmentStatus::Invited) {
            abort(400, 'This review invitation is no longer pending.');
        }

        $this->validate([
            'decline_reason' => ['nullable', 'string', 'max:1000'],
        ]);

        app(ReviewerAssignmentService::class)->respondToInvitation($assignment, false, $this->decline_reason ?: null);
        $this->showDeclineModal = false;
        $this->decliningAssignmentId = null;
        $this->decline_reason = '';
        $this->refresh();
        session()->flash('info', 'Review invitation declined.');
    }

    public function submitReviewerEvaluation(int $assignmentId): void
    {
        $assignment = ReviewerAssignment::where('id', $assignmentId)
            ->where('ethics_request_id', $this->request->id)
            ->firstOrFail();

        $user = auth()->user();
        if (! $user || $user->id === $this->request->student_id || ($user->id !== $assignment->user_id && ! $user->hasRole('admin') && ! $user->is_developer)) {
            abort(403, 'Unauthorized.');
        }

        if ($assignment->status !== ReviewerAssignmentStatus::Accepted) {
            abort(400, 'Review evaluation can only be submitted for accepted review assignments.');
        }

        $this->validate([
            'assessment_consent_file' => ['required', 'file', 'mimes:pdf,doc,docx', 'max:10240'],
            'assessment_protocol_file' => ['required', 'file', 'mimes:pdf,doc,docx', 'max:10240'],
            'assessment_recommendation' => ['required', 'string', 'in:approved,revisions_required,disapproved'],
            'assessment_notes' => ['nullable', 'string', 'max:5000'],
        ], [
            'assessment_consent_file.required' => 'Please upload the completed Informed Consent Assessment form.',
            'assessment_protocol_file.required' => 'Please upload the completed Protocol Assessment form.',
            'assessment_recommendation.required' => 'Please select your official evaluation recommendation.',
            'assessment_recommendation.in' => 'Please select a valid recommendation (Approve, Revisions Required, or Disapprove).',
        ]);

        app(ReviewerAssignmentService::class)->submitEvaluation(
            $assignment,
            $this->assessment_consent_file,
            $this->assessment_protocol_file,
            $this->assessment_recommendation,
            $this->assessment_notes ?: null
        );

        $this->reset(['assessment_consent_file', 'assessment_protocol_file', 'assessment_recommendation', 'assessment_notes']);
        $this->refresh();
        session()->flash('success', 'Review evaluation and assessment forms successfully submitted.');
    }

    public function render()
    {
        $queueService = app(EthicsRequestQueueService::class);
        $queuePosition = $queueService->getQueuePosition($this->request);
        $estimatedDecisionDate = $queueService->getEstimatedDecisionDate($this->request);

        $domainKey = $this->request->research_domain?->value;
        $matchingReviewers = User::role('committee')
            ->where('is_active', true)
            ->where('id', '!=', $this->request->student_id)
            ->get()
            ->filter(function (User $user) use ($domainKey) {
                if (! $domainKey) {
                    return true;
                }
                $domains = (array) ($user->specialty_domains ?? []);

                return in_array($domainKey, $domains, true);
            });

        $allCommitteeMembers = User::role('committee')
            ->where('is_active', true)
            ->where('id', '!=', $this->request->student_id)
            ->get();

        $matchingIds = $matchingReviewers->pluck('id')->all();
        $otherCommitteeMembers = $allCommitteeMembers->reject(function ($user) use ($matchingIds) {
            return in_array($user->id, $matchingIds);
        });

        return view('livewire.request-detail', [
            'queuePosition' => $queuePosition,
            'estimatedDecisionDate' => $estimatedDecisionDate,
            'matchingReviewers' => $matchingReviewers,
            'otherCommitteeMembers' => $otherCommitteeMembers,
            'allCommitteeMembers' => $allCommitteeMembers,
        ])->layout('layouts.app', [
            'header' => 'Request Details: '.($this->request->control_number ?? 'ERB Review'),
        ]);
    }
}
