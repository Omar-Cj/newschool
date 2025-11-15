<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class VerifyPackageFeatureIntegritySeeder extends Seeder
{
    /**
     * Run comprehensive database integrity checks for package-feature relationships.
     *
     * This seeder identifies and reports:
     * 1. Schools without packages
     * 2. Packages without features
     * 3. Inactive features in packages
     * 4. Broken permission relationships
     * 5. Schools with same package (for comparison)
     * 6. Missing Staff Management features
     *
     * @return void
     */
    public function run()
    {
        $this->command->info('=================================================');
        $this->command->info('Package-Feature Integrity Verification');
        $this->command->info('=================================================');
        $this->command->newLine();

        // Check 1: Schools without packages
        $this->checkSchoolsWithoutPackages();

        // Check 2: Packages without features
        $this->checkPackagesWithoutFeatures();

        // Check 3: Inactive features in packages
        $this->checkInactiveFeatures();

        // Check 4: Broken permission relationships
        $this->checkBrokenPermissionRelationships();

        // Check 5: Schools with same package
        $this->checkSchoolsWithSamePackage();

        // Check 6: Missing Staff Management features
        $this->checkMissingStaffManagementFeatures();

        // Check 7: Verify subscriptions
        $this->checkSubscriptionStatus();

        $this->command->newLine();
        $this->command->info('=================================================');
        $this->command->info('Integrity Check Complete');
        $this->command->info('=================================================');
    }

    /**
     * Check for schools without packages assigned
     */
    private function checkSchoolsWithoutPackages()
    {
        $this->command->info('[1/7] Checking schools without packages...');

        $schools = DB::table('schools')
            ->whereNull('package_id')
            ->select('id', 'name', 'package_id')
            ->get();

        if ($schools->isEmpty()) {
            $this->command->comment('  ✓ All schools have packages assigned');
        } else {
            $this->command->warn("  ⚠ Found {$schools->count()} school(s) without packages:");
            foreach ($schools as $school) {
                $this->command->line("    - School ID: {$school->id}, Name: {$school->name}");
            }
            $this->command->error('  ❌ Issue: Schools without packages will have NO feature access in SaaS mode');
            $this->command->info('  💡 Solution: Assign packages to these schools or create subscriptions');
        }

        $this->command->newLine();
    }

    /**
     * Check for packages without any features
     */
    private function checkPackagesWithoutFeatures()
    {
        $this->command->info('[2/7] Checking packages without features...');

        $packages = DB::table('packages as p')
            ->leftJoin('package_permission_features as ppf', 'p.id', '=', 'ppf.package_id')
            ->select('p.id', 'p.name', DB::raw('COUNT(ppf.permission_feature_id) as feature_count'))
            ->groupBy('p.id', 'p.name')
            ->having('feature_count', '=', 0)
            ->get();

        if ($packages->isEmpty()) {
            $this->command->comment('  ✓ All packages have features assigned');
        } else {
            $this->command->warn("  ⚠ Found {$packages->count()} package(s) without features:");
            foreach ($packages as $package) {
                $this->command->line("    - Package ID: {$package->id}, Name: {$package->name}");
            }
            $this->command->error('  ❌ Issue: Schools with these packages will have NO feature access');
            $this->command->info('  💡 Solution: Add features to these packages via package management');
        }

        $this->command->newLine();
    }

    /**
     * Check for inactive features in packages
     */
    private function checkInactiveFeatures()
    {
        $this->command->info('[3/7] Checking inactive features in packages...');

        $inactiveFeatures = DB::table('permission_features as pf')
            ->join('package_permission_features as ppf', 'pf.id', '=', 'ppf.permission_feature_id')
            ->where('pf.status', '=', 0)
            ->select('pf.id', 'pf.name', 'pf.status', DB::raw('COUNT(ppf.package_id) as package_count'))
            ->groupBy('pf.id', 'pf.name', 'pf.status')
            ->get();

        if ($inactiveFeatures->isEmpty()) {
            $this->command->comment('  ✓ No inactive features found in packages');
        } else {
            $this->command->warn("  ⚠ Found {$inactiveFeatures->count()} inactive feature(s) in packages:");
            foreach ($inactiveFeatures as $feature) {
                $this->command->line("    - Feature ID: {$feature->id}, Name: {$feature->name}, Used in {$feature->package_count} package(s)");
            }
            $this->command->error('  ❌ Issue: Inactive features won\'t be accessible even if in package');
            $this->command->info('  💡 Solution: Activate these features or remove from packages');
        }

        $this->command->newLine();
    }

    /**
     * Check for broken permission relationships
     */
    private function checkBrokenPermissionRelationships()
    {
        $this->command->info('[4/7] Checking broken permission relationships...');

        $brokenFeatures = DB::table('permission_features as pf')
            ->leftJoin('permissions as p', 'pf.permission_id', '=', 'p.id')
            ->whereNull('p.id')
            ->select('pf.id', 'pf.name', 'pf.permission_id')
            ->get();

        if ($brokenFeatures->isEmpty()) {
            $this->command->comment('  ✓ All permission features have valid permission relationships');
        } else {
            $this->command->warn("  ⚠ Found {$brokenFeatures->count()} feature(s) with broken permission links:");
            foreach ($brokenFeatures as $feature) {
                $this->command->line("    - Feature ID: {$feature->id}, Name: {$feature->name}, Missing Permission ID: {$feature->permission_id}");
            }
            $this->command->error('  ❌ Issue: Features won\'t work without valid permissions');
            $this->command->info('  💡 Solution: Fix permission_id in permission_features table');
        }

        $this->command->newLine();
    }

    /**
     * Check schools with the same package
     */
    private function checkSchoolsWithSamePackage()
    {
        $this->command->info('[5/7] Checking schools with same package...');

        $schoolsByPackage = DB::table('schools')
            ->whereNotNull('package_id')
            ->select('package_id', DB::raw('GROUP_CONCAT(id) as school_ids'), DB::raw('COUNT(*) as school_count'))
            ->groupBy('package_id')
            ->having('school_count', '>', 1)
            ->get();

        if ($schoolsByPackage->isEmpty()) {
            $this->command->comment('  ✓ Each package is used by only one school or no sharing detected');
        } else {
            $this->command->info("  ℹ Found {$schoolsByPackage->count()} package(s) shared by multiple schools:");
            foreach ($schoolsByPackage as $group) {
                $this->command->line("    - Package ID: {$group->package_id}, Schools: [{$group->school_ids}], Count: {$group->school_count}");
            }
            $this->command->info('  💡 This is normal for SaaS - verify these schools have consistent feature access');
        }

        $this->command->newLine();
    }

    /**
     * Check for missing Staff Management features in packages
     */
    private function checkMissingStaffManagementFeatures()
    {
        $this->command->info('[6/7] Checking Staff Management features in packages...');

        $staffFeatureIds = [59, 60, 61, 62]; // User Management, Roles, Departments, Designations

        $packages = DB::table('packages')
            ->where('status', 1)
            ->get();

        $packagesWithMissingFeatures = [];

        foreach ($packages as $package) {
            $existingFeatures = DB::table('package_permission_features')
                ->where('package_id', $package->id)
                ->whereIn('permission_feature_id', $staffFeatureIds)
                ->pluck('permission_feature_id')
                ->toArray();

            $missingFeatures = array_diff($staffFeatureIds, $existingFeatures);

            if (!empty($missingFeatures)) {
                $packagesWithMissingFeatures[$package->id] = [
                    'name' => $package->name,
                    'missing' => $missingFeatures,
                ];
            }
        }

        if (empty($packagesWithMissingFeatures)) {
            $this->command->comment('  ✓ All packages have complete Staff Management features');
        } else {
            $this->command->warn("  ⚠ Found " . count($packagesWithMissingFeatures) . " package(s) missing Staff Management features:");
            foreach ($packagesWithMissingFeatures as $packageId => $info) {
                $missingIds = implode(', ', $info['missing']);
                $this->command->line("    - Package ID: {$packageId}, Name: {$info['name']}, Missing Feature IDs: [{$missingIds}]");
            }
            $this->command->error('  ❌ Issue: Staff menu shows but gives 403 errors');
            $this->command->info('  💡 Solution: Run AddStaffManagementFeaturesToPackageSeeder');
        }

        $this->command->newLine();
    }

    /**
     * Check subscription status for all schools
     */
    private function checkSubscriptionStatus()
    {
        $this->command->info('[7/7] Checking subscription status...');

        $schoolsWithExpiredOrNoSubscription = DB::table('schools as s')
            ->leftJoin('subscriptions as sub', function($join) {
                $join->on('s.id', '=', 'sub.school_id')
                     ->where('sub.status', '=', 1);
            })
            ->whereNotNull('s.package_id')
            ->select(
                's.id',
                's.name',
                's.package_id',
                'sub.id as subscription_id',
                'sub.expiry_date',
                'sub.status as subscription_status'
            )
            ->get();

        $withoutSubscription = 0;
        $expiredSubscription = 0;
        $activeSubscription = 0;

        foreach ($schoolsWithExpiredOrNoSubscription as $school) {
            if (!$school->subscription_id) {
                $withoutSubscription++;
            } elseif ($school->expiry_date && $school->expiry_date < now()) {
                $expiredSubscription++;
            } else {
                $activeSubscription++;
            }
        }

        $this->command->info("  📊 Subscription Status:");
        $this->command->line("    - Active Subscriptions: {$activeSubscription}");
        $this->command->line("    - Expired Subscriptions: {$expiredSubscription}");
        $this->command->line("    - No Subscription: {$withoutSubscription}");

        if ($withoutSubscription > 0 || $expiredSubscription > 0) {
            $this->command->warn('  ⚠ Some schools have package_id but no active subscription');
            $this->command->info('  💡 This may cause feature access issues if subscription checking is enabled');
        } else {
            $this->command->comment('  ✓ All schools with packages have active subscriptions');
        }

        $this->command->newLine();
    }
}
