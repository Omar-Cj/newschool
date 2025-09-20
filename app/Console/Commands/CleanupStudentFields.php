<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Schema\Blueprint;

class CleanupStudentFields extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'students:cleanup-fields
                           {--dry-run : Run the command without making changes}
                           {--force : Skip confirmation prompts}
                           {--backup : Create backup before dropping columns}';

    /**
     * The console command description.
     */
    protected $description = 'Remove deprecated/unnecessary fields from students table';

    /**
     * Fields to be removed from students table
     */
    private array $fieldsToRemove = [
        'student_ar_name',        // Arabic Name
        'nationality',            // Student Nationality
        'cpr_no',                // CPR Number
        'spoken_lang_at_home',    // Student Spoken Language At Home
        'student_id_certificate', // ID Certificate
        'emergency_contact',      // Emergency Contact
        'health_status',         // Health Status
        'rank_in_family',        // Rank in Family
        'siblings',              // Number of brothers/sisters
    ];

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('🧹 Student Table Cleanup Tool');
        $this->info('===============================');

        $dryRun = $this->option('dry-run');
        $force = $this->option('force');
        $backup = $this->option('backup');

        if ($dryRun) {
            $this->warn('🔍 DRY RUN MODE - No changes will be made');
        }

        // Check current table structure
        $this->info('📋 Analyzing current table structure...');
        $currentColumns = $this->getCurrentColumns();
        $existingFields = array_intersect($this->fieldsToRemove, $currentColumns);
        $nonExistentFields = array_diff($this->fieldsToRemove, $currentColumns);

        $this->displayAnalysis($existingFields, $nonExistentFields, $currentColumns);

        if (empty($existingFields)) {
            $this->info('✅ No deprecated fields found to remove.');
            return 0;
        }

        if (!$force && !$dryRun) {
            $this->warn('⚠️  This action will permanently remove data from the following fields:');
            foreach ($existingFields as $field) {
                $this->line("  • {$field}");
            }

            if (!$this->confirm('Are you sure you want to proceed?')) {
                $this->info('Operation cancelled.');
                return 0;
            }
        }

        try {
            if ($backup && !$dryRun) {
                $this->createBackup($existingFields);
            }

            $this->removeFields($existingFields, $dryRun);

            if (!$dryRun) {
                $this->info('✅ Cleanup completed successfully!');
                $this->validateCleanup($existingFields);
            } else {
                $this->info('🔍 Dry run completed. Use --force to execute the cleanup.');
            }

            return 0;
        } catch (\Exception $e) {
            $this->error('❌ Cleanup failed: ' . $e->getMessage());
            return 1;
        }
    }

    /**
     * Get current table columns
     */
    private function getCurrentColumns(): array
    {
        return Schema::getColumnListing('students');
    }

    /**
     * Display analysis of current state
     */
    private function displayAnalysis(array $existingFields, array $nonExistentFields, array $currentColumns): void
    {
        $this->table(
            ['Metric', 'Count'],
            [
                ['Total Columns', count($currentColumns)],
                ['Fields to Remove (Found)', count($existingFields)],
                ['Fields to Remove (Not Found)', count($nonExistentFields)],
                ['Columns After Cleanup', count($currentColumns) - count($existingFields)],
            ]
        );

        if (!empty($existingFields)) {
            $this->info('🎯 Fields that will be removed:');
            $fieldData = [];
            foreach ($existingFields as $field) {
                $dataCount = $this->getDataCount($field);
                $fieldData[] = [$field, $this->getFieldDescription($field), number_format($dataCount)];
            }
            $this->table(['Field Name', 'Description', 'Records with Data'], $fieldData);
        }

        if (!empty($nonExistentFields)) {
            $this->warn('⚠️  Fields not found in table:');
            foreach ($nonExistentFields as $field) {
                $this->line("  • {$field}");
            }
        }
    }

    /**
     * Get field description
     */
    private function getFieldDescription(string $field): string
    {
        $descriptions = [
            'student_ar_name' => 'Arabic Name',
            'nationality' => 'Student Nationality',
            'cpr_no' => 'CPR Number',
            'spoken_lang_at_home' => 'Language Spoken At Home',
            'student_id_certificate' => 'ID Certificate',
            'emergency_contact' => 'Emergency Contact',
            'health_status' => 'Health Status',
            'rank_in_family' => 'Rank in Family',
            'siblings' => 'Number of Siblings',
        ];

        return $descriptions[$field] ?? 'Unknown field';
    }

    /**
     * Get count of records with data in field
     */
    private function getDataCount(string $field): int
    {
        try {
            return DB::table('students')
                ->whereNotNull($field)
                ->where($field, '!=', '')
                ->count();
        } catch (\Exception $e) {
            return 0;
        }
    }

    /**
     * Create backup of data before removal
     */
    private function createBackup(array $fields): void
    {
        $this->info('💾 Creating backup of student data...');

        $timestamp = now()->format('Y_m_d_H_i_s');
        $backupTable = "students_backup_{$timestamp}";

        // Create backup table with only the fields being removed
        $fieldsToSelect = array_merge(['id'], $fields);
        $selectFields = implode(', ', $fieldsToSelect);

        DB::statement("CREATE TABLE {$backupTable} AS SELECT {$selectFields} FROM students WHERE 1=1");

        $recordCount = DB::table($backupTable)->count();
        $this->info("✅ Backup created: {$backupTable} ({$recordCount} records)");

        // Create a restoration script
        $this->createRestorationScript($backupTable, $fields);
    }

    /**
     * Create restoration script
     */
    private function createRestorationScript(string $backupTable, array $fields): void
    {
        $timestamp = now()->format('Y_m_d_H_i_s');
        $scriptPath = database_path("backups/restore_student_fields_{$timestamp}.sql");

        // Ensure backup directory exists
        if (!is_dir(dirname($scriptPath))) {
            mkdir(dirname($scriptPath), 0755, true);
        }

        $content = "-- Student Fields Restoration Script\n";
        $content .= "-- Generated: " . now()->toDateTimeString() . "\n";
        $content .= "-- Backup Table: {$backupTable}\n\n";

        foreach ($fields as $field) {
            $content .= "-- Restore {$field} field\n";
            $content .= "ALTER TABLE students ADD COLUMN {$field} TEXT;\n";
            $content .= "UPDATE students s SET {$field} = (SELECT {$field} FROM {$backupTable} b WHERE b.id = s.id);\n\n";
        }

        file_put_contents($scriptPath, $content);
        $this->info("📝 Restoration script created: {$scriptPath}");
    }

    /**
     * Remove the specified fields
     */
    private function removeFields(array $fields, bool $dryRun): void
    {
        if ($dryRun) {
            $this->info('🔍 [DRY RUN] Would remove the following fields:');
            foreach ($fields as $field) {
                $this->line("  • {$field}");
            }
            return;
        }

        $this->info('🗑️  Removing deprecated fields...');

        Schema::table('students', function (Blueprint $table) use ($fields) {
            foreach ($fields as $field) {
                $this->line("  Dropping column: {$field}");
                $table->dropColumn($field);
            }
        });

        $this->info('✅ Fields removed successfully.');
    }

    /**
     * Validate cleanup was successful
     */
    private function validateCleanup(array $removedFields): void
    {
        $this->info('🔍 Validating cleanup...');

        $currentColumns = $this->getCurrentColumns();
        $stillExisting = array_intersect($removedFields, $currentColumns);

        if (empty($stillExisting)) {
            $this->info('✅ All specified fields have been successfully removed.');
        } else {
            $this->warn('⚠️  Some fields still exist:');
            foreach ($stillExisting as $field) {
                $this->line("  • {$field}");
            }
        }

        // Show final column count
        $this->info("📊 Final table structure: " . count($currentColumns) . " columns");
    }
}