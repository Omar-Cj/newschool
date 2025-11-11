<?php

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;

echo "=== PERMISSION STRUCTURE DIAGNOSIS ===\n\n";

$userId = 1;
$user = DB::table('users')->where('id', $userId)->first();

echo "User: {$user->name}\n";
echo "Role ID: {$user->role_id}\n";
echo "School ID: {$user->school_id}\n\n";

// Get raw permissions data
echo "=== RAW PERMISSIONS DATA ===\n";
echo "Type: " . gettype($user->permissions) . "\n";
echo "Is JSON string: " . (is_string($user->permissions) ? "YES" : "NO") . "\n\n";

if (is_string($user->permissions)) {
    echo "Raw JSON string (first 500 chars):\n";
    echo substr($user->permissions, 0, 500) . "...\n\n";
}

// Decode permissions
$userPermissions = json_decode($user->permissions ?? '[]', true);

echo "=== DECODED PERMISSIONS ===\n";
echo "Type after decode: " . gettype($userPermissions) . "\n";
echo "Count: " . (is_array($userPermissions) ? count($userPermissions) : 'N/A') . "\n";
echo "Is indexed array: " . (array_values($userPermissions) === $userPermissions ? "YES" : "NO") . "\n\n";

// Show structure
if (is_array($userPermissions) && count($userPermissions) > 0) {
    echo "First 20 permissions (sample):\n";
    $sample = array_slice($userPermissions, 0, 20);
    foreach ($sample as $key => $value) {
        if (is_int($key)) {
            echo "  [$key] => " . (is_string($value) ? $value : json_encode($value)) . "\n";
        } else {
            echo "  ['{$key}'] => " . (is_string($value) ? $value : json_encode($value)) . "\n";
        }
    }
    echo "\n";
}

// Check for specific permissions we need
$testPermissions = [
    'cash_transfer_read',
    'expense_category_read',
    'fees_generate_read',
    'fees_group_read',
    'terms_read',
    'exam_entry_read',
];

echo "=== CHECKING SPECIFIC PERMISSIONS ===\n";
foreach ($testPermissions as $perm) {
    $exists = in_array($perm, $userPermissions);
    echo "{$perm}: " . ($exists ? "✅ FOUND" : "❌ NOT FOUND") . "\n";

    if (!$exists) {
        // Try to find similar permissions
        $similar = array_filter($userPermissions, function($p) use ($perm) {
            return is_string($p) && stripos($p, explode('_', $perm)[0]) !== false;
        });
        if (!empty($similar)) {
            echo "   Similar found: " . implode(', ', array_slice($similar, 0, 3)) . "\n";
        }
    }
}
echo "\n";

// Check how hasPermission() helper works
echo "=== TESTING hasPermission() HELPER ===\n";
if (function_exists('hasPermission')) {
    foreach ($testPermissions as $perm) {
        // We can't call hasPermission directly without auth context
        // but we can check if function exists
        echo "hasPermission() function exists: YES\n";
    }
} else {
    echo "hasPermission() function: NOT FOUND\n";
}
echo "\n";

// Check role permissions from roles table
echo "=== ROLE PERMISSIONS (from roles table) ===\n";
$role = DB::table('roles')->where('id', $user->role_id)->first();
if ($role) {
    echo "Role Name: {$role->name}\n";
    echo "Role Type: {$role->type}\n";

    if (isset($role->permissions)) {
        $rolePermissions = json_decode($role->permissions, true);
        echo "Role has " . (is_array($rolePermissions) ? count($rolePermissions) : 0) . " permissions\n";

        echo "\nChecking if role has our test permissions:\n";
        foreach ($testPermissions as $perm) {
            $exists = is_array($rolePermissions) && in_array($perm, $rolePermissions);
            echo "  {$perm}: " . ($exists ? "✅ YES" : "❌ NO") . "\n";
        }
    }
}
echo "\n";

// Check permissions table structure
echo "=== PERMISSIONS TABLE STRUCTURE ===\n";
$permissionRecords = DB::table('permissions')
    ->whereIn('attribute', ['cash_transfer', 'expense_category', 'fees_generation', 'terms'])
    ->get();

echo "Found " . count($permissionRecords) . " permission records\n";
foreach ($permissionRecords as $perm) {
    echo "\nPermission: {$perm->attribute}\n";
    echo "  ID: {$perm->id}\n";
    if (isset($perm->keywords)) {
        $keywords = json_decode($perm->keywords, true);
        echo "  Keywords: " . json_encode($keywords) . "\n";
    }
}

echo "\n=== DIAGNOSIS COMPLETE ===\n";
