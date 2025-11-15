<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AddStaffManagementFeaturesToPackageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * Adds Staff Management features (Users, Roles, Departments, Designations)
     * to existing packages that are missing these features.
     *
     * This fixes the 403 error issue where sidebar shows staff management
     * but routes deny access due to missing package-feature relationships.
     *
     * @return void
     */
    public function run()
    {
        $this->command->info('Starting Staff Management Features Addition...');

        // Staff Management permission_feature IDs from permission_features table
        $staffManagementFeatures = [
            59, // User Management (permission_id: 158)
            60, // Roles & Permissions (permission_id: 157)
            61, // Departments (permission_id: 159)
            62, // Designations (permission_id: 160)
        ];

        // Get all active packages
        $packages = DB::table('packages')
            ->where('status', 1)
            ->get();

        if ($packages->isEmpty()) {
            $this->command->warn('No active packages found in the system.');
            return;
        }

        $this->command->info("Found {$packages->count()} active package(s).");

        foreach ($packages as $package) {
            $this->command->info("Processing Package ID: {$package->id} - {$package->name}");

            $addedCount = 0;
            $skippedCount = 0;

            foreach ($staffManagementFeatures as $featureId) {
                // Check if the feature already exists for this package
                $exists = DB::table('package_permission_features')
                    ->where('package_id', $package->id)
                    ->where('permission_feature_id', $featureId)
                    ->exists();

                if (!$exists) {
                    // Add the feature to the package
                    DB::table('package_permission_features')->insert([
                        'package_id' => $package->id,
                        'permission_feature_id' => $featureId,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);

                    $addedCount++;
                    $this->command->comment("  ✓ Added Feature ID: {$featureId}");
                } else {
                    $skippedCount++;
                    $this->command->comment("  - Feature ID: {$featureId} already exists (skipped)");
                }
            }

            $this->command->info("  Package {$package->id}: Added {$addedCount}, Skipped {$skippedCount}");

            // Clear feature cache for all schools using this package
            $this->clearFeatureCacheForPackage($package->id);
        }

        $this->command->info('✓ Staff Management Features addition completed successfully!');
        $this->command->info('⚠ Please ensure users have the appropriate role permissions for these features.');
    }

    /**
     * Clear feature cache for all schools using the given package
     *
     * @param int $packageId
     * @return void
     */
    private function clearFeatureCacheForPackage(int $packageId)
    {
        $schools = DB::table('schools')
            ->where('package_id', $packageId)
            ->pluck('id');

        if ($schools->isEmpty()) {
            return;
        }

        $this->command->comment("  Clearing feature cache for {$schools->count()} school(s)...");

        foreach ($schools as $schoolId) {
            $cacheKey = "model_features_School_{$schoolId}";
            \Illuminate\Support\Facades\Cache::forget($cacheKey);
        }

        $this->command->comment("  ✓ Feature cache cleared");
    }
}
