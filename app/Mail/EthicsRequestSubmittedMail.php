<?php

namespace App\Mail;

use App\Models\EthicsRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class EthicsRequestSubmittedMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public EthicsRequest $ethicsRequest
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Ethics Review Submission Acknowledgment - '.$this->ethicsRequest->control_number,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.requests.submitted',
            with: [
                'ethicsRequest' => $this->ethicsRequest,
            ],
        );
    }
}
