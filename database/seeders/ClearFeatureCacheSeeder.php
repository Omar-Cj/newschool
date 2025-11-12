<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class ClearFeatureCacheSeeder extends Seeder
{
    /**
     * Clear all feature-related caches for all schools.
     *
     * This seeder clears:
     * 1. School-specific feature caches (model_features_School_{id})
     * 2. Package-specific caches
     * 3. General feature access caches
     *
     * Run this after:
     * - Adding/removing features from packages
     * - Updating permission relationships
     * - Changing package assignments for schools
     * - Modifying subscription status
     *
     * @return void
     */
    public function run()
    {
        $this->command->info('=================================================');
        $this->command->info('Clearing Feature Caches');
        $this->command->info('=================================================');
        $this->command->newLine();

        // Clear all school feature caches
        $this->clearSchoolFeatureCaches();

        // Clear general caches
        $this->clearGeneralCaches();

        // Clear Laravel config cache
        $this->clearConfigCache();

        $this->command->newLine();
        $this->command->info('=================================================');
        $this->command->info('✓ All feature caches cleared successfully!');
        $this->command->info('=================================================');
        $this->command->info('💡 Tip: Users may need to logout and login again for changes to take effect');
    }

    /**
     * Clear feature caches for all schools
     */
    private function clearSchoolFeatureCaches()
    {
        $this->command->info('[1/3] Clearing school-specific feature caches...');

        $schools = DB::table('schools')->pluck('id');

        if ($schools->isEmpty()) {
            $this->command->warn('  ⚠ No schools found in database');
            return;
        }

        $clearedCount = 0;

        foreach ($schools as $schoolId) {
            $cacheKey = "model_features_School_{$schoolId}";

            if (Cache::has($cacheKey)) {
                Cache::forget($cacheKey);
                $clearedCount++;
                $this->command->comment("  ✓ Cleared cache for School ID: {$schoolId}");
            }
        }

        if ($clearedCount > 0) {
            $this->command->info("  ✓ Cleared {$clearedCount} school feature cache(s)");
        } else {
            $this->command->comment("  ℹ No cached feature data found for schools");
        }

        $this->command->newLine();
    }

    /**
     * Clear general feature and permission caches
     */
    private function clearGeneralCaches()
    {
        $this->command->info('[2/3] Clearing general caches...');

        try {
            // Clear all application cache
            Cache::flush();
            $this->command->comment('  ✓ Application cache flushed');

            // If using tagged caches
            if (method_exists(Cache::getStore(), 'tags')) {
                Cache::tags(['features', 'subscriptions', 'permissions'])->flush();
                $this->command->comment('  ✓ Tagged caches cleared (features, subscriptions, permissions)');
            }

        } catch (\Exception $e) {
            $this->command->warn("  ⚠ Could not clear all caches: {$e->getMessage()}");
        }

        $this->command->newLine();
    }

    /**
     * Clear Laravel configuration cache
     */
    private function clearConfigCache()
    {
        $this->command->info('[3/3] Clearing configuration caches...');

        // Note: This is done via Artisan, not directly in seeder
        $this->command->comment('  ℹ Run these commands manually if needed:');
        $this->command->line('    php artisan cache:clear');
        $this->command->line('    php artisan config:clear');
        $this->command->line('    php artisan route:clear');
        $this->command->line('    php artisan view:clear');

        $this->command->newLine();
    }
}
