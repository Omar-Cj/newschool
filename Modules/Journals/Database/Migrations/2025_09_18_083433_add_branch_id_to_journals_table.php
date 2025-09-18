<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('journals', function (Blueprint $table) {
            // Add branch_id foreign key column (nullable initially for migration)
            $table->unsignedBigInteger('branch_id')->nullable()->after('name');

            // Add foreign key constraint
            $table->foreign('branch_id')
                  ->references('id')
                  ->on('branches')
                  ->onUpdate('cascade')
                  ->onDelete('restrict'); // Prevent deletion of branches with journals

            // Add index for better query performance
            $table->index('branch_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('journals', function (Blueprint $table) {
            // Drop foreign key constraint first
            $table->dropForeign(['branch_id']);

            // Drop the column
            $table->dropColumn('branch_id');
        });
    }
};