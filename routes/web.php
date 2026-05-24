<?php

use App\Http\Controllers\StudioController;
use Illuminate\Support\Facades\Route;

Route::view('/', 'welcome')->name('home');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::view('dashboard', 'dashboard')->name('dashboard');
});

Route::middleware(['auth'])->group(function () {
    Route::resource('studios', StudioController::class)
        ->except(['index', 'show']);
});

Route::resource('studios', StudioController::class)
    ->only(['index', 'show']);

require __DIR__.'/settings.php';
