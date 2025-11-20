<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule): void
    {
        // Check for expired subscriptions and set payment_status to unpaid
        // Runs daily at midnight
        $schedule->command('subscriptions:check-expiry')
            ->daily()
            ->withoutOverlapping()
            ->onOneServer()
            ->runInBackground();

        // Check for subscriptions past grace period
        // Runs daily at 1:00 AM
        $schedule->command('subscriptions:check-grace-period')
            ->dailyAt('01:00')
            ->withoutOverlapping()
            ->onOneServer()
            ->runInBackground();
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
