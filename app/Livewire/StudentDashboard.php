<?php

namespace App\Livewire;

use App\Enums\RequestStatus;
use App\Models\EthicsRequest;
use Livewire\Component;

class StudentDashboard extends Component
{
    public bool $showFeedbackPopup = false;

    public bool $showApprovalPopup = false;

    public ?EthicsRequest $popupRequest = null;

    public function mount()
    {
        // 1. Check for newly approved request
        $approvedRequest = EthicsRequest::with(['department'])
            ->where('student_id', auth()->id())
            ->where('status', RequestStatus::Approved)
            ->latest('updated_at')
            ->first();

        if ($approvedRequest && ! session()->has('dismissed_approval_popup_'.$approvedRequest->id)) {
            $this->popupRequest = $approvedRequest;
            $this->showApprovalPopup = true;

            return;
        }

        // 2. Check for request needing student revision
        $revisionRequest = EthicsRequest::with(['requestDocuments.documentType'])
            ->where('student_id', auth()->id())
            ->where('status', RequestStatus::RevisionsRequested)
            ->latest('last_activity_at')
            ->first();

        if ($revisionRequest && ! session()->has('dismissed_revision_popup_'.$revisionRequest->id)) {
            $this->popupRequest = $revisionRequest;
            $this->showFeedbackPopup = true;
        }
    }

    public function claimSampleRequest(): void
    {
        if (! config('app.demo_mode')) {
            abort(403, 'Demo features are disabled.');
        }

        $userId = auth()->id();
        $sample = EthicsRequest::first();
        if ($sample) {
            $sample->update(['student_id' => $userId]);
            session()->flash('success', 'Sample thesis ethics request has been attached to your Google account.');
        }
    }

    public function dismissApprovalPopup()
    {
        if ($this->popupRequest) {
            session()->put('dismissed_approval_popup_'.$this->popupRequest->id, true);
        }
        $this->showApprovalPopup = false;
    }

    public function dismissPopup()
    {
        if ($this->popupRequest) {
            session()->put('dismissed_revision_popup_'.$this->popupRequest->id, true);
        }
        $this->showFeedbackPopup = false;
    }

    public function render()
    {
        $requests = EthicsRequest::with(['department', 'requestDocuments.documentType', 'requestDocuments.versions'])
            ->where('student_id', auth()->id())
            ->latest()
            ->get();

        return view('livewire.student-dashboard', [
            'requests' => $requests,
        ])->layout('layouts.app', ['header' => 'My Ethics Requests']);
    }
}
