<?php

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;

echo "=== ADD MISSING PERMISSIONS TO SUPER ADMIN ===\n\n";

// Get Super Admin user
$userId = 1;
$user = DB::table('users')->where('id', $userId)->first();

if (!$user) {
    echo "❌ User not found!\n";
    exit(1);
}

echo "User: {$user->name}\n";
echo "Role ID: {$user->role_id}\n\n";

// Current permissions
$currentPermissions = json_decode($user->permissions, true);
$originalCount = count($currentPermissions);

echo "Current permissions count: {$originalCount}\n\n";

// Permissions to add
$permissionsToAdd = [
    'cash_transfer_read',
    'cash_transfer_create',
    'cash_transfer_approve',
    'cash_transfer_reject',
    'cash_transfer_delete',
    'cash_transfer_statistics',
    'expense_category_read',
    'expense_category_create',
    'expense_category_update',
    'expense_category_delete',
    'fees_generate_read',
    'fees_generate_create',
    'fees_generate_delete',
    'terms_read',
    'terms_create',
    'terms_update',
    'terms_delete',
    'exam_entry_read',
    'exam_entry_create',
    'exam_entry_update',
    'exam_entry_delete',
    'exam_entry_publish',
    'report_center_read',
    'report_center_create',
];

echo "Permissions to add:\n";
$addedCount = 0;
$skippedCount = 0;

foreach ($permissionsToAdd as $perm) {
    if (!in_array($perm, $currentPermissions)) {
        $currentPermissions[] = $perm;
        echo "  ✅ Added: {$perm}\n";
        $addedCount++;
    } else {
        echo "  ⚪ Already exists: {$perm}\n";
        $skippedCount++;
    }
}

$newCount = count($currentPermissions);

echo "\n=== SUMMARY ===\n";
echo "Original count: {$originalCount}\n";
echo "Added: {$addedCount}\n";
echo "Skipped (already exist): {$skippedCount}\n";
echo "New total: {$newCount}\n\n";

// Ask for confirmation
echo "=== CONFIRMATION ===\n";
echo "This will UPDATE the user's permissions in the database.\n";
echo "Do you want to proceed? (yes/no): ";

$handle = fopen("php://stdin", "r");
$line = fgets($handle);
$confirmation = trim(strtolower($line));
fclose($handle);

if ($confirmation !== 'yes' && $confirmation !== 'y') {
    echo "\n❌ Operation cancelled.\n";
    exit(0);
}

// Update the database
try {
    DB::table('users')
        ->where('id', $userId)
        ->update([
            'permissions' => json_encode(array_values($currentPermissions))
        ]);

    echo "\n✅ SUCCESS! Permissions updated.\n\n";

    // Verify
    $updatedUser = DB::table('users')->where('id', $userId)->first();
    $updatedPermissions = json_decode($updatedUser->permissions, true);

    echo "Verification:\n";
    echo "  Permissions count after update: " . count($updatedPermissions) . "\n";

    echo "\n  Checking critical permissions:\n";
    foreach (['cash_transfer_read', 'expense_category_read', 'fees_generate_read', 'terms_read', 'exam_entry_read', 'report_center_read'] as $perm) {
        $exists = in_array($perm, $updatedPermissions);
        echo "    {$perm}: " . ($exists ? "✅ FOUND" : "❌ NOT FOUND") . "\n";
    }

    echo "\n=== NEXT STEPS ===\n";
    echo "1. Clear caches:\n";
    echo "   php artisan cache:clear\n";
    echo "   php artisan config:clear\n\n";
    echo "2. Logout and login again as Super Admin\n\n";
    echo "3. Re-run test: php test_and_authorization.php\n\n";

} catch (\Exception $e) {
    echo "\n❌ ERROR: " . $e->getMessage() . "\n";
    exit(1);
}
