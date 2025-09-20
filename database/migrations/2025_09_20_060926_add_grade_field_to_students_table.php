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
            // Add grade field as required enum after student_category_id
            $table->enum('grade', [
                'KG-1', 'KG-2', 'Grade1', 'Grade2', 'Grade3', 'Grade4',
                'Grade5', 'Grade6', 'Grade7', 'Grade8', 'Form1', 'Form2', 'Form3', 'Form4'
            ])->after('student_category_id')->comment('Student grade level - required field');

            // Add index for performance optimization
            $table->index('grade', 'idx_students_grade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('students', function (Blueprint $table) {
            // Remove index first
            $table->dropIndex('idx_students_grade');

            // Remove grade field
            $table->dropColumn('grade');
        });
    }
};
