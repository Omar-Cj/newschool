<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Migrates existing subscription data to populate new fields:
     * - Sets branch_count based on actual branches count per school
     * - Sets total_price from existing price field
     * - Calculates grace_expiry_date based on expiry_date + grace_period_days
     *
     * @return void
     */
    public function up()
    {
        // Use raw queries for better performance with large datasets
        DB::transaction(function () {
            // Step 1: Update branch_count for each subscription
            // Count actual branches per school from branches table
            $subscriptions = DB::table('subscriptions')
                ->select('id', 'school_id', 'price', 'expiry_date', 'grace_period_days')
                ->get();

            foreach ($subscriptions as $subscription) {
                // Count branches for this school (if branches table exists)
                $branchCount = 1; // Default to 1

                if (Schema::hasTable('branches')) {
                    $branchCount = DB::table('branches')
                        ->where('school_id', $subscription->school_id)
                        ->where('status', 1) // Only count active branches
                        ->count();

                    // Ensure minimum of 1 branch
                    $branchCount = max(1, $branchCount);
                }

                // Calculate grace_expiry_date
                $graceExpiryDate = null;
                if ($subscription->expiry_date) {
                    $expiryDate = Carbon::parse($subscription->expiry_date);
                    $gracePeriodDays = $subscription->grace_period_days ?? 2;
                    $graceExpiryDate = $expiryDate->addDays($gracePeriodDays)->toDateString();
                }

                // Update subscription record
                DB::table('subscriptions')
                    ->where('id', $subscription->id)
                    ->update([
                        'branch_count' => $branchCount,
                        'total_price' => $subscription->price, // Use existing price as total_price
                        'grace_expiry_date' => $graceExpiryDate,
                        'updated_at' => now()
                    ]);
            }
        });
    }

    /**
     * Reverse the migrations.
     *
     * Note: This is a data migration, reversal will set fields to null
     * but won't restore original state as original values are overwritten.
     *
     * @return void
     */
    public function down()
    {
        DB::table('subscriptions')->update([
            'branch_count' => 1,
            'total_price' => null,
            'grace_expiry_date' => null,
        ]);
    }
};
