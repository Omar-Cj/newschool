<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class VerifySchoolIdColumnsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * Verify which tables have school_id column and report statistics
     */
    public function run()
    {
        $this->command->info('Verifying school_id columns across all tables...');
        $this->command->newLine();

        // Get all tables in the database
        $tables = $this->getAllTables();

        $tablesWithSchoolId = [];
        $tablesWithoutSchoolId = [];
        $tableStats = [];

        foreach ($tables as $table) {
            // Skip migrations and system tables
            if (in_array($table, ['migrations', 'failed_jobs', 'password_resets', 'personal_access_tokens'])) {
                continue;
            }

            if (Schema::hasColumn($table, 'school_id')) {
                $tablesWithSchoolId[] = $table;

                // Get statistics for tables with school_id
                $totalRecords = DB::table($table)->count();
                $nullSchoolId = DB::table($table)->whereNull('school_id')->count();
                $withSchoolId = $totalRecords - $nullSchoolId;

                $tableStats[$table] = [
                    'total' => $totalRecords,
                    'with_school_id' => $withSchoolId,
                    'null_school_id' => $nullSchoolId,
                ];
            } else {
                $tablesWithoutSchoolId[] = $table;
            }
        }

        // Display summary
        $this->command->info('📊 VERIFICATION SUMMARY');
        $this->command->line(str_repeat('=', 60));
        $this->command->line("Total tables checked: " . count($tables));
        $this->command->line("Tables WITH school_id: " . count($tablesWithSchoolId));
        $this->command->line("Tables WITHOUT school_id: " . count($tablesWithoutSchoolId));
        $this->command->newLine();

        // Display tables WITHOUT school_id
        if (count($tablesWithoutSchoolId) > 0) {
            $this->command->error('❌ TABLES MISSING school_id COLUMN:');
            $this->command->line(str_repeat('-', 60));
            foreach ($tablesWithoutSchoolId as $table) {
                $recordCount = DB::table($table)->count();
                $this->command->line("  - {$table} ({$recordCount} records)");
            }
            $this->command->newLine();
        }

        // Display tables WITH school_id and their statistics
        $this->command->info('✅ TABLES WITH school_id COLUMN:');
        $this->command->line(str_repeat('-', 60));

        foreach ($tablesWithSchoolId as $table) {
            $stats = $tableStats[$table];
            $nullPercentage = $stats['total'] > 0
                ? round(($stats['null_school_id'] / $stats['total']) * 100, 2)
                : 0;

            $status = $stats['null_school_id'] > 0 ? '⚠️' : '✓';

            $this->command->line(sprintf(
                "  %s %-30s | Total: %5d | With school_id: %5d | NULL: %5d (%s%%)",
                $status,
                $table,
                $stats['total'],
                $stats['with_school_id'],
                $stats['null_school_id'],
                $nullPercentage
            ));
        }

        $this->command->newLine();

        // Display tables with NULL school_id values that need attention
        $tablesNeedingAttention = array_filter($tableStats, function($stats) {
            return $stats['null_school_id'] > 0 && $stats['total'] > 0;
        });

        if (count($tablesNeedingAttention) > 0) {
            $this->command->warn('⚠️  TABLES WITH NULL school_id VALUES (Need Review):');
            $this->command->line(str_repeat('-', 60));
            foreach ($tablesNeedingAttention as $table => $stats) {
                $this->command->line(sprintf(
                    "  - %-30s | %d of %d records have NULL school_id",
                    $table,
                    $stats['null_school_id'],
                    $stats['total']
                ));
            }
            $this->command->newLine();
        }

        // Export detailed report to file
        $this->exportDetailedReport($tablesWithSchoolId, $tablesWithoutSchoolId, $tableStats);

        $this->command->info('✅ Verification completed!');
        $this->command->info('📄 Detailed report saved to: storage/logs/school_id_verification.txt');
    }

    /**
     * Get all tables in the database
     */
    private function getAllTables(): array
    {
        $database = config('database.connections.mysql.database');

        $tables = DB::select("
            SELECT TABLE_NAME
            FROM information_schema.TABLES
            WHERE TABLE_SCHEMA = ?
            AND TABLE_TYPE = 'BASE TABLE'
            ORDER BY TABLE_NAME
        ", [$database]);

        return array_map(function($table) {
            return $table->TABLE_NAME;
        }, $tables);
    }

    /**
     * Export detailed report to file
     */
    private function exportDetailedReport($withSchoolId, $withoutSchoolId, $stats)
    {
        $reportPath = storage_path('logs/school_id_verification.txt');

        $report = "SCHOOL_ID COLUMN VERIFICATION REPORT\n";
        $report .= "Generated: " . now()->toDateTimeString() . "\n";
        $report .= str_repeat('=', 80) . "\n\n";

        $report .= "SUMMARY\n";
        $report .= str_repeat('-', 80) . "\n";
        $report .= "Tables with school_id: " . count($withSchoolId) . "\n";
        $report .= "Tables without school_id: " . count($withoutSchoolId) . "\n\n";

        $report .= "TABLES WITHOUT school_id COLUMN\n";
        $report .= str_repeat('-', 80) . "\n";
        foreach ($withoutSchoolId as $table) {
            $recordCount = DB::table($table)->count();
            $report .= sprintf("%-40s | %d records\n", $table, $recordCount);
        }
        $report .= "\n";

        $report .= "TABLES WITH school_id COLUMN (Detailed Statistics)\n";
        $report .= str_repeat('-', 80) . "\n";
        $report .= sprintf("%-40s | %10s | %10s | %10s\n", "Table Name", "Total", "With ID", "NULL");
        $report .= str_repeat('-', 80) . "\n";

        foreach ($withSchoolId as $table) {
            $stat = $stats[$table];
            $report .= sprintf(
                "%-40s | %10d | %10d | %10d\n",
                $table,
                $stat['total'],
                $stat['with_school_id'],
                $stat['null_school_id']
            );
        }

        file_put_contents($reportPath, $report);
    }
}
