<?php

namespace App\Jobs;

use App\Mail\SessionReminderMail;
use App\Models\StudioSession;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Mail;

class SendSessionReminderEmail implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public int $studioSessionId
    ) {}

    public function handle(): void
    {
        $studioSession = StudioSession::query()
            ->with(['studio.owner', 'booker'])
            ->findOrFail($this->studioSessionId);

        Mail::to($studioSession->booker->email)
            ->cc($studioSession->studio->owner->email)
            ->send(new SessionReminderMail($studioSession));
    }
}
