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

// Schedule rent reminders (daily at 9 AM)
Schedule::command('rent:send-reminders --days=3')
    ->dailyAt('09:00')
    ->withoutOverlapping()
    ->runInBackground();

// Schedule overdue rent reminders (daily at 10 AM)
Schedule::command('rent:send-reminders --days=0')
    ->dailyAt('10:00')
    ->withoutOverlapping()
    ->runInBackground();
