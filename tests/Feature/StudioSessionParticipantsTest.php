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

test('musician can reserve a session up to the studio capacity', function () {
    $producer = User::factory()->producer()->create();
    $booker = User::factory()->musician()->create();

    $studio = Studio::factory()->active()->create([
        'owner_id' => $producer->id,
        'hourly_rate' => 500,
        'capacity' => 20,
    ]);

    $guestMusicians = User::factory()
        ->count(19)
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
            'title' => 'Sesión según capacidad del estudio',
            'instrument' => 'Dirección musical',
            'payment_split' => 5,
            'starts_at' => now()->addDay()->format('Y-m-d H:i:s'),
            'ends_at' => now()->addDay()->addHours(2)->format('Y-m-d H:i:s'),
            'notes' => 'Prueba de capacidad dinámica.',
            'participants' => $participants,
        ]);

    $response->assertSessionHasNoErrors();

    assertDatabaseHas('studio_sessions', [
        'title' => 'Sesión según capacidad del estudio',
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

test('session reservation fails when participants exceed dynamic studio capacity', function () {
    $producer = User::factory()->producer()->create();
    $booker = User::factory()->musician()->create();

    $studio = Studio::factory()->active()->create([
        'owner_id' => $producer->id,
        'capacity' => 5,
    ]);

    $guestMusicians = User::factory()
        ->count(5)
        ->musician()
        ->create();

    $participants = $guestMusicians
        ->values()
        ->map(fn (User $musician, int $index) => [
            'user_id' => $musician->id,
            'instrument' => 'Instrumento '.$index,
            'payment_split' => 10,
        ])
        ->all();

    $response = actingAs($booker)
        ->post(route('studios.sessions.store', $studio), [
            'title' => 'Sesión que supera capacidad dinámica',
            'instrument' => 'Voz',
            'payment_split' => 50,
            'starts_at' => now()->addDay()->format('Y-m-d H:i:s'),
            'ends_at' => now()->addDay()->addHours(2)->format('Y-m-d H:i:s'),
            'participants' => $participants,
        ]);

    $response->assertSessionHasErrors('participants');
});

test('session reservation fails when participants exceed studio capacity', function () {
    $producer = User::factory()->producer()->create();
    $booker = User::factory()->musician()->create();
    $guestOne = User::factory()->musician()->create();
    $guestTwo = User::factory()->musician()->create();

    $studio = Studio::factory()->active()->create([
        'owner_id' => $producer->id,
        'capacity' => 2,
    ]);

    $response = actingAs($booker)
        ->post(route('studios.sessions.store', $studio), [
            'title' => 'Sesión que excede capacidad',
            'instrument' => 'Voz',
            'payment_split' => 50,
            'starts_at' => now()->addDay()->format('Y-m-d H:i:s'),
            'ends_at' => now()->addDay()->addHours(2)->format('Y-m-d H:i:s'),
            'participants' => [
                [
                    'user_id' => $guestOne->id,
                    'instrument' => 'Guitarra',
                    'payment_split' => 25,
                ],
                [
                    'user_id' => $guestTwo->id,
                    'instrument' => 'Batería',
                    'payment_split' => 25,
                ],
            ],
        ]);

    $response->assertSessionHasErrors('participants');
});

test('session reservation succeeds when participants match studio capacity', function () {
    $producer = User::factory()->producer()->create();
    $booker = User::factory()->musician()->create();
    $guestOne = User::factory()->musician()->create();
    $guestTwo = User::factory()->musician()->create();

    $studio = Studio::factory()->active()->create([
        'owner_id' => $producer->id,
        'capacity' => 3,
    ]);

    $response = actingAs($booker)
        ->post(route('studios.sessions.store', $studio), [
            'title' => 'Sesión dentro de capacidad',
            'instrument' => 'Voz',
            'payment_split' => 50,
            'starts_at' => now()->addDay()->format('Y-m-d H:i:s'),
            'ends_at' => now()->addDay()->addHours(2)->format('Y-m-d H:i:s'),
            'participants' => [
                [
                    'user_id' => $guestOne->id,
                    'instrument' => 'Guitarra',
                    'payment_split' => 25,
                ],
                [
                    'user_id' => $guestTwo->id,
                    'instrument' => 'Batería',
                    'payment_split' => 25,
                ],
            ],
        ]);

    $response->assertSessionHasNoErrors();

    assertDatabaseHas('studio_sessions', [
        'title' => 'Sesión dentro de capacidad',
        'booked_by' => $booker->id,
    ]);
});
