<?php

namespace App\Console\Commands;

use App\Models\Fees\Receipt;
use App\Models\Fees\PaymentTransaction;
use App\Models\StudentInfo\SessionClassStudent;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class FixReceiptClassSection extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'receipts:fix-class-section
                            {--dry-run : Preview changes without applying them}
                            {--limit= : Limit number of receipts to process}
                            {--session= : Only process receipts for specific session ID}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fix class and section data in existing receipts to show historical enrollment';

    protected $stats = [
        'total' => 0,
        'updated' => 0,
        'skipped' => 0,
        'no_enrollment' => 0,
        'no_session' => 0,
        'errors' => 0,
    ];

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $isDryRun = $this->option('dry-run');
        $limit = $this->option('limit');
        $sessionFilter = $this->option('session');

        $this->info('===========================================');
        $this->info('  Receipt Class/Section Fix Command');
        $this->info('===========================================');
        $this->newLine();

        if ($isDryRun) {
            $this->warn('🔍 DRY RUN MODE - No changes will be saved');
            $this->newLine();
        }

        // Build query for receipts to process
        $query = Receipt::query()
            ->where('source_type', PaymentTransaction::class)
            ->whereNotNull('source_id')
            ->whereNotNull('student_id');

        if ($sessionFilter) {
            $query->where('session_id', $sessionFilter);
            $this->info("Filtering by session: {$sessionFilter}");
        }

        if ($limit) {
            $query->limit($limit);
            $this->info("Limiting to {$limit} receipts");
        }

        $totalReceipts = $query->count();
        $this->stats['total'] = $totalReceipts;

        if ($totalReceipts === 0) {
            $this->warn('No receipts found to process.');
            return Command::SUCCESS;
        }

        $this->info("Found {$totalReceipts} receipts to process");
        $this->newLine();

        if (!$this->confirm('Do you want to continue?', true)) {
            $this->info('Operation cancelled.');
            return Command::SUCCESS;
        }

        $this->newLine();
        $progressBar = $this->output->createProgressBar($totalReceipts);
        $progressBar->start();

        // Process receipts in chunks for memory efficiency
        $query->chunk(100, function ($receipts) use ($isDryRun, $progressBar) {
            foreach ($receipts as $receipt) {
                $this->processReceipt($receipt, $isDryRun);
                $progressBar->advance();
            }
        });

        $progressBar->finish();
        $this->newLine(2);

        // Display results
        $this->displayResults($isDryRun);

        return Command::SUCCESS;
    }

    /**
     * Process a single receipt
     */
    protected function processReceipt(Receipt $receipt, bool $isDryRun): void
    {
        try {
            // Get session_id for historical context
            $sessionId = $this->getSessionId($receipt);

            if (!$sessionId) {
                $this->stats['no_session']++;
                Log::warning('Receipt has no session_id', [
                    'receipt_id' => $receipt->id,
                    'receipt_number' => $receipt->receipt_number,
                ]);
                return;
            }

            // Get correct class/section from historical enrollment
            $enrollment = SessionClassStudent::where('student_id', $receipt->student_id)
                ->where('session_id', $sessionId)
                ->with(['class', 'section'])
                ->first();

            if (!$enrollment) {
                $this->stats['no_enrollment']++;
                Log::warning('No enrollment record found', [
                    'receipt_id' => $receipt->id,
                    'student_id' => $receipt->student_id,
                    'session_id' => $sessionId,
                ]);
                return;
            }

            $correctClass = $enrollment->class->name ?? null;
            $correctSection = $enrollment->section->name ?? null;

            // Check if update is needed
            if ($receipt->class === $correctClass && $receipt->section === $correctSection) {
                $this->stats['skipped']++;
                return;
            }

            // Log the change
            $changeLog = [
                'receipt_id' => $receipt->id,
                'receipt_number' => $receipt->receipt_number,
                'student_id' => $receipt->student_id,
                'session_id' => $sessionId,
                'old_class' => $receipt->class,
                'new_class' => $correctClass,
                'old_section' => $receipt->section,
                'new_section' => $correctSection,
                'dry_run' => $isDryRun,
            ];

            Log::info('Receipt class/section fix', $changeLog);

            // Update if not dry run
            if (!$isDryRun) {
                $receipt->update([
                    'class' => $correctClass,
                    'section' => $correctSection,
                ]);
            }

            $this->stats['updated']++;

        } catch (\Exception $e) {
            $this->stats['errors']++;
            Log::error('Error processing receipt', [
                'receipt_id' => $receipt->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
        }
    }

    /**
     * Get session_id for receipt (from receipt or payment transaction)
     */
    protected function getSessionId(Receipt $receipt): ?int
    {
        // Try receipt.session_id first
        if ($receipt->session_id) {
            return $receipt->session_id;
        }

        // Fallback to payment_transaction → fees_collect → session_id
        if ($receipt->source_type === PaymentTransaction::class && $receipt->source_id) {
            $transaction = PaymentTransaction::with('feesCollect')->find($receipt->source_id);
            return $transaction?->feesCollect?->session_id;
        }

        return null;
    }

    /**
     * Display results summary
     */
    protected function displayResults(bool $isDryRun): void
    {
        $this->info('===========================================');
        $this->info('  Results Summary');
        $this->info('===========================================');
        $this->newLine();

        $this->table(
            ['Metric', 'Count'],
            [
                ['Total Receipts Processed', $this->stats['total']],
                ['✅ Updated', $this->stats['updated']],
                ['⏭️  Skipped (Already Correct)', $this->stats['skipped']],
                ['⚠️  No Enrollment Record', $this->stats['no_enrollment']],
                ['⚠️  No Session ID', $this->stats['no_session']],
                ['❌ Errors', $this->stats['errors']],
            ]
        );

        $this->newLine();

        if ($isDryRun) {
            $this->warn('🔍 This was a DRY RUN - no changes were saved');
            $this->info('Run without --dry-run to apply changes');
        } else {
            $this->info('✅ All changes have been saved');
        }

        if ($this->stats['updated'] > 0) {
            $this->newLine();
            $this->info("Check logs for detailed change history:");
            $this->line("  storage/logs/laravel.log");
        }

        if ($this->stats['errors'] > 0) {
            $this->newLine();
            $this->error("⚠️  {$this->stats['errors']} errors occurred. Check logs for details.");
        }
    }
}
