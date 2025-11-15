<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Modules\MainApp\Entities\School;

class DiagnoseSchoolFeatures extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'diagnose:school-features
                            {school_id : The ID of the school to diagnose}
                            {--compare-with= : Compare with another school ID}
                            {--clear-cache : Clear cache before diagnosis}
                            {--show-sql : Display SQL queries}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Diagnose why a school has empty or incorrect allowed_features';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $schoolId = $this->argument('school_id');
        $compareWithId = $this->option('compare-with');
        $clearCache = $this->option('clear-cache');
        $showSql = $this->option('show-sql');

        $this->info("╔═══════════════════════════════════════════════════════════════╗");
        $this->info("║  School Feature Diagnostic Tool                               ║");
        $this->info("╚═══════════════════════════════════════════════════════════════╝");
        $this->newLine();

        // Load school
        $school = DB::table('schools')->where('id', $schoolId)->first();
        if (!$school) {
            $this->error("❌ School {$schoolId} not found!");
            return self::FAILURE;
        }

        $this->info("🏫 Diagnosing School: {$school->school_name} (ID: {$school->id})");
        $this->info("   Package ID: " . ($school->package_id ?? 'NULL'));
        $this->newLine();

        // Optional: Clear cache
        if ($clearCache) {
            $this->clearSchoolCache($schoolId, $school->package_id);
        }

        // Run diagnostics
        $this->diagnoseSchool($school, $showSql);

        // Compare with another school if requested
        if ($compareWithId) {
            $this->newLine();
            $this->info("═══════════════════════════════════════════════════════════════");
            $this->info("  COMPARISON MODE");
            $this->info("═══════════════════════════════════════════════════════════════");
            $this->newLine();

            $compareSchool = DB::table('schools')->where('id', $compareWithId)->first();
            if ($compareSchool) {
                $this->diagnoseSchool($compareSchool, $showSql);
                $this->compareSchools($school, $compareSchool);
            } else {
                $this->error("❌ Comparison school {$compareWithId} not found!");
            }
        }

        $this->newLine();
        $this->info("✅ Diagnosis complete!");

        return self::SUCCESS;
    }

    /**
     * Clear cache for a school
     */
    private function clearSchoolCache(int $schoolId, ?int $packageId): void
    {
        $this->info("🧹 Clearing cache...");

        $schoolCacheKey = "school_features_{$schoolId}";
        if (Cache::has($schoolCacheKey)) {
            Cache::forget($schoolCacheKey);
            $this->info("   ✅ Cleared: {$schoolCacheKey}");
        }

        if ($packageId) {
            $packageCacheKey = "package_allowed_permissions_{$packageId}";
            if (Cache::has($packageCacheKey)) {
                Cache::forget($packageCacheKey);
                $this->info("   ✅ Cleared: {$packageCacheKey}");
            }
        }

        $this->newLine();
    }

    /**
     * Diagnose a single school
     */
    private function diagnoseSchool(object $school, bool $showSql): void
    {
        $this->info("📊 School: {$school->school_name} (ID: {$school->id})");
        $this->newLine();

        // Check 1: Package exists
        if (!$school->package_id) {
            $this->error("   ❌ CRITICAL: No package_id assigned!");
            $this->warn("   → School cannot have any features without a package");
            return;
        }

        $package = DB::table('packages')->where('id', $school->package_id)->first();
        if (!$package) {
            $this->error("   ❌ CRITICAL: Package {$school->package_id} not found!");
            return;
        }

        $this->info("   ✅ Package: {$package->name} (ID: {$package->id})");
        $this->newLine();

        // Check 2: package_permission_features count
        $this->info("🔍 Step 1: Checking package_permission_features...");
        $ppfCount = DB::table('package_permission_features')
            ->where('package_id', $package->id)
            ->count();

        if ($ppfCount === 0) {
            $this->error("   ❌ CRITICAL: NO package_permission_features for this package!");
            $this->warn("   → Run: php artisan db:seed --class=AddStaffManagementFeaturesToPackageSeeder");
            return;
        }

        $this->info("   ✅ Found {$ppfCount} permission_feature records");
        $this->newLine();

        // Check 3: Active permission_features
        $this->info("🔍 Step 2: Checking active permission_features...");
        $activeFeatures = DB::table('package_permission_features as ppf')
            ->join('permission_features as pf', 'ppf.permission_feature_id', '=', 'pf.id')
            ->where('ppf.package_id', $package->id)
            ->where('pf.status', 1)
            ->count();

        $inactiveFeatures = $ppfCount - $activeFeatures;

        if ($activeFeatures === 0) {
            $this->error("   ❌ CRITICAL: ALL features are INACTIVE (status != 1)!");
            $this->warn("   → Update permission_features: SET status = 1");
            return;
        }

        $this->info("   ✅ Active features: {$activeFeatures}");
        if ($inactiveFeatures > 0) {
            $this->warn("   ⚠️  Inactive features: {$inactiveFeatures} (will be filtered out)");
        }
        $this->newLine();

        // Check 4: Permission relationships
        $this->info("🔍 Step 3: Checking permission relationships...");
        $validPermissions = DB::table('package_permission_features as ppf')
            ->join('permission_features as pf', 'ppf.permission_feature_id', '=', 'pf.id')
            ->join('permissions as p', 'pf.permission_id', '=', 'p.id')
            ->where('ppf.package_id', $package->id)
            ->where('pf.status', 1)
            ->count();

        $brokenRelationships = $activeFeatures - $validPermissions;

        if ($brokenRelationships > 0) {
            $this->error("   ❌ CRITICAL: {$brokenRelationships} features have broken permission relationships!");
            $this->warn("   → Check permission_features.permission_id matches valid permissions.id");
        } else {
            $this->info("   ✅ All active features have valid permissions");
        }
        $this->newLine();

        // Check 5: Final permissions (attributes)
        $this->info("🔍 Step 4: Checking final permission attributes...");
        $sql = "
            SELECT DISTINCT p.attribute
            FROM package_permission_features ppf
            JOIN permission_features pf ON ppf.permission_feature_id = pf.id
            JOIN permissions p ON pf.permission_id = p.id
            WHERE ppf.package_id = ?
              AND pf.status = 1
              AND p.attribute IS NOT NULL
        ";

        if ($showSql) {
            $this->comment("   SQL Query:");
            $this->comment("   " . str_replace("\n", "\n   ", $sql));
            $this->newLine();
        }

        $permissions = DB::select($sql, [$package->id]);
        $permissionCount = count($permissions);

        if ($permissionCount === 0) {
            $this->error("   ❌ CRITICAL: ZERO final permissions!");
            $this->warn("   → Check if permissions.attribute is NULL");
        } else {
            $this->info("   ✅ Final permissions: {$permissionCount}");
            $this->info("   Sample: " . implode(', ', array_slice(array_column($permissions, 'attribute'), 0, 10)));
        }
        $this->newLine();

        // Check 6: Cache status
        $this->info("🔍 Step 5: Checking cache...");
        $schoolCacheKey = "school_features_{$school->id}";
        $packageCacheKey = "package_allowed_permissions_{$package->id}";

        $schoolCached = Cache::has($schoolCacheKey);
        $packageCached = Cache::has($packageCacheKey);

        if ($schoolCached) {
            $cachedFeatures = Cache::get($schoolCacheKey);
            $count = is_array($cachedFeatures) ? count($cachedFeatures) : $cachedFeatures->count();
            $this->info("   ✅ School cache exists: {$count} features");
        } else {
            $this->warn("   ⚠️  School cache empty (will build on next access)");
        }

        if ($packageCached) {
            $cachedPermissions = Cache::get($packageCacheKey);
            $count = is_array($cachedPermissions) ? count($cachedPermissions) : $cachedPermissions->count();
            $this->info("   ✅ Package cache exists: {$count} permissions");
        } else {
            $this->warn("   ⚠️  Package cache empty (will build on next access)");
        }
        $this->newLine();

        // Check 7: Test with Eloquent model
        $this->info("🔍 Step 6: Testing with Eloquent model...");
        try {
            $schoolModel = School::with('package')->find($school->id);
            if ($schoolModel) {
                $features = $schoolModel->getAllowedFeatures();
                $this->info("   ✅ Eloquent getAllowedFeatures() returned: {$features->count()} features");
                if ($features->count() > 0) {
                    $this->info("   Sample: " . $features->take(10)->implode(', '));
                }
            }
        } catch (\Exception $e) {
            $this->error("   ❌ Eloquent error: " . $e->getMessage());
        }
        $this->newLine();
    }

    /**
     * Compare two schools
     */
    private function compareSchools(object $school1, object $school2): void
    {
        $this->info("📊 Comparison Summary:");
        $this->newLine();

        $data = [
            ['Attribute', 'School ' . $school1->id, 'School ' . $school2->id, 'Match?'],
            ['Name', $school1->school_name, $school2->school_name, ''],
            ['Package ID', $school1->package_id ?? 'NULL', $school2->package_id ?? 'NULL', $school1->package_id === $school2->package_id ? '✅' : '❌'],
        ];

        // Get feature counts via Eloquent
        try {
            $s1Model = School::find($school1->id);
            $s2Model = School::find($school2->id);

            if ($s1Model && $s2Model) {
                $s1Features = $s1Model->getAllowedFeatures()->count();
                $s2Features = $s2Model->getAllowedFeatures()->count();

                $data[] = ['Feature Count', $s1Features, $s2Features, $s1Features === $s2Features ? '✅' : '❌'];

                if ($s1Features > 0 && $s2Features === 0) {
                    $this->newLine();
                    $this->error("❌ ISSUE FOUND: School {$school2->id} has ZERO features despite same package!");
                }
            }
        } catch (\Exception $e) {
            $this->error("Error during comparison: " . $e->getMessage());
        }

        $this->table($data[0], array_slice($data, 1));
    }
}
