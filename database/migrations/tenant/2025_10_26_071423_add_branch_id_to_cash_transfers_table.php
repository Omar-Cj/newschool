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
        Schema::table('cash_transfers', function (Blueprint $table) {
            // Step 1: Add nullable branch_id column
            $table->unsignedBigInteger('branch_id')->nullable()->after('journal_id');
        });

        // Step 2: Backfill branch_id from journals
        DB::statement('
            UPDATE cash_transfers ct
            INNER JOIN journals j ON ct.journal_id = j.id
            SET ct.branch_id = j.branch_id
        ');

        Schema::table('cash_transfers', function (Blueprint $table) {
            // Step 3: Make branch_id NOT NULL after population
            $table->unsignedBigInteger('branch_id')->nullable(false)->change();

            // Step 4: Add foreign key constraint and index
            $table->foreign('branch_id')
                  ->references('id')
                  ->on('branches')
                  ->onDelete('cascade');

            $table->index('branch_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('cash_transfers', function (Blueprint $table) {
            // Drop foreign key and index first
            $table->dropForeign(['branch_id']);
            $table->dropIndex(['branch_id']);

            // Drop the column
            $table->dropColumn('branch_id');
        });
    }
};
