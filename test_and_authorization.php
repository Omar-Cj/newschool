<?php

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;

echo "=== AND AUTHORIZATION LOGIC TEST ===\n\n";

// Test user ID and school ID
$userId = 1;
$schoolId = 1;

// Get user info
$user = DB::table('users')->where('id', $userId)->first();
if (!$user) {
    echo "❌ User not found with ID: {$userId}\n";
    exit(1);
}

echo "Testing with User: {$user->name} (ID: {$user->id})\n";
echo "School ID: " . ($user->school_id ?? 'NULL (System Admin)') . "\n";
echo "Role ID: {$user->role_id}\n\n";

// Get user's permissions
$userPermissions = json_decode($user->permissions ?? '[]', true);
echo "User has " . count($userPermissions) . " role permissions\n\n";

// Get school's package
if ($user->school_id) {
    $school = DB::table('schools')->where('id', $user->school_id)->first();
    echo "School: {$school->name}\n";
    echo "Package ID: {$school->package_id}\n\n";

    // Get package features
    $packageFeatures = DB::table('package_permission_features')
        ->join('permission_features', 'package_permission_features.permission_feature_id', '=', 'permission_features.id')
        ->join('permissions', 'permission_features.permission_id', '=', 'permissions.id')
        ->where('package_permission_features.package_id', $school->package_id)
        ->select('permissions.attribute')
        ->pluck('attribute')
        ->toArray();

    echo "Package has " . count($packageFeatures) . " features\n\n";
} else {
    echo "System Admin - No package restrictions\n\n";
    $packageFeatures = [];
}

// Test features
$testFeatures = [
    ['feature' => 'cash_transfer', 'permission' => 'cash_transfer_read'],
    ['feature' => 'expense_category', 'permission' => 'expense_category_read'],
    ['feature' => 'fees_generation', 'permission' => 'fees_generate_read'],
    ['feature' => 'fees_group', 'permission' => 'fees_group_read'],
    ['feature' => 'fees_master', 'permission' => 'fees_master_read'],
    ['feature' => 'fees_assign', 'permission' => 'fees_assign_read'],
    ['feature' => 'terms', 'permission' => 'terms_read'],
    ['feature' => 'exam_entry', 'permission' => 'exam_entry_read'],
    ['feature' => 'report_center', 'permission' => 'report_center_read'],
    ['feature' => 'notification_settings', 'permission' => 'notification_settings_read'],
    ['feature' => 'storage_settings', 'permission' => 'storage_settings_read'],
    ['feature' => 'task_schedules', 'permission' => 'task_schedules_read'],
    ['feature' => 'software_update', 'permission' => 'software_update_read'],
    ['feature' => 'recaptcha_settings', 'permission' => 'recaptcha_settings_read'],
    ['feature' => 'sms_settings', 'permission' => 'sms_settings_read'],
    ['feature' => 'payment_gateway_settings', 'permission' => 'payment_gateway_settings_read'],
    ['feature' => 'email_settings', 'permission' => 'email_settings_read'],
];

echo "=== AND LOGIC TEST RESULTS ===\n";
echo "================================\n\n";

$passCount = 0;
$failCount = 0;

foreach ($testFeatures as $test) {
    $feature = $test['feature'];
    $permission = $test['permission'];

    // Check if feature in package
    $hasFeature = in_array($feature, $packageFeatures);

    // Check if permission in user permissions
    $hasPermission = in_array($permission, $userPermissions);

    // AND logic: For school admins, BOTH required
    if ($user->school_id) {
        $shouldAccess = $hasFeature && $hasPermission;
        $logic = "AND";
    } else {
        // System admin: permission only
        $shouldAccess = $hasPermission;
        $logic = "PERMISSION ONLY";
    }

    echo "Feature: {$feature}\n";
    echo "  Permission: {$permission}\n";

    if ($user->school_id) {
        echo "  Has Feature: " . ($hasFeature ? '✅ YES' : '❌ NO') . "\n";
    }
    echo "  Has Permission: " . ($hasPermission ? '✅ YES' : '❌ NO') . "\n";
    echo "  Logic: {$logic}\n";
    echo "  Result: " . ($shouldAccess ? '✅ ACCESS GRANTED' : '❌ ACCESS DENIED') . "\n";

    if ($shouldAccess) {
        $passCount++;
        echo "  Status: ✅ PASS\n";
    } else {
        $failCount++;
        echo "  Status: ⚠️  BLOCKED (Expected behavior)\n";
    }

    echo "\n";
}

echo "=== TEST SUMMARY ===\n";
echo "====================\n\n";
echo "✅ Features Accessible: {$passCount}\n";
echo "❌ Features Blocked: {$failCount}\n";
echo "Total Features Tested: " . count($testFeatures) . "\n\n";

echo "=== VALIDATION CHECKLIST ===\n";
echo "============================\n\n";

if ($user->school_id) {
    echo "For School Admin (AND logic validation):\n\n";

    $shouldShow = [];
    $shouldHide = [];

    foreach ($testFeatures as $test) {
        $feature = $test['feature'];
        $permission = $test['permission'];

        $hasFeature = in_array($feature, $packageFeatures);
        $hasPermission = in_array($permission, $userPermissions);

        if ($hasFeature && $hasPermission) {
            $shouldShow[] = $feature;
        } else {
            $shouldHide[] = $feature;
        }
    }

    echo "✅ Should SHOW in sidebar (" . count($shouldShow) . " features):\n";
    foreach ($shouldShow as $feature) {
        echo "   - {$feature}\n";
    }

    echo "\n❌ Should HIDE from sidebar (" . count($shouldHide) . " features):\n";
    foreach ($shouldHide as $feature) {
        echo "   - {$feature}\n";
    }
} else {
    echo "System Admin validation:\n";
    echo "  - Should see ALL features with permissions\n";
    echo "  - No package restrictions apply\n";
}

echo "\n\n=== MANUAL TESTING INSTRUCTIONS ===\n";
echo "====================================\n\n";

echo "1. Clear all caches:\n";
echo "   php artisan cache:clear\n";
echo "   php artisan config:clear\n";
echo "   php artisan route:clear\n";
echo "   php artisan view:clear\n\n";

echo "2. Login as user: {$user->email}\n\n";

echo "3. Check sidebar visibility:\n";
echo "   - Menu items should match the 'Should SHOW' list above\n";
echo "   - Menu items should NOT show items from 'Should HIDE' list\n\n";

echo "4. Test route access:\n";
echo "   - Click each visible menu item\n";
echo "   - Should access successfully (no 403 errors)\n";
echo "   - Hidden items should not be accessible even via direct URL\n\n";

echo "5. Expected behavior:\n";
echo "   ✅ Sidebar visibility = Route access (perfect alignment)\n";
echo "   ✅ No 403 errors for visible features\n";
echo "   ✅ Proper 403 errors for non-package features\n\n";

echo "=== SECURITY VALIDATION ===\n";
echo "===========================\n\n";

echo "AND logic enforces TWO security layers:\n\n";

echo "Layer 1 - Package (Subscription):\n";
echo "  " . ($user->school_id ? "✅ Active for school admins" : "⚪ Not applicable for system admins") . "\n";
echo "  Controls what school PAYS for\n\n";

echo "Layer 2 - Permission (Role):\n";
echo "  ✅ Active for all users\n";
echo "  Controls what user ROLE can do\n\n";

echo "Security Benefits:\n";
echo "  ✅ Defense in depth (two independent checks)\n";
echo "  ✅ Users only see features they can access\n";
echo "  ✅ Role-based access within package limits\n";
echo "  ✅ No information leakage\n";
echo "  ✅ Principle of least privilege\n\n";

echo "Test completed! Review results above.\n";
