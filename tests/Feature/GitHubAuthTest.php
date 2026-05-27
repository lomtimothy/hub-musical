<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Contracts\Factory as SocialiteFactory;
use Laravel\Socialite\Contracts\Provider;
use Laravel\Socialite\Contracts\User as SocialiteUser;

use function Pest\Laravel\get;

uses(RefreshDatabase::class);

test('github callback creates a new musician user and authenticates them', function () {
    $abstractUser = Mockery::mock(SocialiteUser::class);
    $abstractUser->shouldReceive('getId')->andReturn('github-123');
    $abstractUser->shouldReceive('getName')->andReturn('GitHub User');
    $abstractUser->shouldReceive('getNickname')->andReturn('githubuser');
    $abstractUser->shouldReceive('getEmail')->andReturn('github@example.com');
    $abstractUser->shouldReceive('getAvatar')->andReturn('https://example.com/avatar.png');

    $provider = Mockery::mock(Provider::class);
    $provider->shouldReceive('user')->andReturn($abstractUser);

    $socialite = Mockery::mock(SocialiteFactory::class);
    $socialite->shouldReceive('driver')
        ->with('github')
        ->andReturn($provider);

    app()->instance(SocialiteFactory::class, $socialite);

    $response = get(route('auth.github.callback'));

    $response->assertRedirect(route('studios.index'));

    expect(Auth::check())->toBeTrue();

    $user = User::query()
        ->where('email', 'github@example.com')
        ->first();

    expect($user)->not->toBeNull();
    expect($user->name)->toBe('GitHub User');
    expect($user->role)->toBe('musician');
    expect($user->github_id)->toBe('github-123');
    expect($user->github_avatar_url)->toBe('https://example.com/avatar.png');
});
