<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote')->hourly();

// Sync PawaPay active correspondents every 7 days (Sundays at 03:00)
Schedule::command('pawapay:sync-active-conf')
    ->weekly()
    ->sundays()
    ->at('03:00')
    ->withoutOverlapping()
    ->runInBackground()
    ->emailOutputOnFailure(config('mail.from.address'));

