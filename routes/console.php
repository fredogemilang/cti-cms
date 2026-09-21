<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// content:publish-scheduled is already scheduled in bootstrap/app.php
// (with ->withoutOverlapping()); scheduling it here too would run it twice
// per minute.

// backup:clean must be scheduled too, otherwise the daily zips accumulate
// forever (13 GB / 50 zips found on cdtcms.ctizen.id, 2026-09-21).
// Retention lives in config/backup.php 'cleanup'. Clean first, then run.
Schedule::command('backup:clean')->dailyAt('01:30');
Schedule::command('backup:run')->dailyAt('02:00');
