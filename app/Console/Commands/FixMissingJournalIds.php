<?php

namespace App\Console\Commands;

use App\Models\Fees\FeesCollect;
use App\Models\Fees\PaymentTransaction;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class FixMissingJournalIds extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fees:fix-missing-journal-ids
                            {--dry-run : Preview changes without executing}
                            {--student= : Only fix records for specific student ID}
                            {--limit= : Limit number of records to process}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fix fees_collects records missing journal_id by matching with payment_transactions';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $dryRun = $this->option('dry-run');
        $studentId = $this->option('student');
        $limit = $this->option('limit');

        if ($dryRun) {
            $this->warn('🔍 DRY RUN MODE - No changes will be made');
            $this->newLine();
        }

        $this->info('Scanning fees_collects for missing journal_ids...');

        // Find fees_collects records without journal_id
        $query = FeesCollect::whereNull('journal_id')
            ->whereHas('paymentTransactions', function($q) {
                $q->whereNotNull('journal_id');
            })
            ->with(['paymentTransactions' => function($q) {
                $q->whereNotNull('journal_id');
            }, 'student']);

        // Apply student filter if specified
        if ($studentId) {
            $query->where('student_id', $studentId);
            $this->info("📌 Filtering for student ID: {$studentId}");
            $this->newLine();
        }

        // Apply limit if specified
        if ($limit) {
            $query->limit((int) $limit);
            $this->info("📌 Processing limit: {$limit} records");
            $this->newLine();
        }

        $recordsWithoutJournal = $query->get();

        if ($recordsWithoutJournal->isEmpty()) {
            $this->info('✓ No records found without journal_id');
            return 0;
        }

        $this->info("Found {$recordsWithoutJournal->count()} records without journal_id");
        $this->newLine();

        $this->info('Checking payment_transactions for journal_id references...');
        $this->newLine();

        // Prepare data for display
        $tableData = [];
        $fixableRecords = [];
        $multipleJournals = [];
        $totalRecordsFound = $recordsWithoutJournal->count();

        foreach ($recordsWithoutJournal as $feesCollect) {
            $transactions = $feesCollect->paymentTransactions;

            // Get unique journal_ids from payment_transactions
            $journalIds = $transactions->pluck('journal_id')->filter()->unique();

            if ($journalIds->isEmpty()) {
                continue; // Skip if no journal_id found
            }

            if ($journalIds->count() > 1) {
                // Handle edge case: multiple different journal_ids
                $multipleJournals[] = [
                    'fees_collect_id' => $feesCollect->id,
                    'student_id' => $feesCollect->student_id,
                    'student_name' => $feesCollect->student->full_name ?? 'Unknown',
                    'journal_ids' => $journalIds->toArray(),
                    'transactions_count' => $transactions->count(),
                ];
                continue;
            }

            // Single journal_id found - this is fixable
            $journalId = $journalIds->first();
            $fixableRecords[] = [
                'fees_collect' => $feesCollect,
                'journal_id' => $journalId,
                'transactions_count' => $transactions->count(),
            ];

            $tableData[] = [
                'Fee ID' => $feesCollect->id,
                'Student' => $feesCollect->student->full_name ?? 'Unknown',
                'Student ID' => $feesCollect->student_id,
                'Amount' => '$' . number_format($feesCollect->amount, 2),
                'Journal ID' => $journalId,
                'Transactions' => $transactions->count(),
                'Branch' => $feesCollect->getBranchName(),
            ];
        }

        // Display fixable records
        if (!empty($tableData)) {
            $this->table(
                ['Fee ID', 'Student', 'Student ID', 'Amount', 'Journal ID', 'Transactions', 'Branch'],
                $tableData
            );
            $this->newLine();
        }

        // Display records with multiple journal_ids
        if (!empty($multipleJournals)) {
            $this->warn('⚠️  Found ' . count($multipleJournals) . ' records with multiple different journal_ids:');
            foreach ($multipleJournals as $record) {
                $this->line("  • Fee #{$record['fees_collect_id']} - Student: {$record['student_name']} - Journals: " . implode(', ', $record['journal_ids']));
            }
            $this->newLine();
            $this->warn('These records require manual review and will be skipped.');
            $this->newLine();
        }

        $fixableCount = count($fixableRecords);

        if ($fixableCount === 0) {
            $this->warn('No fixable records found (all have conflicts or missing data).');
            return 0;
        }

        // Proceed with fixing
        if (!$dryRun) {
            if (!$this->confirm("Do you want to proceed with updating {$fixableCount} records?", true)) {
                $this->warn('Operation cancelled.');
                return 0;
            }

            $this->info('Fixing records...');
            $progressBar = $this->output->createProgressBar($fixableCount);
            $progressBar->start();

            $fixed = 0;
            $failed = 0;
            $errors = [];

            DB::beginTransaction();
            try {
                foreach ($fixableRecords as $record) {
                    try {
                        $feesCollect = $record['fees_collect'];
                        $journalId = $record['journal_id'];

                        // Update the journal_id
                        $feesCollect->journal_id = $journalId;
                        $feesCollect->save();

                        // Log the change
                        Log::info("Fixed fees_collect journal_id", [
                            'fees_collect_id' => $feesCollect->id,
                            'student_id' => $feesCollect->student_id,
                            'journal_id' => $journalId,
                            'transactions_count' => $record['transactions_count'],
                        ]);

                        $fixed++;
                    } catch (\Exception $e) {
                        $failed++;
                        $errors[] = [
                            'fees_collect_id' => $feesCollect->id,
                            'error' => $e->getMessage(),
                        ];
                        Log::error("Failed to fix fees_collect journal_id", [
                            'fees_collect_id' => $feesCollect->id,
                            'error' => $e->getMessage(),
                        ]);
                    }
                    $progressBar->advance();
                }

                DB::commit();
                $progressBar->finish();
                $this->newLine();
                $this->newLine();

                $this->info('✅ Fix completed successfully!');
                $this->newLine();

                $this->info('📊 Summary:');
                $this->line("  • Total records found: {$totalRecordsFound}");
                $this->line("  • Records with multiple journals (skipped): " . count($multipleJournals));
                $this->line("  • Records fixed: {$fixed}");
                $this->line("  • Records failed: {$failed}");
                $this->newLine();

                if (!empty($errors)) {
                    $this->warn('Errors encountered:');
                    foreach ($errors as $error) {
                        $this->line("  • Fee #{$error['fees_collect_id']}: {$error['error']}");
                    }
                }

            } catch (\Exception $e) {
                DB::rollBack();
                $this->error('Transaction failed: ' . $e->getMessage());
                return 1;
            }

        } else {
            // Dry run summary
            $this->newLine();
            $this->info('📊 Dry Run Summary (no changes made):');
            $this->line("  • Total records found: {$totalRecordsFound}");
            $this->line("  • Records with multiple journals (would skip): " . count($multipleJournals));
            $this->line("  • Records that would be fixed: {$fixableCount}");
            $this->newLine();
            $this->info('Run without --dry-run to apply changes');
        }

        return 0;
    }
}
