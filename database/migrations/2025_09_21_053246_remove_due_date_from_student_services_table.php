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
        Schema::table('student_services', function (Blueprint $table) {
            // Drop the index that includes due_date first
            $table->dropIndex('idx_student_services_active_due');

            // Remove the due_date column
            $table->dropColumn('due_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('student_services', function (Blueprint $table) {
            // Add the due_date column back
            $table->date('due_date')
                  ->nullable()
                  ->comment('Calculated or custom due date for this service')
                  ->after('amount');

            // Recreate the index
            $table->index(['is_active', 'due_date'], 'idx_student_services_active_due');
        });
    }
};
