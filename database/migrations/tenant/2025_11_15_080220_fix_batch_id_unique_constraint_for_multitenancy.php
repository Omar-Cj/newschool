<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Fix batch_id unique constraint to be school-scoped instead of globally unique.
     * This allows each school to have its own BATCH_ID_1, BATCH_ID_2, etc.
     */
    public function up(): void
    {
        Schema::table('fees_generations', function (Blueprint $table) {
            // Drop the existing global unique constraint on batch_id
            $table->dropUnique(['batch_id']);

            // Add composite unique constraint for (batch_id, school_id)
            // This allows each school to have independent batch_id sequences
            $table->unique(['batch_id', 'school_id'], 'fees_generations_batch_school_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('fees_generations', function (Blueprint $table) {
            // Drop the school-scoped unique constraint
            $table->dropUnique('fees_generations_batch_school_unique');

            // Restore the global unique constraint on batch_id
            $table->unique('batch_id');
        });
    }
};
