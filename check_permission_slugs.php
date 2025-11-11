<?php

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;

echo "=== PERMISSION SLUG INVESTIGATION ===\n\n";

$problematicFeatures = [
    'fees_generation',
    'terms',
    'exam_entry',
    'expense_category',
    'cash_transfer',
    'report_center'
];

echo "Checking permission slugs for problematic features:\n";
echo "==================================================\n\n";

foreach ($problematicFeatures as $feature) {
    echo "Feature: {$feature}\n";

    // Try exact match
    $exactMatch = DB::table('permissions')
        ->where('attribute', $feature)
        ->first();

    if ($exactMatch) {
        echo "  ✅ Exact match found: {$exactMatch->attribute}\n";
        echo "     ID: {$exactMatch->id}\n";
    } else {
        // Try similar matches
        $similarMatches = DB::table('permissions')
            ->where('attribute', 'LIKE', "%{$feature}%")
            ->orWhere('attribute', 'LIKE', str_replace('_', '%', $feature))
            ->get();

        if ($similarMatches->count() > 0) {
            echo "  ⚠️  No exact match. Similar permissions found:\n";
            foreach ($similarMatches as $match) {
                echo "     - {$match->attribute} (ID: {$match->id})\n";
            }
        } else {
            echo "  ❌ No permission found for this feature!\n";
        }
    }
    echo "\n";
}

// Also check for variations with _read suffix
echo "\n=== Checking with _read suffix ===\n";
echo "==================================\n\n";

foreach ($problematicFeatures as $feature) {
    $withReadSuffix = $feature . '_read';
    $permission = DB::table('permissions')
        ->where('attribute', $withReadSuffix)
        ->first();

    if ($permission) {
        echo "✅ {$withReadSuffix}: EXISTS (ID: {$permission->id})\n";
    } else {
        echo "❌ {$withReadSuffix}: NOT FOUND\n";
    }
}

// Check the fees_generate vs fees_generation specifically
echo "\n\n=== Specific Check: fees_generate vs fees_generation ===\n";
echo "========================================================\n\n";

$feesGenerate = DB::table('permissions')->where('attribute', 'LIKE', '%fees_generat%')->get();
foreach ($feesGenerate as $perm) {
    echo "Found: {$perm->attribute} (ID: {$perm->id})\n";
}
