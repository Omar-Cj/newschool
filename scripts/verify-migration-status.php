#!/usr/bin/env php
<?php

/**
 * Migration Status Verification Script
 *
 * This script verifies that the FixMigrationStatusSeeder has been executed correctly
 * and that all required tables and records exist.
 *
 * Usage: php scripts/verify-migration-status.php
 */

// Load Laravel application
require __DIR__ . '/../bootstrap/app.php';

use Illuminate\Support\Facades\DB;
use Modules\MainApp\Entities\School;
use Modules\MainApp\Entities\Package;
use Modules\MainApp\Entities\Subscription;

$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(\Illuminate\Contracts\Http\Kernel::class);

// Simple output formatting
class VerificationReport
{
    private array $results = [];
    private int $passCount = 0;
    private int $failCount = 0;

    public function check(string $name, bool $condition, string $details = ''): self
    {
        $status = $condition ? 'PASS' : 'FAIL';
        $this->results[] = [
            'name' => $name,
            'status' => $status,
            'details' => $details
        ];

        if ($condition) {
            $this->passCount++;
        } else {
            $this->failCount++;
        }

        return $this;
    }

    public function print(): void
    {
        echo "\n";
        echo "═══════════════════════════════════════════════════════════════\n";
        echo "  MIGRATION STATUS VERIFICATION REPORT\n";
        echo "═══════════════════════════════════════════════════════════════\n\n";

        foreach ($this->results as $result) {
            $icon = $result['status'] === 'PASS' ? '✓' : '✗';
            $color = $result['status'] === 'PASS' ? '\033[32m' : '\033[31m';
            $reset = '\033[0m';

            echo "{$color}[{$icon}] {$result['status']}{$reset} - {$result['name']}\n";
            if ($result['details']) {
                echo "    └─ {$result['details']}\n";
            }
        }

        echo "\n───────────────────────────────────────────────────────────────\n";
        echo "SUMMARY: {$this->passCount} passed, {$this->failCount} failed\n";
        echo "═══════════════════════════════════════════════════════════════\n\n";

        if ($this->failCount === 0) {
            echo "\033[32m✓ All checks passed! Migration status is correct.\033[0m\n\n";
            exit(0);
        } else {
            echo "\033[31m✗ Some checks failed. Please review the issues above.\033[0m\n\n";
            exit(1);
        }
    }
}

$report = new VerificationReport();

// Check 1: Database connection
echo "Checking database connection...\n";
try {
    DB::connection()->getPdo();
    $report->check('Database Connection', true, 'Successfully connected to database');
} catch (\Exception $e) {
    $report->check('Database Connection', false, $e->getMessage());
}

// Check 2: Migrations table exists
echo "Checking migrations table...\n";
$migrationsTableExists = DB::connection()->getSchemaBuilder()->hasTable('migrations');
$report->check('Migrations Table', $migrationsTableExists, 'Table exists');

// Check 3: Migration records for batch 100
echo "Checking migration records...\n";
$batchCount = DB::table('migrations')->where('batch', 100)->count();
$report->check(
    'Migration Records (Batch 100)',
    $batchCount >= 18,
    "Found {$batchCount} migrations in batch 100 (expected >= 18)"
);

// Check 4: Packages table exists
echo "Checking packages table...\n";
$packagesTableExists = DB::connection()->getSchemaBuilder()->hasTable('packages');
$report->check('Packages Table', $packagesTableExists, 'Table exists');

// Check 5: Package record exists
echo "Checking default package...\n";
try {
    $package = Package::find(1);
    $report->check(
        'Default Package (ID=1)',
        $package !== null,
        $package ? "Name: {$package->name}" : 'Package not found'
    );
} catch (\Exception $e) {
    $report->check('Default Package (ID=1)', false, $e->getMessage());
}

// Check 6: Schools table exists
echo "Checking schools table...\n";
$schoolsTableExists = DB::connection()->getSchemaBuilder()->hasTable('schools');
$report->check('Schools Table', $schoolsTableExists, 'Table exists');

// Check 7: School record exists
echo "Checking main school...\n";
try {
    $school = School::find(1);
    $schoolExists = $school !== null;
    $report->check(
        'Main School (ID=1)',
        $schoolExists,
        $school ? "Name: {$school->name}, Email: {$school->email}" : 'School not found'
    );

    if ($schoolExists) {
        $report->check(
            'School Name',
            $school->name === 'Main School',
            "Current: {$school->name}"
        );
        $report->check(
            'School Email',
            $school->email === 'admin@mainschool.com',
            "Current: {$school->email}"
        );
        $report->check(
            'School Status',
            $school->status === 1,
            'Status should be 1 (Active)'
        );
    }
} catch (\Exception $e) {
    $report->check('Main School (ID=1)', false, $e->getMessage());
}

// Check 8: Subscriptions table exists
echo "Checking subscriptions table...\n";
$subscriptionsTableExists = DB::connection()->getSchemaBuilder()->hasTable('subscriptions');
$report->check('Subscriptions Table', $subscriptionsTableExists, 'Table exists');

// Check 9: Subscription record exists
echo "Checking subscription for main school...\n";
try {
    $subscription = Subscription::where('school_id', 1)->first();
    $subscriptionExists = $subscription !== null;
    $report->check(
        'School Subscription',
        $subscriptionExists,
        $subscription ? "ID: {$subscription->id}, Expiry: {$subscription->expiry_date}" : 'Subscription not found'
    );

    if ($subscriptionExists) {
        $report->check(
            'Subscription Status',
            $subscription->status === 1,
            'Status should be 1 (Approved)'
        );
        $report->check(
            'Subscription Payment Status',
            $subscription->payment_status === 1,
            'Payment status should be 1 (Paid)'
        );
        $isExpired = now()->isAfter($subscription->expiry_date);
        $report->check(
            'Subscription Not Expired',
            !$isExpired,
            "Expires: {$subscription->expiry_date->format('Y-m-d H:i:s')}"
        );
    }
} catch (\Exception $e) {
    $report->check('School Subscription', false, $e->getMessage());
}

// Check 10: Package-School relationship
echo "Checking package-school relationship...\n";
try {
    $school = School::with('package')->find(1);
    $hasPackage = $school && $school->package !== null;
    $report->check(
        'School Package Relationship',
        $hasPackage,
        $school && $school->package ? "Package: {$school->package->name}" : 'Package not linked'
    );
} catch (\Exception $e) {
    $report->check('School Package Relationship', false, $e->getMessage());
}

// Check 11: Count critical migrations
echo "Verifying critical migrations...\n";
$criticalMigrations = [
    'packages' => false,
    'schools' => false,
    'subscriptions' => false,
    'users' => false,
    'jobs' => false,
];

$allMigrations = DB::table('migrations')->pluck('migration')->toArray();
foreach ($criticalMigrations as $key => &$value) {
    $value = collect($allMigrations)->contains(function ($migration) use ($key) {
        return str_contains($migration, $key);
    });
}

$report->check(
    'Critical Migrations Present',
    collect($criticalMigrations)->every(fn($value) => $value === true),
    'All critical migration records found'
);

// Print the final report
$report->print();
