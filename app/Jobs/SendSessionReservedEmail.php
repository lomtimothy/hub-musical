<?php

namespace App\Jobs;

use App\Mail\SessionReservedMail;
use App\Models\StudioSession;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Mail;

class SendSessionReservedEmail implements ShouldQueue
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

        Mail::to($studioSession->studio->owner->email)
            ->send(new SessionReservedMail($studioSession));
    }
}
