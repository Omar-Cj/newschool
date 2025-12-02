<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Fix term_definitions unique constraints to be school-scoped.
     * This allows different schools to have the same term codes/names independently.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::table('term_definitions', function (Blueprint $table) {
            // Drop the global unique constraint on code
            $table->dropUnique(['code']);

            // Add school-scoped unique constraints
            // Each school can have their own TERM1, TERM2, etc.
            $table->unique(['code', 'school_id'], 'term_definitions_code_school_unique');
            $table->unique(['name', 'school_id'], 'term_definitions_name_school_unique');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::table('term_definitions', function (Blueprint $table) {
            // Drop the school-scoped unique constraints
            $table->dropUnique('term_definitions_code_school_unique');
            $table->dropUnique('term_definitions_name_school_unique');

            // Restore the original global unique constraint on code
            $table->unique('code');
        });
    }
};
