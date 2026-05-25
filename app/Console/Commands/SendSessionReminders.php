<?php

namespace App\Console\Commands;

use App\Jobs\SendSessionReminderEmail;
use App\Models\StudioSession;
use Illuminate\Console\Command;

class SendSessionReminders extends Command
{
    protected $signature = 'sessions:send-reminders';

    protected $description = 'Send reminder emails for studio sessions scheduled for tomorrow.';

    public function handle(): int
    {
        $tomorrowStart = now()->addDay()->startOfDay();
        $tomorrowEnd = now()->addDay()->endOfDay();

        $sessions = StudioSession::query()
            ->with(['studio.owner', 'booker'])
            ->whereBetween('starts_at', [$tomorrowStart, $tomorrowEnd])
            ->where(function ($query): void {
                $query
                    ->where('status', 'pending')
                    ->orWhere('status', 'confirmed');
            })
            ->whereNull('reminder_sent_at')
            ->get();

        $sessions->each(function (StudioSession $studioSession): void {
            SendSessionReminderEmail::dispatch($studioSession->id);

            $studioSession->forceFill([
                'reminder_sent_at' => now(),
            ])->save();
        });

        $this->info("Session reminders dispatched: {$sessions->count()}");

        return self::SUCCESS;
    }
}
