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
Schedule::command('backup:run')->dailyAt('02:00');
