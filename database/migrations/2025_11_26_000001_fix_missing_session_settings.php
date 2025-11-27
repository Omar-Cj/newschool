<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * Fix missing session settings for schools created before the fix
 *
 * Background: Due to an order-of-operations bug in SchoolRepository::storeSchool(),
 * settings were seeded before sessions existed, causing the 'session' setting to be skipped.
 * This migration inserts the missing session settings for all affected schools.
 *
 * Related Issue: ClassSetup creation fails with "Column 'session_id' cannot be null"
 * Root Cause: setting('session') returns null because no 'session' setting exists
 *
 * @see Modules\MainApp\Http\Repositories\SchoolRepository::seedSchoolSettings()
 * @see Modules\MainApp\Services\BranchDataSeederService::seedAcademicSession()
 */
return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Log::info('Starting migration: fix_missing_session_settings');

        try {
            // Find all schools with active sessions but no 'session' setting
            // For each school/branch combination, insert the active session ID
            $affected = DB::statement("
                INSERT INTO settings (school_id, branch_id, name, value, created_at, updated_at)
                SELECT DISTINCT
                    s.school_id,
                    s.branch_id,
                    'session' as name,
                    s.id as value,
                    NOW() as created_at,
                    NOW() as updated_at
                FROM sessions s
                WHERE s.school_id IS NOT NULL
                AND s.status = 1
                AND NOT EXISTS (
                    SELECT 1 FROM settings st
                    WHERE st.school_id = s.school_id
                    AND st.branch_id = s.branch_id
                    AND st.name = 'session'
                )
                ORDER BY s.school_id, s.branch_id, s.id DESC
            ");

            // Get count of affected schools for logging
            $count = DB::select("
                SELECT COUNT(DISTINCT s.school_id) as school_count
                FROM sessions s
                WHERE s.school_id IS NOT NULL
                AND s.status = 1
                AND NOT EXISTS (
                    SELECT 1 FROM settings st
                    WHERE st.school_id = s.school_id
                    AND st.branch_id = s.branch_id
                    AND st.name = 'session'
                )
            ");

            $schoolCount = $count[0]->school_count ?? 0;

            Log::info('Migration completed: fix_missing_session_settings', [
                'affected_schools' => $schoolCount,
                'status' => 'success'
            ]);

            if ($schoolCount > 0) {
                echo "\n✓ Fixed missing session settings for {$schoolCount} school(s)\n";
            } else {
                echo "\n✓ No missing session settings found (all schools already have session settings)\n";
            }

        } catch (\Throwable $th) {
            Log::error('Migration failed: fix_missing_session_settings', [
                'error' => $th->getMessage(),
                'trace' => $th->getTraceAsString()
            ]);

            throw $th;
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Intentionally left empty
        // We don't want to accidentally delete valid session settings
        // that may have been created through normal operations

        Log::info('Migration rollback: fix_missing_session_settings (no-op)');
    }
};
