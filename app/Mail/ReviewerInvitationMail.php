<?php

namespace App\Mail;

use App\Models\EthicsRequest;
use App\Models\ReviewerAssignment;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ReviewerInvitationMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public EthicsRequest $ethicsRequest,
        public ReviewerAssignment $assignment
    ) {}

    public function envelope(): Envelope
    {
        $identifier = $this->ethicsRequest->control_number ?: ('Request #'.$this->ethicsRequest->id);

        return new Envelope(
            subject: 'DEMO ERB Reviewer Invitation - '.$identifier,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.reviewer-invitation',
            with: [
                'ethicsRequest' => $this->ethicsRequest,
                'assignment' => $this->assignment,
            ],
        );
    }
}
