<?php

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;

echo "=== SIDEBAR FIX VERIFICATION ===\n\n";

// Test with school admin user (user_id=1, school_id=1)
$userId = 1;
$schoolId = 1;

$user = DB::table('users')->where('id', $userId)->first();
echo "Testing with User: {$user->name} (ID: {$user->id})\n";
echo "School ID: {$user->school_id}\n";
echo "Role ID: {$user->role_id}\n\n";

// Get school's package
$school = DB::table('schools')->where('id', $schoolId)->first();
echo "School: {$school->name}\n";
echo "Package ID: {$school->package_id}\n\n";

// Get all features in the package
$packageFeatures = DB::table('package_permission_features')
    ->join('permission_features', 'package_permission_features.permission_feature_id', '=', 'permission_features.id')
    ->join('permissions', 'permission_features.permission_id', '=', 'permissions.id')
    ->where('package_permission_features.package_id', $school->package_id)
    ->select('permissions.attribute', 'permissions.id as permission_id', 'permission_features.id as feature_id')
    ->get();

echo "=== FEATURES IN PACKAGE (Should SHOW in sidebar) ===\n";
echo "Total features in package: " . $packageFeatures->count() . "\n\n";

$allPackageFeatureNames = $packageFeatures->pluck('attribute')->toArray();

// The 17 problematic features we fixed
$fixedFeatures = [
    // Features that were IN package but NOT showing (should now SHOW)
    'report_center',
    'fees_generation',
    'terms',
    'exam_entry',
    'expense_category',
    'cash_transfer',

    // Features that were NOT in package but showing (should now HIDE)
    'fees_group',
    'fees_master',
    'fees_assign',
    'storage_settings',
    'task_schedules',
    'software_update',
    'payment_gateway_settings',
    'email_settings',
    'notification_settings',
    'sms_settings',
    'recaptcha_settings'
];

echo "Checking our 17 fixed features:\n";
echo "================================\n\n";

echo "✅ SHOULD SHOW (in package):\n";
foreach ($fixedFeatures as $feature) {
    if (in_array($feature, $allPackageFeatureNames)) {
        echo "  ✓ {$feature} - IN PACKAGE\n";
    }
}

echo "\n❌ SHOULD HIDE (not in package):\n";
foreach ($fixedFeatures as $feature) {
    if (!in_array($feature, $allPackageFeatureNames)) {
        echo "  ✗ {$feature} - NOT IN PACKAGE\n";
    }
}

echo "\n\n=== EXPECTED SIDEBAR BEHAVIOR ===\n";
echo "==================================\n\n";

echo "For school admin (user_id={$userId}, school_id={$schoolId}):\n\n";

echo "Items that SHOULD APPEAR:\n";
$shouldShow = array_intersect($fixedFeatures, $allPackageFeatureNames);
foreach ($shouldShow as $feature) {
    echo "  ✅ {$feature}\n";
}

echo "\nItems that SHOULD NOT APPEAR:\n";
$shouldHide = array_diff($fixedFeatures, $allPackageFeatureNames);
foreach ($shouldHide as $feature) {
    echo "  ❌ {$feature}\n";
}

echo "\n\n=== TESTING INSTRUCTIONS ===\n";
echo "============================\n\n";

echo "1. Login as school admin:\n";
echo "   Email: " . $user->email . "\n";
echo "   (Use the password you know for this user)\n\n";

echo "2. Check sidebar menu items:\n\n";

echo "   FEES MENU - Check these items:\n";
echo "   ✅ Fee Generation (fees_generation) - Should SHOW\n";
echo "   ❌ Group (fees_group) - Should HIDE\n";
echo "   ❌ Master (fees_master) - Should HIDE\n";
echo "   ❌ Assign (fees_assign) - Should HIDE\n\n";

echo "   EXAMINATION MENU - Check these items:\n";
echo "   ✅ Terms (terms) - Should SHOW\n";
echo "   ✅ Exam Entry (exam_entry) - Should SHOW\n\n";

echo "   ACCOUNTS MENU - Check these items:\n";
echo "   ✅ Expense Category (expense_category) - Should SHOW\n";
echo "   ✅ Cash Transfer (cash_transfer) - Should SHOW\n\n";

echo "   REPORTS MENU - Check these items:\n";
echo "   ✅ Report Center (report_center) - Should SHOW\n\n";

echo "   SETTINGS MENU - Check these items:\n";
echo "   ❌ Storage Settings (storage_settings) - Should HIDE\n";
echo "   ❌ Task Schedules (task_schedules) - Should HIDE\n";
echo "   ❌ Software Update (software_update) - Should HIDE\n";
echo "   ❌ Recaptcha Settings (recaptcha_settings) - Should HIDE\n";
echo "   ❌ SMS Settings (sms_settings) - Should HIDE\n";
echo "   ❌ Payment Gateway Settings (payment_gateway_settings) - Should HIDE\n";
echo "   ❌ Email Settings (email_settings) - Should HIDE\n";
echo "   ❌ Notification Settings (notification_settings) - Should HIDE\n\n";

echo "3. If all items match the expected behavior above, the fix is SUCCESSFUL! ✓\n\n";

echo "=== DEBUG: Package Feature List ===\n";
echo "===================================\n\n";
echo "Complete list of features in Package ID {$school->package_id}:\n";
foreach ($packageFeatures as $feature) {
    echo "  - {$feature->attribute} (Permission ID: {$feature->permission_id})\n";
}
