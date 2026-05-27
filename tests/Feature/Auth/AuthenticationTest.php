<?php

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Laravel\Fortify\Features;

use function Pest\Laravel\assertAuthenticated;
use function Pest\Laravel\assertGuest;
use function Pest\Laravel\get;
use function Pest\Laravel\post;

test('login screen can be rendered', function () {
    $response = get(route('login'));

    $response->assertOk();
});

test('users can authenticate using the login screen', function () {
    $user = User::factory()->create();

    $response = post(route('login.store'), [
        'email' => $user->email,
        'password' => 'password',
    ]);

    $response
        ->assertSessionHasNoErrors()
        ->assertRedirect(route('studios.index'));

    assertAuthenticated();
});

test('users can not authenticate with invalid password', function () {
    $user = User::factory()->create();

    $response = post(route('login.store'), [
        'email' => $user->email,
        'password' => 'wrong-password',
    ]);

    $response->assertSessionHasErrorsIn('email');

    assertGuest();
});

test('users with two factor enabled are redirected to two factor challenge', function () {
    if (! Features::enabled(Features::twoFactorAuthentication())) {
        expect(true)->toBeTrue();

        return;
    }

    Features::twoFactorAuthentication([
        'confirm' => true,
        'confirmPassword' => true,
    ]);

    $user = User::factory()->withTwoFactor()->create();

    $response = post(route('login.store'), [
        'email' => $user->email,
        'password' => 'password',
    ]);

    $response->assertRedirect(route('two-factor.login'));

    assertGuest();
});

test('users can logout', function () {
    $user = User::factory()->create();

    Auth::login($user);

    $response = post(route('logout'));

    $response->assertRedirect(route('login'));

    assertGuest();
});
