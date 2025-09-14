<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * Adds academic_level column to classes table for explicit academic level assignment.
     * This makes the fee assignment system scalable and removes dependency on fragile 
     * name-based detection.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('classes', function (Blueprint $table) {
            $table->enum('academic_level', ['kg', 'primary', 'secondary', 'high_school'])
                  ->nullable()
                  ->after('name')
                  ->comment('Explicitly assigned academic level for this class');
        });

        // Add index for performance
        Schema::table('classes', function (Blueprint $table) {
            $table->index('academic_level', 'idx_classes_academic_level');
        });

        \Log::info('Added academic_level column to classes table', [
            'migration' => '2025_01_11_110000_add_academic_level_to_classes_table',
            'column' => 'academic_level',
            'type' => 'enum(kg, primary, secondary, high_school)'
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('classes', function (Blueprint $table) {
            $table->dropIndex('idx_classes_academic_level');
            $table->dropColumn('academic_level');
        });

        \Log::info('Removed academic_level column from classes table');
    }
};