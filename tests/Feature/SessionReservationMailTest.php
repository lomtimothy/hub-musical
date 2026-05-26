<?php

use App\Jobs\SendSessionReservedEmail;
use App\Models\Studio;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;

use function Pest\Laravel\actingAs;

uses(RefreshDatabase::class);

test('reservation dispatches job to send custom email to producer', function () {
    Queue::fake();

    $producer = User::factory()->producer()->create([
        'email' => 'producer@example.com',
    ]);

    $musician = User::factory()->musician()->create();

    $studio = Studio::factory()->active()->create([
        'owner_id' => $producer->id,
    ]);

    $response = actingAs($musician)
        ->post(route('studios.sessions.store', $studio), [
            'title' => 'Grabación de voces',
            'instrument' => 'Voz',
            'payment_split' => 100,
            'starts_at' => now()->addDay()->format('Y-m-d H:i:s'),
            'ends_at' => now()->addDay()->addHours(2)->format('Y-m-d H:i:s'),
            'notes' => 'Sesión creada desde una prueba automatizada.',
        ]);

    $response->assertSessionHasNoErrors();

    Queue::assertPushed(SendSessionReservedEmail::class);
});
