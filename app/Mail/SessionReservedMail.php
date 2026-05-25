<?php

namespace App\Mail;

use App\Models\StudioSession;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class SessionReservedMail extends Mailable
{
    use Queueable;
    use SerializesModels;

    public function __construct(
        public StudioSession $studioSession
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Nueva reserva en '.$this->studioSession->studio->name,
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.sessions.reserved',
            with: [
                'studioSession' => $this->studioSession,
                'studio' => $this->studioSession->studio,
                'booker' => $this->studioSession->booker,
            ],
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
