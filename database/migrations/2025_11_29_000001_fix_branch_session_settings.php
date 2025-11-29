<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

return new class extends Migration
{
    /**
     * Fix session settings for branches that incorrectly share the same session ID.
     *
     * Bug: SchoolRepository::seedSchoolSettings() was querying for active sessions
     * without filtering by branch_id, causing all branches to receive the same
     * session ID instead of their branch-specific session.
     *
     * This migration corrects existing data by updating each branch's session
     * setting to point to its correct branch-specific session.
     */
    public function up(): void
    {
        // Find all branches where the session setting doesn't match the branch's actual session
        $branchesWithWrongSessions = DB::table('settings as s')
            ->join('sessions as sess', function ($join) {
                $join->on('s.school_id', '=', 'sess.school_id')
                     ->on('s.branch_id', '=', 'sess.branch_id');
            })
            ->where('s.name', 'session')
            ->whereColumn('s.value', '!=', DB::raw('CAST(sess.id AS CHAR)'))
            ->where('sess.status', 1)
            ->select('s.id as setting_id', 'sess.id as correct_session_id', 's.school_id', 's.branch_id', 's.value as old_session_id')
            ->get();

        $updatedCount = 0;

        foreach ($branchesWithWrongSessions as $item) {
            DB::table('settings')
                ->where('id', $item->setting_id)
                ->update([
                    'value' => (string) $item->correct_session_id,
                    'updated_at' => now(),
                ]);

            Log::info('Fixed branch session setting', [
                'setting_id' => $item->setting_id,
                'school_id' => $item->school_id,
                'branch_id' => $item->branch_id,
                'old_session_id' => $item->old_session_id,
                'new_session_id' => $item->correct_session_id,
            ]);

            $updatedCount++;
        }

        Log::info('Branch session settings fix migration completed', [
            'total_updated' => $updatedCount,
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * No rollback - this is a data correction migration.
     */
    public function down(): void
    {
        // No rollback needed - this fixes incorrect data
        // Rolling back would reintroduce the bug
    }
};
