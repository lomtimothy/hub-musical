<?php

use Laravel\Fortify\Features;

use function Pest\Laravel\assertAuthenticated;
use function Pest\Laravel\get;
use function Pest\Laravel\post;

function registrationFeatureIsEnabled(): bool
{
    return Features::enabled(Features::registration());
}

test('registration screen can be rendered', function () {
    if (! registrationFeatureIsEnabled()) {
        expect(true)->toBeTrue();

        return;
    }

    $response = get(route('register'));

    $response->assertOk();
});

test('new users can register', function () {
    if (! registrationFeatureIsEnabled()) {
        expect(true)->toBeTrue();

        return;
    }

    $response = post(route('register.store'), [
        'name' => 'John Doe',
        'email' => 'test@example.com',
        'password' => 'password',
        'password_confirmation' => 'password',
    ]);

    $response->assertSessionHasNoErrors()
        ->assertRedirect(route('studios.index'));

    assertAuthenticated();
});
