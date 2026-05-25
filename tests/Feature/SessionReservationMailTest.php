<?php

use App\Mail\SessionReservedMail;
use App\Models\Studio;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;

use function Pest\Laravel\actingAs;

uses(RefreshDatabase::class);

test('producer receives custom email when musician reserves a session', function () {
    Mail::fake();

    $producer = User::factory()->producer()->create([
        'email' => 'producer@example.com',
    ]);

    $musician = User::factory()->musician()->create();

    Tag::create([
        'name' => 'Mezcla',
        'slug' => 'mezcla',
    ]);

    $studio = Studio::factory()->active()->create([
        'owner_id' => $producer->id,
        'name' => 'Estudio de Prueba',
        'slug' => 'estudio-de-prueba',
        'hourly_rate' => 500,
    ]);

    $response = actingAs($musician)
        ->post(route('studios.sessions.store', $studio), [
            'title' => 'Grabación de voces',
            'instrument' => 'Voz',
            'starts_at' => now()->addDay()->format('Y-m-d H:i:s'),
            'ends_at' => now()->addDay()->addHours(2)->format('Y-m-d H:i:s'),
            'notes' => 'Sesión creada desde una prueba automatizada.',
        ]);

    $response->assertSessionHasNoErrors();

    Mail::assertSent(SessionReservedMail::class, function (SessionReservedMail $mail) use ($producer) {
        return $mail->hasTo($producer->email);
    });
});
