<?php

declare(strict_types=1);

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\FeatureAccessService;
use App\Services\MenuGeneratorService;
use App\Models\Subscription;

/**
 * Clear Feature Cache Command
 *
 * Artisan command to clear feature-related caches
 * Usage: php artisan features:clear-cache [options]
 */
class ClearFeatureCacheCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'features:clear-cache
                            {--school= : Clear cache for specific school ID}
                            {--all : Clear cache for all schools}
                            {--menus : Clear menu caches only}
                            {--features : Clear feature caches only}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clear feature access and menu caches';

    /**
     * Feature access service
     */
    private FeatureAccessService $featureAccessService;

    /**
     * Menu generator service
     */
    private MenuGeneratorService $menuGeneratorService;

    /**
     * Create a new command instance.
     *
     * @param FeatureAccessService $featureAccessService
     * @param MenuGeneratorService $menuGeneratorService
     */
    public function __construct(
        FeatureAccessService $featureAccessService,
        MenuGeneratorService $menuGeneratorService
    ) {
        parent::__construct();
        $this->featureAccessService = $featureAccessService;
        $this->menuGeneratorService = $menuGeneratorService;
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle(): int
    {
        $schoolId = $this->option('school');
        $all = $this->option('all');
        $menusOnly = $this->option('menus');
        $featuresOnly = $this->option('features');

        // Validate options
        if ($schoolId && $all) {
            $this->error('Cannot use --school and --all together. Choose one option.');
            return 1;
        }

        if (!$schoolId && !$all) {
            $this->error('Please specify either --school=ID or --all');
            return 1;
        }

        $this->info('Starting cache clearing operation...');

        // Clear for specific school
        if ($schoolId) {
            return $this->clearSchoolCache((int) $schoolId, $menusOnly, $featuresOnly);
        }

        // Clear for all schools
        if ($all) {
            return $this->clearAllSchoolCaches($menusOnly, $featuresOnly);
        }

        return 0;
    }

    /**
     * Clear cache for a specific school
     *
     * @param int $schoolId
     * @param bool $menusOnly
     * @param bool $featuresOnly
     * @return int
     */
    private function clearSchoolCache(int $schoolId, bool $menusOnly, bool $featuresOnly): int
    {
        $this->info("Clearing cache for school ID: {$schoolId}");

        try {
            if (!$menusOnly) {
                $this->featureAccessService->clearSchoolCache($schoolId);
                $this->line('✓ Feature caches cleared');
            }

            if (!$featuresOnly) {
                $this->menuGeneratorService->clearMenuCache($schoolId);
                $this->line('✓ Menu caches cleared');
            }

            $this->info("Cache cleared successfully for school ID: {$schoolId}");
            return 0;
        } catch (\Exception $e) {
            $this->error("Error clearing cache: {$e->getMessage()}");
            return 1;
        }
    }

    /**
     * Clear cache for all schools
     *
     * @param bool $menusOnly
     * @param bool $featuresOnly
     * @return int
     */
    private function clearAllSchoolCaches(bool $menusOnly, bool $featuresOnly): int
    {
        $this->info('Clearing cache for all schools...');

        try {
            // Get all school IDs from subscriptions
            $schoolIds = Subscription::distinct()
                ->pluck('school_id')
                ->filter()
                ->toArray();

            if (empty($schoolIds)) {
                $this->warn('No schools found with subscriptions.');
                return 0;
            }

            $this->info(sprintf('Found %d schools with subscriptions', count($schoolIds)));

            $progressBar = $this->output->createProgressBar(count($schoolIds));
            $progressBar->start();

            foreach ($schoolIds as $schoolId) {
                if (!$menusOnly) {
                    $this->featureAccessService->clearSchoolCache($schoolId);
                }

                if (!$featuresOnly) {
                    $this->menuGeneratorService->clearMenuCache($schoolId);
                }

                $progressBar->advance();
            }

            $progressBar->finish();
            $this->newLine(2);

            // Also clear global caches
            if (!$menusOnly) {
                $this->featureAccessService->clearAllCaches();
                $this->line('✓ Global feature caches cleared');
            }

            $this->info(sprintf(
                'Successfully cleared cache for %d schools',
                count($schoolIds)
            ));

            // Display statistics
            $this->displayStatistics($schoolIds);

            return 0;
        } catch (\Exception $e) {
            $this->error("Error clearing caches: {$e->getMessage()}");
            return 1;
        }
    }

    /**
     * Display cache clearing statistics
     *
     * @param array $schoolIds
     * @return void
     */
    private function displayStatistics(array $schoolIds): void
    {
        $this->newLine();
        $this->info('Cache Clearing Statistics:');

        $this->table(
            ['Metric', 'Value'],
            [
                ['Schools Processed', count($schoolIds)],
                ['Feature Caches Cleared', $this->option('menus') ? '0 (skipped)' : count($schoolIds)],
                ['Menu Caches Cleared', $this->option('features') ? '0 (skipped)' : count($schoolIds)],
                ['Timestamp', now()->toDateTimeString()],
            ]
        );
    }
}
