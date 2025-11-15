<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Define your scheduled tasks here

// Test scheduled task - runs every minute (for testing purposes)
Schedule::command('test:scheduled-task')
    ->everyMinute()
    ->withoutOverlapping();

// Process queued jobs
Schedule::command('queue:work --stop-when-empty')
    ->everyMinute()
    ->withoutOverlapping()
    ->runInBackground();

// Example: Run a custom command daily at midnight
// Schedule::command('your:custom-command')
//     ->daily();

// Example: Run a closure every hour
// Schedule::call(function () {
//     // Your task logic here
//     \Log::info('Scheduled task executed at ' . now());
// })->hourly();

// Example: Run a job every 5 minutes
// Schedule::job(new \App\Jobs\YourJob())
//     ->everyFiveMinutes();
