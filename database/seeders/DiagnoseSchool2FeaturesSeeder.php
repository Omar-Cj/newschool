<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class DiagnoseSchool2FeaturesSeeder extends Seeder
{
    /**
     * Run comprehensive diagnostics for School 2 feature loading issue.
     *
     * This seeder investigates why School 2 (package_id: 1) has ZERO allowed_features
     * while School 1 (same package_id: 1) has 40+ features.
     */
    public function run(): void
    {
        $this->command->info('╔════════════════════════════════════════════════════════════════╗');
        $this->command->info('║  School 2 Feature Resolution Diagnostic Tool                  ║');
        $this->command->info('╚════════════════════════════════════════════════════════════════╝');
        $this->command->newLine();

        // Schools to diagnose
        $school1Id = 1;
        $school2Id = 2;
        $packageId = 1;

        // Diagnostic 1: Verify schools and package
        $this->command->info('🔍 [1/7] Verifying school and package data...');
        $this->diagnoseSchoolsAndPackage($school1Id, $school2Id, $packageId);
        $this->command->newLine();

        // Diagnostic 2: Check package_permission_features records
        $this->command->info('🔍 [2/7] Checking package_permission_features for package_id = 1...');
        $this->diagnosePackageFeatures($packageId);
        $this->command->newLine();

        // Diagnostic 3: Check permission_features status
        $this->command->info('🔍 [3/7] Checking permission_features status distribution...');
        $this->diagnoseFeatureStatus($packageId);
        $this->command->newLine();

        // Diagnostic 4: Check for broken relationships
        $this->command->info('🔍 [4/7] Checking for broken feature→permission relationships...');
        $this->diagnoseBrokenRelationships($packageId);
        $this->command->newLine();

        // Diagnostic 5: Check cache status
        $this->command->info('🔍 [5/7] Checking cache status for both schools...');
        $this->diagnoseCacheStatus($school1Id, $school2Id, $packageId);
        $this->command->newLine();

        // Diagnostic 6: Compare feature loading for both schools
        $this->command->info('🔍 [6/7] Simulating feature loading for both schools...');
        $this->compareFeatureLoading($school1Id, $school2Id);
        $this->command->newLine();

        // Diagnostic 7: Verify permission attributes
        $this->command->info('🔍 [7/7] Verifying permission attributes...');
        $this->diagnosePermissionAttributes($packageId);
        $this->command->newLine();

        // Summary
        $this->displaySummary();
    }

    /**
     * Diagnostic 1: Verify schools and package exist with correct relationships
     */
    private function diagnoseSchoolsAndPackage(int $school1Id, int $school2Id, int $packageId): void
    {
        $school1 = DB::table('schools')->where('id', $school1Id)->first();
        $school2 = DB::table('schools')->where('id', $school2Id)->first();
        $package = DB::table('packages')->where('id', $packageId)->first();

        $this->command->table(
            ['School ID', 'Name', 'Package ID', 'Status'],
            [
                [$school1->id ?? 'N/A', $school1->name ?? 'NULL', $school1->package_id ?? 'NULL', $school1 ? '✅ Exists' : '❌ Missing'],
                [$school2->id ?? 'N/A', $school2->name ?? 'NULL', $school2->package_id ?? 'NULL', $school2 ? '✅ Exists' : '❌ Missing'],
            ]
        );

        if ($package) {
            $this->command->info("✅ Package {$packageId} exists: {$package->name}");
        } else {
            $this->command->error("❌ Package {$packageId} NOT FOUND!");
        }

        if ($school1 && $school2 && $school1->package_id == $school2->package_id) {
            $this->command->info("✅ Both schools have the SAME package_id: {$school1->package_id}");
        } else {
            $this->command->warn("⚠️  Package IDs differ or are NULL!");
        }
    }

    /**
     * Diagnostic 2: Check package_permission_features count
     */
    private function diagnosePackageFeatures(int $packageId): void
    {
        $count = DB::table('package_permission_features')
            ->where('package_id', $packageId)
            ->count();

        if ($count > 0) {
            $this->command->info("✅ Found {$count} permission_feature records for package_id = {$packageId}");

            // Show sample records
            $samples = DB::table('package_permission_features')
                ->where('package_id', $packageId)
                ->limit(5)
                ->get();

            $this->command->table(
                ['ID', 'Package ID', 'Permission Feature ID', 'Created At'],
                $samples->map(fn($s) => [$s->id, $s->package_id, $s->permission_feature_id, $s->created_at])->toArray()
            );
        } else {
            $this->command->error("❌ CRITICAL: NO package_permission_features records found for package_id = {$packageId}!");
            $this->command->warn("   This explains why School 2 has zero features!");
        }
    }

    /**
     * Diagnostic 3: Check permission_features status distribution
     */
    private function diagnoseFeatureStatus(int $packageId): void
    {
        $statusDistribution = DB::table('package_permission_features as ppf')
            ->join('permission_features as pf', 'ppf.permission_feature_id', '=', 'pf.id')
            ->where('ppf.package_id', $packageId)
            ->select('pf.status', DB::raw('COUNT(*) as count'))
            ->groupBy('pf.status')
            ->get();

        if ($statusDistribution->isEmpty()) {
            $this->command->warn("⚠️  No permission_features found (check broken relationships)");
            return;
        }

        $this->command->table(
            ['Feature Status', 'Count', 'Active?'],
            $statusDistribution->map(function($row) {
                return [
                    $row->status ?? 'NULL',
                    $row->count,
                    $row->status == 1 ? '✅ Yes' : '❌ No (filtered out!)'
                ];
            })->toArray()
        );

        $inactiveCount = $statusDistribution->where('status', '!=', 1)->sum('count');
        if ($inactiveCount > 0) {
            $this->command->warn("⚠️  {$inactiveCount} features are INACTIVE (status != 1) and will be filtered out!");
        }
    }

    /**
     * Diagnostic 4: Check for broken relationships (NULL joins)
     */
    private function diagnoseBrokenRelationships(int $packageId): void
    {
        $brokenFeatures = DB::table('package_permission_features as ppf')
            ->leftJoin('permission_features as pf', 'ppf.permission_feature_id', '=', 'pf.id')
            ->leftJoin('permissions as p', 'pf.permission_id', '=', 'p.id')
            ->where('ppf.package_id', $packageId)
            ->whereRaw('(pf.id IS NULL OR p.id IS NULL OR pf.status != 1)')
            ->select('ppf.id as pivot_id', 'ppf.permission_feature_id', 'pf.id as feature_id', 'pf.name', 'pf.status', 'pf.permission_id', 'p.id as perm_id', 'p.attribute')
            ->get();

        if ($brokenFeatures->isEmpty()) {
            $this->command->info("✅ No broken relationships found - all features have valid permissions");
        } else {
            $this->command->error("❌ CRITICAL: Found {$brokenFeatures->count()} broken/inactive features!");

            $this->command->table(
                ['Pivot ID', 'Feature ID', 'Feature Name', 'Status', 'Permission ID', 'Perm Attr', 'Issue'],
                $brokenFeatures->map(function($f) {
                    $issue = [];
                    if ($f->feature_id === null) $issue[] = 'Missing Feature';
                    if ($f->perm_id === null) $issue[] = 'Missing Permission';
                    if ($f->status != 1) $issue[] = 'Inactive';

                    return [
                        $f->pivot_id,
                        $f->feature_id ?? 'NULL',
                        $f->name ?? 'NULL',
                        $f->status ?? 'NULL',
                        $f->permission_id ?? 'NULL',
                        $f->attribute ?? 'NULL',
                        implode(', ', $issue)
                    ];
                })->toArray()
            );
        }
    }

    /**
     * Diagnostic 5: Check cache status for both schools
     */
    private function diagnoseCacheStatus(int $school1Id, int $school2Id, int $packageId): void
    {
        $cacheKeys = [
            "school_features_{$school1Id}" => "School {$school1Id} features",
            "school_features_{$school2Id}" => "School {$school2Id} features",
            "package_allowed_permissions_{$packageId}" => "Package {$packageId} permissions",
        ];

        $cacheData = [];
        foreach ($cacheKeys as $key => $label) {
            $hasCache = Cache::has($key);
            $value = $hasCache ? Cache::get($key) : null;
            $count = $value ? (is_array($value) ? count($value) : $value->count()) : 0;

            $cacheData[] = [
                $label,
                $key,
                $hasCache ? '✅ Cached' : '❌ Not cached',
                $count
            ];
        }

        $this->command->table(
            ['Description', 'Cache Key', 'Status', 'Item Count'],
            $cacheData
        );

        // Option to clear cache
        if ($this->command->confirm('Clear School 2 cache to force fresh load?', false)) {
            Cache::forget("school_features_{$school2Id}");
            Cache::forget("package_allowed_permissions_{$packageId}");
            $this->command->info("✅ Cache cleared for School {$school2Id} and Package {$packageId}");
        }
    }

    /**
     * Diagnostic 6: Simulate feature loading for both schools
     */
    private function compareFeatureLoading(int $school1Id, int $school2Id): void
    {
        $school1 = DB::table('schools')->where('id', $school1Id)->first();
        $school2 = DB::table('schools')->where('id', $school2Id)->first();

        if (!$school1 || !$school2) {
            $this->command->error("❌ Cannot compare - one or both schools not found");
            return;
        }

        // Simulate the getAllowedFeatures() → getAllowedPermissions() chain
        $this->command->info("Simulating feature loading chain...");

        foreach ([$school1, $school2] as $school) {
            $schoolName = $school->name ?? 'Unknown';
            $this->command->info("\n📊 School {$school->id} ({$schoolName}):");

            if (!$school->package_id) {
                $this->command->error("  ❌ No package_id - would return empty array");
                continue;
            }

            // Execute the actual query from Package->getAllowedPermissions()
            $features = DB::table('packages as pkg')
                ->join('package_permission_features as ppf', 'pkg.id', '=', 'ppf.package_id')
                ->join('permission_features as pf', 'ppf.permission_feature_id', '=', 'pf.id')
                ->join('permissions as p', 'pf.permission_id', '=', 'p.id')
                ->where('pkg.id', $school->package_id)
                ->where('pf.status', 1) // Active filter
                ->select('p.attribute')
                ->distinct()
                ->get();

            $count = $features->count();
            $this->command->info("  ✅ Package ID: {$school->package_id}");
            $this->command->info("  ✅ Query returned: {$count} features");

            if ($count > 0) {
                $this->command->info("  Sample features: " . $features->take(10)->pluck('attribute')->implode(', '));
            } else {
                $this->command->error("  ❌ ZERO features returned from database query!");
            }
        }
    }

    /**
     * Diagnostic 7: Verify permission attributes are not NULL
     */
    private function diagnosePermissionAttributes(int $packageId): void
    {
        $nullAttributes = DB::table('package_permission_features as ppf')
            ->join('permission_features as pf', 'ppf.permission_feature_id', '=', 'pf.id')
            ->join('permissions as p', 'pf.permission_id', '=', 'p.id')
            ->where('ppf.package_id', $packageId)
            ->where('pf.status', 1)
            ->whereNull('p.attribute')
            ->count();

        if ($nullAttributes > 0) {
            $this->command->error("❌ Found {$nullAttributes} permissions with NULL attribute (filtered by ->filter())");
        } else {
            $this->command->info("✅ All active permissions have valid attributes");
        }

        // Show unique attribute count
        $uniqueCount = DB::table('package_permission_features as ppf')
            ->join('permission_features as pf', 'ppf.permission_feature_id', '=', 'pf.id')
            ->join('permissions as p', 'pf.permission_id', '=', 'p.id')
            ->where('ppf.package_id', $packageId)
            ->where('pf.status', 1)
            ->whereNotNull('p.attribute')
            ->distinct()
            ->count('p.attribute');

        $this->command->info("✅ Found {$uniqueCount} unique permission attributes for package {$packageId}");
    }

    /**
     * Display summary and recommendations
     */
    private function displaySummary(): void
    {
        $this->command->info('╔════════════════════════════════════════════════════════════════╗');
        $this->command->info('║  DIAGNOSTIC SUMMARY                                            ║');
        $this->command->info('╚════════════════════════════════════════════════════════════════╝');
        $this->command->newLine();

        $this->command->info('📝 Common Issues & Solutions:');
        $this->command->newLine();

        $this->command->info('1. If package_permission_features is EMPTY:');
        $this->command->warn('   → Run: php artisan db:seed --class=AddStaffManagementFeaturesToPackageSeeder');
        $this->command->newLine();

        $this->command->info('2. If features are INACTIVE (status != 1):');
        $this->command->warn('   → Update permission_features table: UPDATE permission_features SET status = 1 WHERE status != 1;');
        $this->command->newLine();

        $this->command->info('3. If broken relationships exist:');
        $this->command->warn('   → Check permission_features.permission_id matches valid permissions.id');
        $this->command->newLine();

        $this->command->info('4. If cache is stale:');
        $this->command->warn('   → Clear cache: php artisan cache:forget school_features_2');
        $this->command->newLine();

        $this->command->info('5. After fixes, test with:');
        $this->command->warn('   → php artisan diagnose:school-features 2 --compare-with=1');
        $this->command->warn('   → Visit debug endpoint as School 2 user');
    }
}
