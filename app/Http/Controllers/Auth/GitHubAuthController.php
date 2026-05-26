<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;
use Symfony\Component\HttpFoundation\RedirectResponse as SymfonyRedirectResponse;

class GitHubAuthController extends Controller
{
    public function redirect(): SymfonyRedirectResponse|RedirectResponse
    {
        return Socialite::driver('github')->redirect();
    }

    public function callback(): RedirectResponse
    {
        $githubUser = Socialite::driver('github')->user();

        $email = $githubUser->getEmail()
            ?? 'github-'.$githubUser->getId().'@hubmusical.test';

        $user = User::query()
            ->where('github_id', $githubUser->getId())
            ->orWhere('email', $email)
            ->first();

        if (! $user) {
            $user = User::create([
                'name' => $githubUser->getName() ?: $githubUser->getNickname() ?: 'Usuario GitHub',
                'email' => $email,
                'password' => Hash::make(Str::password(32)),
                'role' => 'musician',
                'github_id' => $githubUser->getId(),
                'github_avatar_url' => $githubUser->getAvatar(),
                'email_verified_at' => now(),
            ]);
        } else {
            $user->forceFill([
                'github_id' => $user->github_id ?: $githubUser->getId(),
                'github_avatar_url' => $githubUser->getAvatar(),
                'email_verified_at' => $user->email_verified_at ?: now(),
            ])->save();
        }

        Auth::login($user, remember: true);

        return redirect()->intended(route('dashboard'));
    }
}
