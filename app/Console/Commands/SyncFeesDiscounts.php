<?php

namespace App\Console\Commands;

use App\Models\Fees\FeesCollect;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class SyncFeesDiscounts extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fees:sync-discounts
                            {--dry-run : Preview changes without actually updating records}
                            {--student= : Only sync records for specific student ID}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync discount_applied field with discount_amount for historical fee records';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $dryRun = $this->option('dry-run');
        $studentId = $this->option('student');

        $this->info('Syncing discount fields for fees_collects table...');
        $this->newLine();

        if ($dryRun) {
            $this->warn('🔍 DRY RUN MODE - No changes will be made');
            $this->newLine();
        }

        // Build query to find mismatched records
        $query = FeesCollect::where(function($q) {
            // Find records where discount_amount exists but doesn't match discount_applied
            $q->where('discount_amount', '>', 0)
              ->whereRaw('COALESCE(discount_applied, 0) != COALESCE(discount_amount, 0)');
        })->orWhere(function($q) {
            // Or where discount_amount is null but discount_applied is not
            $q->whereNull('discount_amount')
              ->where('discount_applied', '>', 0);
        });

        // Filter by student if specified
        if ($studentId) {
            $query->where('student_id', $studentId);
            $this->info("📌 Filtering for student ID: {$studentId}");
            $this->newLine();
        }

        $records = $query->with('student')->get();

        if ($records->isEmpty()) {
            $this->info('✓ No records found with mismatched discount fields.');
            return 0;
        }

        $this->info("Found {$records->count()} records with mismatched discount fields:");
        $this->newLine();

        // Display table of records to be updated
        $tableData = [];
        $totalDiscountAmount = 0;
        $studentsAffected = [];

        foreach ($records as $record) {
            $oldValue = $record->discount_applied ?? 0;
            $newValue = $record->discount_amount ?? 0;
            $difference = $newValue - $oldValue;
            $totalDiscountAmount += $newValue;
            $studentsAffected[$record->student_id] = true;

            $tableData[] = [
                'ID' => $record->id,
                'Student' => $record->student->full_name ?? 'Unknown',
                'Student ID' => $record->student_id,
                'Amount' => '$' . number_format($record->amount, 2),
                'Old Discount' => '$' . number_format($oldValue, 2),
                'New Discount' => '$' . number_format($newValue, 2),
                'Difference' => '$' . number_format($difference, 2),
            ];
        }

        $this->table(
            ['ID', 'Student', 'Student ID', 'Amount', 'Old Discount', 'New Discount', 'Difference'],
            $tableData
        );

        $this->newLine();

        if (!$dryRun) {
            if (!$this->confirm('Do you want to proceed with updating these records?', true)) {
                $this->warn('Operation cancelled.');
                return 0;
            }

            $this->info('Updating records...');
            $progressBar = $this->output->createProgressBar($records->count());
            $progressBar->start();

            $updated = 0;
            $failed = 0;

            DB::beginTransaction();
            try {
                foreach ($records as $record) {
                    try {
                        // Sync discount_applied with discount_amount
                        $record->discount_applied = $record->discount_amount ?? 0;

                        // Update payment status based on new balance
                        $record->updatePaymentStatus();

                        $record->save();
                        $updated++;
                    } catch (\Exception $e) {
                        $failed++;
                        $this->error("Failed to update record {$record->id}: {$e->getMessage()}");
                    }
                    $progressBar->advance();
                }

                DB::commit();
                $progressBar->finish();
                $this->newLine();
                $this->newLine();

                $this->info('✓ Update completed successfully!');
                $this->newLine();

                $this->info('📊 Summary:');
                $this->line("  • Records updated: {$updated}");
                $this->line("  • Records failed: {$failed}");
                $this->line('  • Total discount synced: $' . number_format($totalDiscountAmount, 2));
                $this->line('  • Students affected: ' . count($studentsAffected));

            } catch (\Exception $e) {
                DB::rollBack();
                $this->error('Transaction failed: ' . $e->getMessage());
                return 1;
            }

        } else {
            $this->newLine();
            $this->info('📊 Dry Run Summary (no changes made):');
            $this->line("  • Records to update: {$records->count()}");
            $this->line('  • Total discount to sync: $' . number_format($totalDiscountAmount, 2));
            $this->line('  • Students to affect: ' . count($studentsAffected));
            $this->newLine();
            $this->info('Run without --dry-run to apply changes');
        }

        return 0;
    }
}
