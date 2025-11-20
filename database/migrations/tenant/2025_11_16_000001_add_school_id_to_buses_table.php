<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Add school_id column to buses table for multi-tenant isolation.
     * This ensures each school can only access their own buses.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::table('buses', function (Blueprint $table) {
            // Add school_id column after id
            $table->unsignedBigInteger('school_id')
                ->default(1)
                ->after('id')
                ->comment('School ID for multi-tenant isolation');

            // Add index for query performance
            $table->index('school_id', 'buses_school_id_index');

            // Drop old unique constraint (without school_id)
            $table->dropUnique('buses_area_name_branch_unique');

            // Add new unique constraint including school_id
            // This ensures area_name is unique per school per branch
            $table->unique(
                ['area_name', 'branch_id', 'school_id'],
                'buses_area_name_branch_school_unique'
            );
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::table('buses', function (Blueprint $table) {
            // Drop new unique constraint
            $table->dropUnique('buses_area_name_branch_school_unique');

            // Restore old unique constraint
            $table->unique(['area_name', 'branch_id'], 'buses_area_name_branch_unique');

            // Drop index
            $table->dropIndex('buses_school_id_index');

            // Drop school_id column
            $table->dropColumn('school_id');
        });
    }
};
