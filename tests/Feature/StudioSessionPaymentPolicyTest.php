<?php

use App\Models\Studio;
use App\Models\StudioSession;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('booker can pay an unpaid studio session', function () {
    $producer = User::factory()->producer()->create();
    $musician = User::factory()->musician()->create();

    $studio = Studio::factory()->active()->create([
        'owner_id' => $producer->id,
    ]);

    $studioSession = StudioSession::factory()->confirmed()->create([
        'studio_id' => $studio->id,
        'booked_by' => $musician->id,
        'payment_status' => 'unpaid',
    ]);

    expect($musician->can('pay', $studioSession))->toBeTrue();
});

test('producer cannot pay a session booked by another user', function () {
    $producer = User::factory()->producer()->create();
    $musician = User::factory()->musician()->create();

    $studio = Studio::factory()->active()->create([
        'owner_id' => $producer->id,
    ]);

    $studioSession = StudioSession::factory()->confirmed()->create([
        'studio_id' => $studio->id,
        'booked_by' => $musician->id,
        'payment_status' => 'unpaid',
    ]);

    expect($producer->can('pay', $studioSession))->toBeFalse();
});

test('booker cannot pay an already paid session', function () {
    $producer = User::factory()->producer()->create();
    $musician = User::factory()->musician()->create();

    $studio = Studio::factory()->active()->create([
        'owner_id' => $producer->id,
    ]);

    $studioSession = StudioSession::factory()->confirmed()->create([
        'studio_id' => $studio->id,
        'booked_by' => $musician->id,
        'payment_status' => 'paid',
        'paid_at' => now(),
    ]);

    expect($musician->can('pay', $studioSession))->toBeFalse();
});

test('booker cannot pay a cancelled session', function () {
    $producer = User::factory()->producer()->create();
    $musician = User::factory()->musician()->create();

    $studio = Studio::factory()->active()->create([
        'owner_id' => $producer->id,
    ]);

    $studioSession = StudioSession::factory()->create([
        'studio_id' => $studio->id,
        'booked_by' => $musician->id,
        'status' => 'cancelled',
        'payment_status' => 'unpaid',
    ]);

    expect($musician->can('pay', $studioSession))->toBeFalse();
});
