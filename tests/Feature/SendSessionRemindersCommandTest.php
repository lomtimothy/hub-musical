<?php

use App\Jobs\SendSessionReminderEmail;
use App\Models\Studio;
use App\Models\StudioSession;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;

use function Pest\Laravel\artisan;

uses(RefreshDatabase::class);

test('command dispatches reminder jobs for tomorrow sessions', function () {
    Queue::fake();

    $producer = User::factory()->producer()->create();
    $musician = User::factory()->musician()->create();

    $studio = Studio::factory()->active()->create([
        'owner_id' => $producer->id,
    ]);

    $studioSession = StudioSession::factory()->confirmed()->create([
        'studio_id' => $studio->id,
        'booked_by' => $musician->id,
        'starts_at' => now()->addDay()->setTime(12, 0),
        'ends_at' => now()->addDay()->setTime(14, 0),
        'reminder_sent_at' => null,
    ]);

    artisan('sessions:send-reminders')
        ->expectsOutput('Session reminders dispatched: 1')
        ->assertSuccessful();

    Queue::assertPushed(SendSessionReminderEmail::class);

    expect($studioSession->fresh()->reminder_sent_at)->not->toBeNull();
});

test('command does not dispatch duplicate reminders', function () {
    Queue::fake();

    $producer = User::factory()->producer()->create();
    $musician = User::factory()->musician()->create();

    $studio = Studio::factory()->active()->create([
        'owner_id' => $producer->id,
    ]);

    StudioSession::factory()->confirmed()->create([
        'studio_id' => $studio->id,
        'booked_by' => $musician->id,
        'starts_at' => now()->addDay()->setTime(12, 0),
        'ends_at' => now()->addDay()->setTime(14, 0),
        'reminder_sent_at' => now(),
    ]);

    artisan('sessions:send-reminders')
        ->expectsOutput('Session reminders dispatched: 0')
        ->assertSuccessful();

    Queue::assertNotPushed(SendSessionReminderEmail::class);
});
