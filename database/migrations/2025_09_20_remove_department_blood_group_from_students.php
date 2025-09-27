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
        Schema::table('students', function (Blueprint $table) {
            // Drop foreign key constraints first
            $table->dropForeign(['department_id']);
            $table->dropForeign(['blood_group_id']);
            
            // Drop the columns
            $table->dropColumn(['department_id', 'blood_group_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('students', function (Blueprint $table) {
            // Add columns back
            $table->foreignId('department_id')->nullable()->constrained('departments');
            $table->foreignId('blood_group_id')->nullable()->constrained('blood_groups')->cascadeOnDelete();
        });
    }
};
