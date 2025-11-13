#!/usr/bin/env php
<?php

/**
 * Migration Verification Script
 *
 * Automatically verifies the permission feature migration was successful.
 * Run this after executing the migration to confirm everything is working.
 *
 * Usage: php verify_migration.php
 */

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Schema;

echo "\n";
echo "╔════════════════════════════════════════════════════════════╗\n";
echo "║     PERMISSION FEATURE MIGRATION VERIFICATION SCRIPT      ║\n";
echo "╚════════════════════════════════════════════════════════════╝\n";
echo "\n";

$errors = [];
$warnings = [];
$passed = 0;
$total = 0;

/**
 * Test function with formatted output
 */
function test($description, $callable) {
    global $errors, $warnings, $passed, $total;
    $total++;

    echo str_pad($description . " ", 60, ".");

    try {
        $result = $callable();

        if ($result === true) {
            echo " ✅ PASS\n";
            $passed++;
        } elseif ($result === null) {
            echo " ⚠️  WARN\n";
            $warnings[] = $description;
        } else {
            echo " ❌ FAIL\n";
            $errors[] = $description . ": " . $result;
        }
    } catch (\Exception $e) {
        echo " ❌ ERROR\n";
        $errors[] = $description . ": " . $e->getMessage();
    }
}

// ============================================================
// DATABASE STRUCTURE TESTS
// ============================================================
echo "\n📋 DATABASE STRUCTURE TESTS\n";
echo str_repeat("─", 62) . "\n";

test("Feature Group 14 'Community' exists", function() {
    $exists = DB::table('feature_groups')->where('id', 14)->exists();
    return $exists ?: "Feature Group 14 not found";
});

test("Permissions table does NOT have branch_id column", function() {
    $hasBranchId = Schema::hasColumn('permissions', 'branch_id');
    return !$hasBranchId ?: "branch_id column still exists in permissions table";
});

test("Roles table does NOT have branch_id column", function() {
    $hasBranchId = Schema::hasColumn('roles', 'branch_id');
    return !$hasBranchId ?: "branch_id column still exists in roles table";
});

// ============================================================
// DATA INTEGRITY TESTS
// ============================================================
echo "\n📊 DATA INTEGRITY TESTS\n";
echo str_repeat("─", 62) . "\n";

test("Total permissions count = 102", function() {
    $count = DB::table('permissions')->count();
    return $count === 102 ?: "Expected 102, got $count";
});

test("Total permission_features count = 102", function() {
    $count = DB::table('permission_features')->count();
    return $count === 102 ?: "Expected 102, got $count";
});

test("Premium features count = 27", function() {
    $count = DB::table('permission_features')->where('is_premium', 1)->count();
    return $count === 27 ?: "Expected 27, got $count";
});

test("Basic features count = 75", function() {
    $count = DB::table('permission_features')->where('is_premium', 0)->count();
    return $count === 75 ?: "Expected 75, got $count";
});

test("Feature groups count = 14", function() {
    $count = DB::table('feature_groups')->count();
    return $count === 14 ?: "Expected 14, got $count";
});

test("Package 1 has 75 features assigned", function() {
    $count = DB::table('package_permission_features')
        ->where('package_id', 1)
        ->count();
    return $count === 75 ?: "Expected 75, got $count";
});

test("No unmapped permissions exist", function() {
    $unmapped = DB::table('permissions as p')
        ->leftJoin('permission_features as pf', 'p.id', '=', 'pf.permission_id')
        ->whereNull('pf.id')
        ->count();
    return $unmapped === 0 ?: "Found $unmapped unmapped permissions";
});

test("No duplicate permission attributes", function() {
    $duplicates = DB::table('permissions')
        ->select('attribute', DB::raw('count(*) as count'))
        ->groupBy('attribute')
        ->having('count', '>', 1)
        ->count();
    return $duplicates === 0 ?: "Found $duplicates duplicate attributes";
});

test("No premium features in Package 1", function() {
    $premiumCount = DB::table('package_permission_features as ppf')
        ->join('permission_features as pf', 'ppf.permission_feature_id', '=', 'pf.id')
        ->where('ppf.package_id', 1)
        ->where('pf.is_premium', 1)
        ->count();
    return $premiumCount === 0 ?: "Found $premiumCount premium features in Basic Package";
});

test("No orphaned permission_features", function() {
    $orphaned = DB::table('permission_features as pf')
        ->leftJoin('permissions as p', 'pf.permission_id', '=', 'p.id')
        ->whereNull('p.id')
        ->count();
    return $orphaned === 0 ?: "Found $orphaned orphaned permission_features";
});

// ============================================================
// MODEL VERIFICATION TESTS
// ============================================================
echo "\n🏗️  MODEL VERIFICATION TESTS\n";
echo str_repeat("─", 62) . "\n";

test("Permission model extends Model (not BaseModel)", function() {
    $permission = new \App\Models\Permission();
    $parentClass = get_parent_class($permission);
    return $parentClass === 'Illuminate\Database\Eloquent\Model' ?:
        "Expected Model, got $parentClass";
});

test("Role model extends Model (not BaseModel)", function() {
    $role = new \App\Models\Role();
    $parentClass = get_parent_class($role);
    return $parentClass === 'Illuminate\Database\Eloquent\Model' ?:
        "Expected Model, got $parentClass";
});

// ============================================================
// FEATURE DISTRIBUTION TESTS
// ============================================================
echo "\n📈 FEATURE DISTRIBUTION TESTS\n";
echo str_repeat("─", 62) . "\n";

$expectedDistribution = [
    1 => ['name' => 'Dashboard', 'count' => 1],
    2 => ['name' => 'Student Information', 'count' => 7],
    3 => ['name' => 'Academic Management', 'count' => 9],
    4 => ['name' => 'Fees Management', 'count' => 9],
    5 => ['name' => 'Examination', 'count' => 12],
    6 => ['name' => 'Accounts', 'count' => 4],
    7 => ['name' => 'Attendance', 'count' => 2],
    8 => ['name' => 'Reports', 'count' => 7],
    9 => ['name' => 'Library', 'count' => 5],
    10 => ['name' => 'Online Examination', 'count' => 4],
    11 => ['name' => 'Staff Management', 'count' => 5],
    12 => ['name' => 'Website', 'count' => 16],
    13 => ['name' => 'Settings', 'count' => 16],
    14 => ['name' => 'Community', 'count' => 3],
];

foreach ($expectedDistribution as $groupId => $expected) {
    test("Group '{$expected['name']}' has {$expected['count']} features", function() use ($groupId, $expected) {
        $count = DB::table('permission_features')
            ->where('feature_group_id', $groupId)
            ->count();
        return $count === $expected['count'] ?:
            "Expected {$expected['count']}, got $count";
    });
}

// ============================================================
// PREMIUM FEATURE TESTS
// ============================================================
echo "\n💎 PREMIUM FEATURE VERIFICATION\n";
echo str_repeat("─", 62) . "\n";

test("All Online Examination features are premium", function() {
    $nonPremium = DB::table('permission_features')
        ->where('feature_group_id', 10) // Online Examination
        ->where('is_premium', 0)
        ->count();
    return $nonPremium === 0 ?: "Found $nonPremium non-premium online exam features";
});

test("All Community features are premium", function() {
    $nonPremium = DB::table('permission_features')
        ->where('feature_group_id', 14) // Community
        ->where('is_premium', 0)
        ->count();
    return $nonPremium === 0 ?: "Found $nonPremium non-premium community features";
});

test("Cash Transfer feature is premium", function() {
    $isPremium = DB::table('permission_features as pf')
        ->join('permissions as p', 'pf.permission_id', '=', 'p.id')
        ->where('p.attribute', 'cash_transfer')
        ->where('pf.is_premium', 1)
        ->exists();
    return $isPremium ?: "Cash Transfer is not marked as premium";
});

// ============================================================
// PACKAGE ASSIGNMENT TESTS
// ============================================================
echo "\n📦 PACKAGE ASSIGNMENT TESTS\n";
echo str_repeat("─", 62) . "\n";

test("Package 1 includes Dashboard feature", function() {
    return DB::table('package_permission_features as ppf')
        ->join('permission_features as pf', 'ppf.permission_feature_id', '=', 'pf.id')
        ->join('permissions as p', 'pf.permission_id', '=', 'p.id')
        ->where('ppf.package_id', 1)
        ->where('p.attribute', 'dashboard')
        ->exists() ?: "Dashboard not in Package 1";
});

test("Package 1 includes Student Management", function() {
    return DB::table('package_permission_features as ppf')
        ->join('permission_features as pf', 'ppf.permission_feature_id', '=', 'pf.id')
        ->join('permissions as p', 'pf.permission_id', '=', 'p.id')
        ->where('ppf.package_id', 1)
        ->where('p.attribute', 'student')
        ->exists() ?: "Student not in Package 1";
});

test("Package 1 excludes Online Exam (premium)", function() {
    $hasOnlineExam = DB::table('package_permission_features as ppf')
        ->join('permission_features as pf', 'ppf.permission_feature_id', '=', 'pf.id')
        ->join('permissions as p', 'pf.permission_id', '=', 'p.id')
        ->where('ppf.package_id', 1)
        ->where('p.attribute', 'online_exam')
        ->exists();
    return !$hasOnlineExam ?: "Online Exam found in Basic Package";
});

test("Package 1 excludes Forums (premium)", function() {
    $hasForums = DB::table('package_permission_features as ppf')
        ->join('permission_features as pf', 'ppf.permission_feature_id', '=', 'pf.id')
        ->join('permissions as p', 'pf.permission_id', '=', 'p.id')
        ->where('ppf.package_id', 1)
        ->where('p.attribute', 'forums')
        ->exists();
    return !$hasForums ?: "Forums found in Basic Package";
});

test("Package 1 excludes Cash Transfer (premium)", function() {
    $hasCashTransfer = DB::table('package_permission_features as ppf')
        ->join('permission_features as pf', 'ppf.permission_feature_id', '=', 'pf.id')
        ->join('permissions as p', 'pf.permission_id', '=', 'p.id')
        ->where('ppf.package_id', 1)
        ->where('p.attribute', 'cash_transfer')
        ->exists();
    return !$hasCashTransfer ?: "Cash Transfer found in Basic Package";
});

// ============================================================
// SUMMARY
// ============================================================
echo "\n";
echo "╔════════════════════════════════════════════════════════════╗\n";
echo "║                    VERIFICATION SUMMARY                    ║\n";
echo "╚════════════════════════════════════════════════════════════╝\n";
echo "\n";

$failedCount = count($errors);
$warnCount = count($warnings);

echo "📊 Total Tests: $total\n";
echo "✅ Passed: $passed\n";
echo "❌ Failed: $failedCount\n";
echo "⚠️  Warnings: $warnCount\n";
echo "\n";

if ($failedCount > 0) {
    echo "❌ FAILED TESTS:\n";
    echo str_repeat("─", 62) . "\n";
    foreach ($errors as $error) {
        echo "  • $error\n";
    }
    echo "\n";
}

if ($warnCount > 0) {
    echo "⚠️  WARNINGS:\n";
    echo str_repeat("─", 62) . "\n";
    foreach ($warnings as $warning) {
        echo "  • $warning\n";
    }
    echo "\n";
}

if ($failedCount === 0) {
    echo "╔════════════════════════════════════════════════════════════╗\n";
    echo "║           ✅ ALL VERIFICATION TESTS PASSED! ✅             ║\n";
    echo "║                                                            ║\n";
    echo "║  Migration completed successfully. System is ready for     ║\n";
    echo "║  production use.                                           ║\n";
    echo "╚════════════════════════════════════════════════════════════╝\n";
    echo "\n";

    echo "📋 Next Steps:\n";
    echo "  1. Test both School 1 and School 2 logins\n";
    echo "  2. Verify sidebar rendering and feature access\n";
    echo "  3. Test core functionality (students, fees, attendance)\n";
    echo "  4. Monitor logs for any errors\n";
    echo "  5. Keep database backup for 7 days\n";
    echo "\n";

    exit(0);
} else {
    echo "╔════════════════════════════════════════════════════════════╗\n";
    echo "║           ❌ MIGRATION VERIFICATION FAILED! ❌             ║\n";
    echo "║                                                            ║\n";
    echo "║  Please review failed tests above and troubleshoot         ║\n";
    echo "║  before proceeding.                                        ║\n";
    echo "╚════════════════════════════════════════════════════════════╝\n";
    echo "\n";

    exit(1);
}
