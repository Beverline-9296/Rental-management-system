<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Schedule M-Pesa pending transaction processing
Schedule::command('mpesa:process-pending --timeout=0.5')
    ->everyMinute()
    ->withoutOverlapping()
    ->runInBackground();
