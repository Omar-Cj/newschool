<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Modules\MainApp\Entities\Subscription;
use Modules\MainApp\Entities\School;

class UpdateSubscriptionBranchData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'subscriptions:update-branch-data
                            {--dry-run : Preview changes without applying them}
                            {--force : Force update even if data exists}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update subscription branch_count and total_price based on actual branches per school';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $dryRun = $this->option('dry-run');
        $force = $this->option('force');

        $this->info('═══════════════════════════════════════════════════════');
        $this->info('  Subscription Branch Data Update Tool');
        $this->info('═══════════════════════════════════════════════════════');
        $this->newLine();

        if ($dryRun) {
            $this->warn('⚠️  DRY RUN MODE - No changes will be saved to database');
            $this->newLine();
        }

        // Get subscriptions that need updating
        $query = Subscription::whereNotNull('school_id')
            ->with(['school', 'package']);

        if (!$force) {
            // Only update subscriptions where total_price is null or branch_count is default (1)
            $query->where(function($q) {
                $q->whereNull('total_price')
                  ->orWhere('branch_count', '<=', 1);
            });
        }

        $subscriptions = $query->get();

        if ($subscriptions->isEmpty()) {
            $this->info('✅ No subscriptions found that need updating.');
            $this->info('   All subscriptions have correct branch data.');
            return 0;
        }

        $this->info("📊 Found {$subscriptions->count()} subscription(s) to process");
        $this->newLine();

        $bar = $this->output->createProgressBar($subscriptions->count());
        $bar->setFormat(' %current%/%max% [%bar%] %percent:3s%% %message%');
        $bar->setMessage('Starting...');
        $bar->start();

        $updated = 0;
        $skipped = 0;
        $errors = [];

        DB::beginTransaction();

        try {
            foreach ($subscriptions as $subscription) {
                $bar->setMessage("Processing: {$subscription->school->name}");
                $bar->advance();

                // Get the school
                $school = $subscription->school;

                if (!$school) {
                    $skipped++;
                    continue;
                }

                // Count actual branches for this school
                $branchCount = DB::table('branches')
                    ->where('school_id', $subscription->school_id)
                    ->where('status', \App\Enums\Status::ACTIVE)
                    ->count();

                // Default to 1 if no branches exist (shouldn't happen but safe fallback)
                $branchCount = max($branchCount, 1);

                // Get base price from subscription
                $basePrice = $subscription->price;

                // Calculate total price (price × branch_count)
                $totalPrice = $basePrice * $branchCount;

                if ($dryRun) {
                    $this->newLine();
                    $this->line('─────────────────────────────────────────────────────');
                    $this->line("  Subscription ID: {$subscription->id}");
                    $this->line("  School: {$school->name}");
                    $this->line("  Package: {$subscription->package->name}");
                    $this->line("  Current branch_count: {$subscription->branch_count}");
                    $this->line("  New branch_count: {$branchCount}");
                    $this->line("  Base price: \${$basePrice}");
                    $this->line("  Current total_price: " . ($subscription->total_price ? "\${$subscription->total_price}" : 'NULL'));
                    $this->line("  New total_price: \${$totalPrice}");
                    $this->line('─────────────────────────────────────────────────────');
                } else {
                    // Update the subscription
                    $subscription->update([
                        'branch_count' => $branchCount,
                        'total_price' => $totalPrice,
                    ]);
                }

                $updated++;
            }

            $bar->setMessage('Complete!');
            $bar->finish();
            $this->newLine(2);

            if ($dryRun) {
                DB::rollBack();
                $this->newLine();
                $this->info('═══════════════════════════════════════════════════════');
                $this->info('  DRY RUN SUMMARY');
                $this->info('═══════════════════════════════════════════════════════');
                $this->info("  Would update: {$updated} subscription(s)");
                $this->info("  Skipped: {$skipped} subscription(s)");
                $this->newLine();
                $this->warn('⚠️  No changes were made to the database');
                $this->info('💡 Run without --dry-run to apply these changes');
            } else {
                DB::commit();
                $this->newLine();
                $this->info('═══════════════════════════════════════════════════════');
                $this->info('  UPDATE SUMMARY');
                $this->info('═══════════════════════════════════════════════════════');
                $this->info("  ✅ Successfully updated: {$updated} subscription(s)");
                if ($skipped > 0) {
                    $this->warn("  ⚠️  Skipped: {$skipped} subscription(s)");
                }
                $this->newLine();
                $this->info('✨ All subscriptions have been updated successfully!');
            }

            return 0;

        } catch (\Exception $e) {
            DB::rollBack();
            $this->newLine(2);
            $this->error('═══════════════════════════════════════════════════════');
            $this->error('  ERROR OCCURRED');
            $this->error('═══════════════════════════════════════════════════════');
            $this->error("  {$e->getMessage()}");
            $this->newLine();
            $this->error('❌ No changes were made to the database');

            if ($this->option('verbose')) {
                $this->error('Stack trace:');
                $this->error($e->getTraceAsString());
            }

            return 1;
        }
    }
}
