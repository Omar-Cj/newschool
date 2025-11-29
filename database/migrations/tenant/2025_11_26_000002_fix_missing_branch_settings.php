<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * For each school, copy settings from main branch to other branches
     * that are missing their own settings.
     *
     * @return void
     */
    public function up(): void
    {
        Log::info('Starting migration: fix_missing_branch_settings');

        try {
            // Get all schools
            $schools = DB::table('schools')->get();

            foreach ($schools as $school) {
                // Get all branches for this school
                $branches = DB::table('branches')
                    ->where('school_id', $school->id)
                    ->orderBy('id')
                    ->get();

                if ($branches->count() <= 1) {
                    continue; // Skip schools with only one branch
                }

                $mainBranch = $branches->first();

                // Get settings from main branch
                $mainSettings = DB::table('settings')
                    ->where('school_id', $school->id)
                    ->where('branch_id', $mainBranch->id)
                    ->get();

                if ($mainSettings->isEmpty()) {
                    Log::warning("No settings found for school {$school->id} main branch {$mainBranch->id}");
                    continue;
                }

                // Copy to other branches
                foreach ($branches->skip(1) as $branch) {
                    foreach ($mainSettings as $setting) {
                        // Check if setting already exists - use NOT EXISTS for idempotency
                        $exists = DB::table('settings')
                            ->where('school_id', $school->id)
                            ->where('branch_id', $branch->id)
                            ->where('name', $setting->name)
                            ->exists();

                        if (!$exists) {
                            DB::table('settings')->insert([
                                'school_id' => $school->id,
                                'branch_id' => $branch->id,
                                'name' => $setting->name,
                                'value' => $setting->value,
                                'created_at' => now(),
                                'updated_at' => now(),
                            ]);
                        }
                    }
                }

                Log::info("Copied settings from main branch to " . ($branches->count() - 1) . " branches for school {$school->id}");
            }

            Log::info('Migration completed: fix_missing_branch_settings');
        } catch (\Throwable $th) {
            Log::error('Migration failed: fix_missing_branch_settings', [
                'error' => $th->getMessage(),
                'trace' => $th->getTraceAsString(),
            ]);
            throw $th;
        }
    }

    /**
     * Reverse the migrations.
     *
     * No-op for safety - we don't want to delete settings
     *
     * @return void
     */
    public function down(): void
    {
        // No-op for safety - we don't want to delete settings
        Log::info('Migration rollback: fix_missing_branch_settings (no-op)');
    }
};
