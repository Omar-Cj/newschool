<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * This migration adds the 'use_enhanced_fee_system' setting to all schools
     * that don't already have it. This is necessary because this setting was
     * added after some schools were already created.
     */
    public function up(): void
    {
        // Get all unique school_id + branch_id combinations that don't have the setting
        $schoolsWithoutSetting = DB::table('settings')
            ->select('school_id', 'branch_id')
            ->groupBy('school_id', 'branch_id')
            ->whereNotExists(function ($query) {
                $query->select(DB::raw(1))
                    ->from('settings as s2')
                    ->whereRaw('s2.school_id = settings.school_id')
                    ->whereRaw('s2.branch_id = settings.branch_id')
                    ->where('s2.name', 'use_enhanced_fee_system');
            })
            ->get();

        $now = now();
        $inserted = 0;

        foreach ($schoolsWithoutSetting as $school) {
            DB::table('settings')->insert([
                'school_id' => $school->school_id,
                'branch_id' => $school->branch_id,
                'name' => 'use_enhanced_fee_system',
                'value' => '1',
                'created_at' => $now,
                'updated_at' => $now,
            ]);
            $inserted++;
        }

        if ($inserted > 0) {
            \Log::info('Added use_enhanced_fee_system setting to existing schools', [
                'schools_updated' => $inserted
            ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('settings')
            ->where('name', 'use_enhanced_fee_system')
            ->delete();
    }
};
