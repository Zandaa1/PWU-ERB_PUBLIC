<?php

namespace App\Livewire;

use App\Contracts\DocumentStorage;
use App\Enums\DocumentStatus;
use App\Enums\RequestStatus;
use App\Mail\DocumentCommentAddedMail;
use App\Models\Comment;
use App\Models\DocumentVersion;
use App\Models\Notification;
use App\Models\RequestDocument;
use App\Models\StatusLog;
use App\Models\User;
use App\Services\DocumentService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Livewire\WithFileUploads;

class DocumentViewer extends Component
{
    use WithFileUploads;

    public RequestDocument $requestDocument;

    public ?DocumentVersion $selectedVersion = null;

    public string $newComment = '';

    public ?int $replyToCommentId = null;

    public string $replyText = '';

    // Modal States
    public bool $showRevisionModal = false;

    public bool $showNoCommentWarningModal = false;

    public bool $showApproveConfirmationModal = false;

    public bool $showVersionHistoryModal = false;

    public $newVersionFile;

    public string $changeNotes = '';

    public function mount(int $requestId, int $documentId)
    {
        $this->requestDocument = RequestDocument::with([
            'ethicsRequest.student',
            'documentType',
            'versions.uploader',
            'versions.comments.commenter',
            'versions.comments.documentVersion',
            'versions.comments.replies.commenter',
            'versions.comments.replies.documentVersion',
        ])->where('ethics_request_id', $requestId)->findOrFail($documentId);

        $this->selectedVersion = $this->requestDocument->latestVersion;

        if (request()->query('action') === 'revision' && auth()->id() === $this->requestDocument->ethicsRequest->student_id) {
            $this->showRevisionModal = true;
        }
    }

    public function selectVersion(int $versionId)
    {
        $this->selectedVersion = DocumentVersion::with([
            'uploader',
            'comments.commenter',
            'comments.documentVersion',
            'comments.replies.commenter',
            'comments.replies.documentVersion',
        ])->findOrFail($versionId);

        $this->showVersionHistoryModal = false;
        $this->dispatch('pdfVersionChanged', versionId: $versionId);
    }

    protected function createCommentNotifications(?Comment $comment = null): void
    {
        $docName = $this->requestDocument->documentType->name;
        $link = "/requests/{$this->requestDocument->ethics_request_id}/documents/{$this->requestDocument->id}";
        $commenter = auth()->user();

        if ($commenter->hasAnyRole(['committee', 'admin'])) {
            // Notify the student when committee comments
            Notification::create([
                'user_id' => $this->requestDocument->ethicsRequest->student_id,
                'type' => 'committee_comment',
                'message' => 'New committee feedback on document: '.$docName,
                'link' => $link,
            ]);

            $student = $this->requestDocument->ethicsRequest->student;
            if ($comment && $student && ! empty($student->email)) {
                Mail::to($student->email)->send(new DocumentCommentAddedMail($this->requestDocument, $comment));
            }
        } else {
            // Notify committee members and admins when student comments
            $recipients = User::role(['committee', 'admin'])->get();
            foreach ($recipients as $recipient) {
                if ($recipient->id !== $commenter->id) {
                    Notification::create([
                        'user_id' => $recipient->id,
                        'type' => 'student_comment',
                        'message' => 'Student '.$commenter->name.' commented on '.$docName,
                        'link' => $link,
                    ]);
                }
            }
        }
    }

    public function addComment()
    {
        $this->validate([
            'newComment' => 'required|string|min:2',
        ]);

        $comment = DB::transaction(function () {
            return Comment::create([
                'document_version_id' => $this->selectedVersion->id,
                'commenter_id' => auth()->id(),
                'comment_text' => $this->newComment,
                'resolved' => false,
            ]);
        });

        $this->createCommentNotifications($comment);

        $this->newComment = '';
        $this->selectedVersion->refresh();
    }

    public function addReply(int $parentCommentId)
    {
        $this->validate([
            'replyText' => 'required|string|min:2',
        ]);

        $comment = DB::transaction(function () use ($parentCommentId) {
            return Comment::create([
                'document_version_id' => $this->selectedVersion->id,
                'commenter_id' => auth()->id(),
                'parent_id' => $parentCommentId,
                'comment_text' => $this->replyText,
                'resolved' => false,
            ]);
        });

        $this->createCommentNotifications($comment);

        $this->replyText = '';
        $this->replyToCommentId = null;
        $this->selectedVersion->refresh();
    }

    public function toggleResolve(int $commentId)
    {
        if (! auth()->user()->hasAnyRole(['committee', 'admin'])) {
            return;
        }

        $comment = Comment::findOrFail($commentId);
        $comment->update(['resolved' => ! $comment->resolved]);
        $this->selectedVersion->refresh();
    }

    public function promptApproveDocument()
    {
        if (! auth()->user()->hasAnyRole(['committee', 'admin'])) {
            return;
        }

        if ($this->requestDocument->status === DocumentStatus::Approved) {
            session()->flash('info', 'This document is already approved.');

            return;
        }

        $this->showApproveConfirmationModal = true;
    }

    public function closeApproveModal()
    {
        $this->showApproveConfirmationModal = false;
    }

    public function confirmApproveDocument()
    {
        $this->showApproveConfirmationModal = false;
        $this->approveDocument();
    }

    public function approveDocument()
    {
        if (! auth()->user()->hasAnyRole(['committee', 'admin'])) {
            return;
        }

        if ($this->requestDocument->status === DocumentStatus::Approved) {
            session()->flash('info', 'This document is already approved.');

            return;
        }

        $this->showApproveConfirmationModal = false;
        app(DocumentService::class)->approveDocument($this->requestDocument, auth()->user());
        $this->requestDocument->refresh();
        session()->flash('success', 'Document approved successfully.');
    }

    public function requestRevision()
    {
        if (! auth()->user()->hasAnyRole(['committee', 'admin'])) {
            return;
        }

        if (! $this->selectedVersion || $this->selectedVersion->comments()->count() === 0) {
            $this->showNoCommentWarningModal = true;

            return;
        }

        $this->executeRequestRevision();
    }

    public function confirmRequestRevision()
    {
        $this->showNoCommentWarningModal = false;
        $this->executeRequestRevision();
    }

    private function executeRequestRevision()
    {
        app(DocumentService::class)->requestRevision($this->requestDocument, auth()->user());
        $this->requestDocument->refresh();
        session()->flash('success', 'Status set to Revisions Requested.');
    }

    public function isImageFile(?DocumentVersion $version = null): bool
    {
        $v = $version ?? $this->selectedVersion;
        if (! $v) {
            return false;
        }
        $path = strtolower($v->file_path ?? '');
        $name = strtolower($v->file_name ?? '');

        $imageExtensions = ['.jpg', '.jpeg', '.png', '.webp', '.gif', '.bmp'];
        foreach ($imageExtensions as $ext) {
            if (str_ends_with($path, $ext) || str_ends_with($name, $ext)) {
                return true;
            }
        }

        return false;
    }

    public function submitRevision()
    {
        $this->validate([
            'newVersionFile' => 'required|file|mimes:pdf,jpg,jpeg,png,webp|max:10240',
            'changeNotes' => 'required|string|min:5',
        ], [
            'newVersionFile.mimes' => 'Document must be a PDF file or an Image (JPG, PNG, WEBP).',
            'newVersionFile.max' => 'File size must not exceed 10MB.',
        ]);

        $nextVersionNum = $this->requestDocument->versions->max('version_number') + 1;
        $path = app(DocumentStorage::class)->store($this->newVersionFile, $this->requestDocument->ethics_request_id, $this->requestDocument->document_type_id, $nextVersionNum);

        try {
            $newVersion = DB::transaction(function () use ($path, $nextVersionNum) {
                $version = DocumentVersion::create([
                    'request_document_id' => $this->requestDocument->id,
                    'file_path' => $path,
                    'version_number' => $nextVersionNum,
                    'change_notes' => $this->changeNotes,
                    'uploaded_by' => auth()->id(),
                ]);

                $this->requestDocument->update(['status' => DocumentStatus::Pending]);

                $ethicsRequest = $this->requestDocument->ethicsRequest;
                $oldReqStatus = $ethicsRequest->status;
                if ($oldReqStatus === RequestStatus::RevisionsRequested) {
                    $ethicsRequest->update([
                        'status' => RequestStatus::UnderReview,
                        'last_activity_at' => now(),
                    ]);

                    StatusLog::create([
                        'ethics_request_id' => $ethicsRequest->id,
                        'old_status' => RequestStatus::RevisionsRequested->value,
                        'new_status' => RequestStatus::UnderReview->value,
                        'changed_by' => auth()->id(),
                        'notes' => 'Status automatically updated to Under Review upon uploading revision for '.$this->requestDocument->documentType->name,
                    ]);

                    $reviewers = User::role('committee')->get();
                    foreach ($reviewers as $reviewer) {
                        Notification::create([
                            'user_id' => $reviewer->id,
                            'type' => 'revision_submitted',
                            'message' => 'Student uploaded a revised version for '.$this->requestDocument->documentType->name.' on protocol: '.$ethicsRequest->title,
                            'link' => '/requests/'.$ethicsRequest->id.'/documents/'.$this->requestDocument->id,
                            'is_read' => false,
                        ]);
                    }
                } else {
                    $ethicsRequest->update(['last_activity_at' => now()]);
                }

                StatusLog::create([
                    'ethics_request_id' => $this->requestDocument->ethics_request_id,
                    'old_status' => $oldReqStatus->value,
                    'new_status' => DocumentStatus::Pending->value,
                    'changed_by' => auth()->id(),
                    'notes' => 'Uploaded version v'.$nextVersionNum.' of '.$this->requestDocument->documentType->name.' with change notes: "'.$this->changeNotes.'"',
                ]);

                return $version;
            });
        } catch (\Throwable $e) {
            Storage::disk('local')->delete($path);
            throw $e;
        }

        $this->showRevisionModal = false;
        $this->newVersionFile = null;
        $this->changeNotes = '';
        $this->requestDocument->refresh();
        $this->selectedVersion = $newVersion;
        $this->dispatch('pdfVersionChanged', versionId: $newVersion->id);

        session()->flash('success', 'New version uploaded successfully.');
    }

    public function render()
    {
        return view('livewire.document-viewer')->layout('layouts.app', [
            'header' => 'Review: '.$this->requestDocument->documentType->name,
        ]);
    }
}
