<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('fees_generations', function (Blueprint $table) {
            // Add branch_id foreign key column
            $table->foreignId('branch_id')
                  ->default(1)
                  ->after('school_id')
                  ->constrained('branches')
                  ->cascadeOnDelete();
        });

        // Update existing records to have proper branch relationships
        // Set all existing generations to branch_id = 1 (default branch)
        DB::table('fees_generations')
            ->whereNull('branch_id')
            ->orWhere('branch_id', 0)
            ->update(['branch_id' => 1]);

        Schema::table('fees_generations', function (Blueprint $table) {
            // Add composite indexes for performance
            $table->index(['branch_id', 'status'], 'idx_fees_generations_branch_status');
            $table->index(['branch_id', 'created_at'], 'idx_fees_generations_branch_created');
            $table->index(['branch_id', 'school_id'], 'idx_fees_generations_branch_school');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('fees_generations', function (Blueprint $table) {
            // Drop indexes first
            $table->dropIndex('idx_fees_generations_branch_status');
            $table->dropIndex('idx_fees_generations_branch_created');
            $table->dropIndex('idx_fees_generations_branch_school');

            // Drop foreign key constraint and column
            $table->dropForeign(['branch_id']);
            $table->dropColumn('branch_id');
        });
    }
};