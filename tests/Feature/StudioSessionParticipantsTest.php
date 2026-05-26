<?php

use App\Models\Studio;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\assertDatabaseHas;

uses(RefreshDatabase::class);

test('musician can reserve a session with additional participants and split information', function () {
    $producer = User::factory()->producer()->create();
    $booker = User::factory()->musician()->create();
    $guestMusician = User::factory()->musician()->create();

    $studio = Studio::factory()->active()->create([
        'owner_id' => $producer->id,
        'hourly_rate' => 500,
    ]);

    $response = actingAs($booker)
        ->post(route('studios.sessions.store', $studio), [
            'title' => 'Sesión colaborativa',
            'instrument' => 'Voz',
            'payment_split' => 60,
            'starts_at' => now()->addDay()->format('Y-m-d H:i:s'),
            'ends_at' => now()->addDay()->addHours(2)->format('Y-m-d H:i:s'),
            'notes' => 'Prueba de músicos adicionales.',
            'participants' => [
                [
                    'user_id' => $guestMusician->id,
                    'instrument' => 'Guitarra',
                    'payment_split' => 40,
                ],
            ],
        ]);

    $response->assertSessionHasNoErrors();

    assertDatabaseHas('studio_sessions', [
        'title' => 'Sesión colaborativa',
        'booked_by' => $booker->id,
    ]);

    assertDatabaseHas('studio_session_user', [
        'user_id' => $booker->id,
        'instrument' => 'Voz',
        'payment_split' => 60,
    ]);

    assertDatabaseHas('studio_session_user', [
        'user_id' => $guestMusician->id,
        'instrument' => 'Guitarra',
        'payment_split' => 40,
    ]);
});

test('session reservation fails when split total is not one hundred percent', function () {
    $producer = User::factory()->producer()->create();
    $booker = User::factory()->musician()->create();
    $guestMusician = User::factory()->musician()->create();

    $studio = Studio::factory()->active()->create([
        'owner_id' => $producer->id,
    ]);

    $response = actingAs($booker)
        ->post(route('studios.sessions.store', $studio), [
            'title' => 'Sesión con split incorrecto',
            'instrument' => 'Voz',
            'payment_split' => 70,
            'starts_at' => now()->addDay()->format('Y-m-d H:i:s'),
            'ends_at' => now()->addDay()->addHours(2)->format('Y-m-d H:i:s'),
            'participants' => [
                [
                    'user_id' => $guestMusician->id,
                    'instrument' => 'Guitarra',
                    'payment_split' => 20,
                ],
            ],
        ]);

    $response->assertSessionHasErrors('payment_split');
});

test('musician can reserve a session with up to ten additional participants', function () {
    $producer = User::factory()->producer()->create();
    $booker = User::factory()->musician()->create();

    $studio = Studio::factory()->active()->create([
        'owner_id' => $producer->id,
        'hourly_rate' => 500,
    ]);

    $guestMusicians = User::factory()
        ->count(10)
        ->musician()
        ->create();

    $participants = $guestMusicians
        ->values()
        ->map(fn (User $musician, int $index) => [
            'user_id' => $musician->id,
            'instrument' => 'Instrumento '.$index,
            'payment_split' => 5,
        ])
        ->all();

    $response = actingAs($booker)
        ->post(route('studios.sessions.store', $studio), [
            'title' => 'Sesión con diez músicos adicionales',
            'instrument' => 'Dirección musical',
            'payment_split' => 50,
            'starts_at' => now()->addDay()->format('Y-m-d H:i:s'),
            'ends_at' => now()->addDay()->addHours(2)->format('Y-m-d H:i:s'),
            'notes' => 'Prueba de máximo de participantes.',
            'participants' => $participants,
        ]);

    $response->assertSessionHasNoErrors();

    assertDatabaseHas('studio_sessions', [
        'title' => 'Sesión con diez músicos adicionales',
        'booked_by' => $booker->id,
    ]);

    foreach ($guestMusicians as $index => $guestMusician) {
        assertDatabaseHas('studio_session_user', [
            'user_id' => $guestMusician->id,
            'instrument' => 'Instrumento '.$index,
            'payment_split' => 5,
        ]);
    }
});

test('session reservation fails with more than ten additional participants', function () {
    $producer = User::factory()->producer()->create();
    $booker = User::factory()->musician()->create();

    $studio = Studio::factory()->active()->create([
        'owner_id' => $producer->id,
    ]);

    $guestMusicians = User::factory()
        ->count(11)
        ->musician()
        ->create();

    $participants = $guestMusicians
        ->map(fn (User $musician) => [
            'user_id' => $musician->id,
            'instrument' => 'Instrumento',
            'payment_split' => 4,
        ])
        ->all();

    $response = actingAs($booker)
        ->post(route('studios.sessions.store', $studio), [
            'title' => 'Sesión con demasiados músicos',
            'instrument' => 'Voz',
            'payment_split' => 56,
            'starts_at' => now()->addDay()->format('Y-m-d H:i:s'),
            'ends_at' => now()->addDay()->addHours(2)->format('Y-m-d H:i:s'),
            'participants' => $participants,
        ]);

    $response->assertSessionHasErrors('participants');
});
