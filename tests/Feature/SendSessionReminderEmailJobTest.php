<?php

use App\Jobs\SendSessionReminderEmail;
use App\Mail\SessionReminderMail;
use App\Models\Studio;
use App\Models\StudioSession;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;

uses(RefreshDatabase::class);

test('send session reminder email job sends reminder mail to booker', function () {
    Mail::fake();

    $producer = User::factory()->producer()->create([
        'email' => 'producer@example.com',
    ]);

    $musician = User::factory()->musician()->create([
        'email' => 'musician@example.com',
    ]);

    $studio = Studio::factory()->active()->create([
        'owner_id' => $producer->id,
    ]);

    $studioSession = StudioSession::factory()->confirmed()->create([
        'studio_id' => $studio->id,
        'booked_by' => $musician->id,
    ]);

    $job = new SendSessionReminderEmail($studioSession->id);

    $job->handle();

    Mail::assertSent(SessionReminderMail::class, function (SessionReminderMail $mail) use ($musician) {
        return $mail->hasTo($musician->email);
    });
});
