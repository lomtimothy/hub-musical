<?php

use App\Jobs\SendSessionReservedEmail;
use App\Mail\SessionReservedMail;
use App\Models\Studio;
use App\Models\StudioSession;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;

uses(RefreshDatabase::class);

test('send session reserved email job sends mailable to studio owner', function () {
    Mail::fake();

    $producer = User::factory()->producer()->create([
        'email' => 'producer@example.com',
    ]);

    $musician = User::factory()->musician()->create();

    $studio = Studio::factory()->active()->create([
        'owner_id' => $producer->id,
    ]);

    $studioSession = StudioSession::factory()->confirmed()->create([
        'studio_id' => $studio->id,
        'booked_by' => $musician->id,
    ]);

    $job = new SendSessionReservedEmail($studioSession->id);

    $job->handle();

    Mail::assertSent(SessionReservedMail::class, function (SessionReservedMail $mail) use ($producer) {
        return $mail->hasTo($producer->email);
    });
});
