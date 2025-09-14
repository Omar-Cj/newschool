<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use App\Models\Fees\FeesCollect;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Only run this migration if the billing_period column exists
        if (!Schema::hasColumn('fees_collects', 'billing_period')) {
            echo "ERROR: billing_period column does not exist. Please run the billing period fields migration first.\n";
            return;
        }

        echo "Starting billing period population for existing fees...\n";

        // Get all fees that don't have billing period set
        $feesWithoutBillingPeriod = FeesCollect::whereNull('billing_period')->get();

        echo "Found {$feesWithoutBillingPeriod->count()} fees without billing periods.\n";

        $updated = 0;
        $skipped = 0;

        foreach ($feesWithoutBillingPeriod as $fee) {
            try {
                // Determine billing period from available data
                $billingPeriod = $this->determineBillingPeriod($fee);

                if ($billingPeriod) {
                    $billingDate = \Carbon\Carbon::createFromFormat('Y-m', $billingPeriod);

                    $fee->update([
                        'billing_period' => $billingPeriod,
                        'billing_year' => $billingDate->year,
                        'billing_month' => $billingDate->month,
                    ]);

                    $updated++;
                } else {
                    $skipped++;
                    echo "WARNING: Could not determine billing period for fee ID {$fee->id}\n";
                }
            } catch (\Exception $e) {
                $skipped++;
                echo "ERROR: Processing fee ID {$fee->id}: {$e->getMessage()}\n";
            }
        }

        echo "Migration completed: {$updated} updated, {$skipped} skipped.\n";

        // Display summary by billing period
        $this->displayBillingPeriodSummary();
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        echo "Clearing billing period data...\n";

        FeesCollect::query()->update([
            'billing_period' => null,
            'billing_year' => null,
            'billing_month' => null,
        ]);

        echo "Billing period data cleared.\n";
    }

    /**
     * Determine billing period for a fee record using various strategies
     */
    private function determineBillingPeriod(FeesCollect $fee): ?string
    {
        // Strategy 1: Use due_date if available
        if ($fee->due_date) {
            return FeesCollect::inferBillingPeriodFromDueDate($fee->due_date);
        }

        // Strategy 2: Use payment date if available
        if ($fee->date) {
            $paymentDate = \Carbon\Carbon::parse($fee->date);
            return FeesCollect::inferBillingPeriodFromDueDate($paymentDate);
        }

        // Strategy 3: Use creation date as last resort
        if ($fee->created_at) {
            return FeesCollect::inferBillingPeriodFromDueDate($fee->created_at);
        }

        // Strategy 4: Try to infer from generation batch pattern
        if ($fee->generation_batch_id && preg_match('/(\d{4})_(\d{1,2})/', $fee->generation_batch_id, $matches)) {
            $year = $matches[1];
            $month = str_pad($matches[2], 2, '0', STR_PAD_LEFT);
            return "{$year}-{$month}";
        }

        return null;
    }

    /**
     * Display a summary of billing periods after migration
     */
    private function displayBillingPeriodSummary(): void
    {
        echo "\nBilling Period Summary:\n";
        echo str_repeat("-", 50) . "\n";

        $summary = FeesCollect::select(
            'billing_period',
            DB::raw('COUNT(*) as fee_count'),
            DB::raw('SUM(amount) as total_amount')
        )
        ->whereNotNull('billing_period')
        ->groupBy('billing_period')
        ->orderBy('billing_period', 'desc')
        ->get();

        printf("%-15s %-10s %-15s\n", "Billing Period", "Fee Count", "Total Amount");
        echo str_repeat("-", 50) . "\n";

        foreach ($summary as $row) {
            printf(
                "%-15s %-10s %-15s\n",
                $row->billing_period ?? 'NULL',
                $row->fee_count,
                '$' . number_format($row->total_amount, 2)
            );
        }

        // Show fees without billing period
        $nullCount = FeesCollect::whereNull('billing_period')->count();
        if ($nullCount > 0) {
            echo "\nWARNING: {$nullCount} fees still don't have billing periods set.\n";
        }
    }
};