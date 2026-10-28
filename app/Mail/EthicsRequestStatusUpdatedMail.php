<?php

namespace App\Mail;

use App\Models\EthicsRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class EthicsRequestStatusUpdatedMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public EthicsRequest $ethicsRequest,
        public ?string $remarks = null
    ) {}

    public function envelope(): Envelope
    {
        $statusLabel = strtoupper($this->ethicsRequest->status->label());

        return new Envelope(
            subject: 'Ethics Review Decision Update: '.$statusLabel.' - '.$this->ethicsRequest->control_number,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.requests.status-updated',
            with: [
                'ethicsRequest' => $this->ethicsRequest,
                'remarks' => $this->remarks,
            ],
        );
    }
}
