<?php

use App\Http\Controllers\Auth\GitHubAuthController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\SplitSheetController;
use App\Http\Controllers\StudioController;
use App\Http\Controllers\StudioSessionController;
use App\Http\Controllers\TrackController;
use Illuminate\Support\Facades\Route;

Route::view('/', 'welcome')->name('home');

Route::post('stripe/webhook', [PaymentController::class, 'webhook'])
    ->name('stripe.webhook');

Route::get('auth/github/redirect', [GitHubAuthController::class, 'redirect'])
    ->name('auth.github.redirect');

Route::get('auth/github/callback', [GitHubAuthController::class, 'callback'])
    ->name('auth.github.callback');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::view('dashboard', 'dashboard')->name('dashboard');
});

Route::middleware(['auth'])->group(function () {
    Route::get('studios/{studio}/sessions/create', [StudioSessionController::class, 'create'])
        ->name('studios.sessions.create');

    Route::post('studios/{studio}/sessions', [StudioSessionController::class, 'store'])
        ->name('studios.sessions.store');

    Route::get('studio-sessions/{studioSession}', [StudioSessionController::class, 'show'])
        ->name('studio-sessions.show');

    Route::delete('studio-sessions/{studioSession}', [StudioSessionController::class, 'destroy'])
        ->name('studio-sessions.destroy');

    Route::get('studio-sessions/{studioSession}/split-sheet', [SplitSheetController::class, 'download'])
        ->name('studio-sessions.split-sheet');

    Route::post('studio-sessions/{studioSession}/checkout', [PaymentController::class, 'checkout'])
        ->name('payments.checkout');

    Route::get('payments/success', [PaymentController::class, 'success'])
        ->name('payments.success');

    Route::post('studio-sessions/{studioSession}/tracks', [TrackController::class, 'store'])
        ->name('studio-sessions.tracks.store');

    Route::get('tracks/{track}/download', [TrackController::class, 'download'])
        ->name('tracks.download');

    Route::delete('tracks/{track}', [TrackController::class, 'destroy'])
        ->name('tracks.destroy');

    Route::resource('studios', StudioController::class)
        ->except(['index', 'show']);
});

Route::resource('studios', StudioController::class)
    ->only(['index', 'show']);

require __DIR__.'/settings.php';
