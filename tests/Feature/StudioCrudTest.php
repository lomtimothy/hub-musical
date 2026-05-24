<?php

use App\Models\Studio;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\assertDatabaseHas;
use function Pest\Laravel\assertDatabaseMissing;
use function Pest\Laravel\assertSoftDeleted;
use function Pest\Laravel\get;

uses(RefreshDatabase::class);

test('guest can view studios index and see available studios text', function () {
    Studio::factory()->active()->create([
        'name' => 'Estudio de Prueba',
        'slug' => 'estudio-de-prueba',
    ]);

    $response = get(route('studios.index'));

    $response
        ->assertOk()
        ->assertSee('Estudios Disponibles')
        ->assertSee('Estudio de Prueba');
});

test('producer can create a studio and is redirected to the show page', function () {
    $producer = User::factory()->producer()->create();

    $tag = Tag::create([
        'name' => 'Mezcla',
        'slug' => 'mezcla',
    ]);

    $response = actingAs($producer)
        ->post(route('studios.store'), [
            'name' => 'Estudio Nuevo',
            'description' => 'Este es un estudio profesional creado desde una prueba automatizada.',
            'address' => 'Calle Prueba 123',
            'city' => 'Guadalajara',
            'state' => 'Jalisco',
            'hourly_rate' => 650,
            'capacity' => 5,
            'is_active' => '1',
            'tags' => [$tag->id],
        ]);

    $studio = Studio::query()
        ->where('name', 'Estudio Nuevo')
        ->first();

    expect($studio)->not->toBeNull();

    $response
        ->assertSessionHasNoErrors()
        ->assertRedirect(route('studios.show', $studio));

    assertDatabaseHas('studios', [
        'id' => $studio->id,
        'owner_id' => $producer->id,
        'name' => 'Estudio Nuevo',
        'slug' => 'estudio-nuevo',
        'city' => 'Guadalajara',
        'state' => 'Jalisco',
    ]);

    assertDatabaseHas('taggables', [
        'tag_id' => $tag->id,
        'taggable_id' => $studio->id,
        'taggable_type' => Studio::class,
    ]);
});

test('producer receives validation errors when creating studio with invalid data', function () {
    $producer = User::factory()->producer()->create();

    $response = actingAs($producer)
        ->from(route('studios.create'))
        ->post(route('studios.store'), []);

    $response
        ->assertRedirect(route('studios.create'))
        ->assertSessionHasErrors([
            'name',
            'description',
            'address',
            'city',
            'state',
            'hourly_rate',
            'capacity',
        ]);

    assertDatabaseMissing('studios', [
        'name' => '',
    ]);
});

test('studio owner can delete a studio using soft deletes and is redirected', function () {
    $producer = User::factory()->producer()->create();

    $studio = Studio::factory()->active()->create([
        'owner_id' => $producer->id,
        'name' => 'Estudio Para Eliminar',
        'slug' => 'estudio-para-eliminar',
    ]);

    $response = actingAs($producer)
        ->delete(route('studios.destroy', $studio));

    $response
        ->assertSessionHasNoErrors()
        ->assertRedirect(route('studios.index'));

    assertSoftDeleted('studios', [
        'id' => $studio->id,
    ]);
});
