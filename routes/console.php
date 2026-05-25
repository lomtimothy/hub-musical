<?php

use App\Console\Commands\SendSessionReminders;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Schedule::command(SendSessionReminders::class)
    ->dailyAt('09:00')
    ->withoutOverlapping();
