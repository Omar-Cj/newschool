<?php

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;

echo "=== MISSING PERMISSIONS INVESTIGATION ===\n\n";

// The permissions we're looking for
$neededPermissions = [
    'cash_transfer_read',
    'expense_category_read',
    'fees_generate_read',
    'terms_read',
    'exam_entry_read',
    'report_center_read',
];

echo "Checking if these permissions exist in the permissions table:\n\n";

foreach ($neededPermissions as $perm) {
    // Check permissions table
    $permission = DB::table('permissions')
        ->where('attribute', 'LIKE', '%' . str_replace('_read', '', $perm) . '%')
        ->get();

    echo "Looking for: {$perm}\n";

    if ($permission->count() > 0) {
        foreach ($permission as $p) {
            echo "  ✅ Found permission record:\n";
            echo "     ID: {$p->id}\n";
            echo "     Attribute: {$p->attribute}\n";

            if (isset($p->keywords)) {
                $keywords = json_decode($p->keywords, true);
                echo "     Keywords: " . json_encode($keywords) . "\n";

                // Check if the exact permission is in keywords
                if (is_array($keywords) && in_array($perm, $keywords)) {
                    echo "     ✅ EXACT match found in keywords!\n";
                } else {
                    echo "     ⚠️  EXACT match NOT in keywords\n";
                    if (is_array($keywords)) {
                        echo "     Keywords contain: " . implode(', ', $keywords) . "\n";
                    }
                }
            }
        }
    } else {
        echo "  ❌ NO permission record found\n";
    }
    echo "\n";
}

// Get Super Admin role current permissions
echo "\n=== SUPER ADMIN CURRENT PERMISSIONS ===\n";
$user = DB::table('users')->where('id', 1)->first();
$userPermissions = json_decode($user->permissions, true);

// Find similar permissions that are assigned
echo "Similar permissions that ARE assigned:\n";
foreach ($neededPermissions as $needed) {
    $base = str_replace('_read', '', $needed);
    $similar = array_filter($userPermissions, function($p) use ($base) {
        return stripos($p, $base) !== false;
    });

    if (!empty($similar)) {
        echo "\nFor '{$needed}':\n";
        foreach ($similar as $sim) {
            echo "  - {$sim}\n";
        }
    }
}

echo "\n\n=== SOLUTION OPTIONS ===\n";
echo "========================\n\n";

echo "Option 1: ADD missing permissions to Super Admin role\n";
echo "  - Directly add these permission keywords to the role\n";
echo "  - Requires updating roles table\n\n";

echo "Option 2: FIX permission checking logic\n";
echo "  - Use existing similar permissions\n";
echo "  - Map new permission names to old ones\n\n";

echo "Option 3: SYNC permissions from permissions table\n";
echo "  - Regenerate role permissions from permission_features\n";
echo "  - Ensure all package features have corresponding permissions\n\n";

echo "=== RECOMMENDED ACTION ===\n";
echo "Run this query to see what permission keywords should exist:\n\n";

$features = ['cash_transfer', 'expense_category', 'fees_generation', 'terms', 'exam_entry', 'report_center'];
foreach ($features as $feature) {
    $perms = DB::table('permissions')->where('attribute', $feature)->get();
    if ($perms->count() > 0) {
        foreach ($perms as $p) {
            $keywords = json_decode($p->keywords ?? '[]', true);
            echo "Feature: {$feature}\n";
            echo "  Keywords should be: " . implode(', ', $keywords) . "\n";
        }
    }
}
