<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;
use Modules\MainApp\Entities\Subscription;

/*
|--------------------------------------------------------------------------
| Console Routes
|--------------------------------------------------------------------------
|
| This file is where you may define all of your Closure based console
| commands. Each Closure is bound to a command instance allowing a
| simple approach to interacting with each command's IO methods.
|
*/

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

/*
|--------------------------------------------------------------------------
| Subscription Expiry Check Command
|--------------------------------------------------------------------------
|
| This command checks for expired subscriptions and updates their
| payment_status to unpaid (0). Run daily via scheduler.
|
*/

Artisan::command('subscriptions:check-expiry', function () {
    $expiredCount = Subscription::where('expiry_date', '<', now())
        ->where('payment_status', 1) // Currently paid
        ->update([
            'payment_status' => 0, // Set to unpaid
        ]);

    $this->info("Updated {$expiredCount} expired subscriptions to unpaid status.");

    \Log::info("Subscription expiry check completed. Updated {$expiredCount} subscriptions.");
})->purpose('Check for expired subscriptions and set payment_status to unpaid');

/*
|--------------------------------------------------------------------------
| Grace Period Expiry Check Command
|--------------------------------------------------------------------------
|
| This command checks for subscriptions past their grace period
| and updates their status. Run daily via scheduler.
|
*/

Artisan::command('subscriptions:check-grace-period', function () {
    $graceExpiredCount = Subscription::where('grace_expiry_date', '<', now())
        ->where('status', \Modules\MainApp\Entities\Enums\SubscriptionStatus::APPROVED)
        ->update([
            'status' => \Modules\MainApp\Entities\Enums\SubscriptionStatus::REJECTED, // Or a specific expired status
        ]);

    $this->info("Updated {$graceExpiredCount} subscriptions past grace period.");

    \Log::info("Grace period check completed. Updated {$graceExpiredCount} subscriptions.");
})->purpose('Check for subscriptions past grace period and update status');
