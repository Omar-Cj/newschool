#!/usr/bin/env php
<?php

/**
 * Test script to debug dependent dropdown issues
 *
 * Usage: php test-dependent-dropdown.php
 */

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\ReportParameter;
use App\Repositories\Report\ReportRepository;
use App\Services\Report\DependentParameterService;

echo "=== Dependent Dropdown Debug Script ===\n\n";

// Get all report parameters with parent relationships
echo "Fetching report parameters with dependencies...\n";
$parameters = ReportParameter::with(['parent', 'children'])
    ->whereNotNull('parent_id')
    ->orderBy('report_id')
    ->orderBy('display_order')
    ->get();

echo "Found " . $parameters->count() . " dependent parameters\n\n";

foreach ($parameters as $param) {
    echo "----------------------------------------\n";
    echo "Parameter ID: {$param->id}\n";
    echo "Parameter Name: {$param->name}\n";
    echo "Parameter Label: {$param->label}\n";
    echo "Parameter Type: {$param->type}\n";
    echo "Parent ID: {$param->parent_id}\n";

    if ($param->parent) {
        echo "Parent Name: {$param->parent->name}\n";
        echo "Parent Label: {$param->parent->label}\n";
    }

    echo "Has Dynamic Query: " . ($param->hasDynamicQuery() ? 'YES' : 'NO') . "\n";
    echo "Has Static Values: " . ($param->hasStaticValues() ? 'YES' : 'NO') . "\n";

    if ($param->hasDynamicQuery()) {
        echo "\nQuery:\n";
        echo $param->getQueryString() . "\n";
    }

    if ($param->hasStaticValues()) {
        echo "\nStatic Values:\n";
        print_r($param->getParsedValues());
    }

    echo "\n";
}

// Test specific dependent parameter resolution
echo "\n=== Testing Dependent Value Resolution ===\n";

$reportRepo = new ReportRepository();
$dependentService = new DependentParameterService($reportRepo);

// Find a specific dependent parameter (e.g., section depends on class)
$sectionParam = ReportParameter::where('name', 'section_id')
    ->orWhere('name', 'section')
    ->whereNotNull('parent_id')
    ->first();

if ($sectionParam) {
    echo "\nTesting section parameter resolution...\n";
    echo "Parameter ID: {$sectionParam->id}\n";
    echo "Parameter Name: {$sectionParam->name}\n";

    if ($sectionParam->parent) {
        echo "Parent Parameter: {$sectionParam->parent->name}\n";

        // Try to get first class value
        echo "\nFetching parent (class) values...\n";
        try {
            $classValues = $reportRepo->getParameterValues($sectionParam->parent, []);
            echo "Found " . count($classValues) . " class values\n";

            if (!empty($classValues)) {
                $firstClass = $classValues[0];
                echo "Testing with class value: {$firstClass['value']} ({$firstClass['label']})\n";

                echo "\nResolving sections for this class...\n";
                $sections = $dependentService->resolveDependentValues(
                    $sectionParam->id,
                    $firstClass['value']
                );

                echo "Found " . count($sections) . " sections\n";
                if (!empty($sections)) {
                    echo "Sample sections:\n";
                    print_r(array_slice($sections, 0, 5));
                }
            }
        } catch (\Exception $e) {
            echo "ERROR: " . $e->getMessage() . "\n";
            echo "Trace: " . $e->getTraceAsString() . "\n";
        }
    }
} else {
    echo "\nNo section parameter found in database\n";
}

// Test term parameter (for examination reports)
echo "\n\n=== Testing Term Parameter ===\n";
$termParam = ReportParameter::where('name', 'term_id')
    ->orWhere('name', 'term')
    ->whereNotNull('parent_id')
    ->first();

if ($termParam) {
    echo "Parameter ID: {$termParam->id}\n";
    echo "Parameter Name: {$termParam->name}\n";

    if ($termParam->parent) {
        echo "Parent Parameter: {$termParam->parent->name}\n";

        // Try to get first session value
        echo "\nFetching parent (session) values...\n";
        try {
            $sessionValues = $reportRepo->getParameterValues($termParam->parent, []);
            echo "Found " . count($sessionValues) . " session values\n";

            if (!empty($sessionValues)) {
                $firstSession = $sessionValues[0];
                echo "Testing with session value: {$firstSession['value']} ({$firstSession['label']})\n";

                echo "\nResolving terms for this session...\n";
                $terms = $dependentService->resolveDependentValues(
                    $termParam->id,
                    $firstSession['value']
                );

                echo "Found " . count($terms) . " terms\n";
                if (!empty($terms)) {
                    echo "Sample terms:\n";
                    print_r(array_slice($terms, 0, 5));
                }
            }
        } catch (\Exception $e) {
            echo "ERROR: " . $e->getMessage() . "\n";
            echo "Trace: " . $e->getTraceAsString() . "\n";
        }
    }
} else {
    echo "\nNo term parameter found in database\n";
}

echo "\n=== Check recent logs for more details ===\n";
echo "Log file: storage/logs/laravel-" . date('Y-m-d') . ".log\n";

echo "\n=== Test Complete ===\n";
