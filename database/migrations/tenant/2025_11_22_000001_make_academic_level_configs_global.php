<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Makes academic_level_configs table global (system-wide) instead of per-school.
     */
    public function up(): void
    {
        // Step 1: Deduplicate data - keep only one record per academic_level
        // Delete duplicates, keeping the one with the lowest id
        DB::statement("
            DELETE t1 FROM academic_level_configs t1
            INNER JOIN academic_level_configs t2
            WHERE t1.id > t2.id AND t1.academic_level = t2.academic_level
        ");

        // Step 2: Remove school_id column (MySQL auto-drops associated indexes)
        Schema::table('academic_level_configs', function (Blueprint $table) {
            $table->dropColumn('school_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('academic_level_configs', function (Blueprint $table) {
            // Re-add school_id column
            $table->unsignedBigInteger('school_id')->default(1)->after('id');
            $table->index('school_id');
        });
    }
};
