<?php

use App\Models\Studio;
use App\Models\StudioSession;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

use function Pest\Laravel\actingAs;

uses(RefreshDatabase::class);

test('authorized musician can download split sheet pdf', function () {
    $producer = User::factory()->producer()->create();
    $musician = User::factory()->musician()->create();

    $studio = Studio::factory()->active()->create([
        'owner_id' => $producer->id,
    ]);

    $studioSession = StudioSession::factory()->confirmed()->create([
        'studio_id' => $studio->id,
        'booked_by' => $musician->id,
    ]);

    $studioSession->musicians()->attach($musician->id, [
        'instrument' => 'Voz',
        'payment_split' => 100,
    ]);

    $response = actingAs($musician)
        ->get(route('studio-sessions.split-sheet', $studioSession));

    $response
        ->assertOk()
        ->assertHeader('content-type', 'application/pdf');
});
