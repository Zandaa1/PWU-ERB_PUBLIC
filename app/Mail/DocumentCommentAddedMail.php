<?php

namespace App\Mail;

use App\Models\Comment;
use App\Models\RequestDocument;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class DocumentCommentAddedMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public RequestDocument $document,
        public Comment $comment
    ) {}

    public function envelope(): Envelope
    {
        $docName = $this->document->documentType->name ?? 'Document';
        $controlNo = $this->document->ethicsRequest->control_number;

        return new Envelope(
            subject: 'New Reviewer Feedback: '.$docName.' - '.$controlNo,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.requests.comment-added',
            with: [
                'document' => $this->document,
                'comment' => $this->comment,
            ],
        );
    }
}
