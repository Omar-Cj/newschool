<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * Fixes academic level configurations to match school structure:
     * - Kindergarten (KG): KG-1 to KG-3
     * - Primary: Grade 1 to Grade 8  
     * - Secondary: Form 1 to Form 4
     *
     * @return void
     */
    public function up()
    {
        // Update KG level configuration
        DB::table('academic_level_configs')
            ->where('academic_level', 'kg')
            ->update([
                'display_name' => 'Kindergarten',
                'description' => 'Kindergarten students (KG-1 to KG-3)',
                'class_identifiers' => json_encode([
                    'KG', 'KG-1', 'KG-2', 'KG-3', 
                    'PreK', 'Pre-K', 'Nursery', 'Pre-School'
                ]),
                'numeric_range' => json_encode(['min' => -3, 'max' => 0]),
                'updated_at' => now()
            ]);

        // Update Primary level configuration (Grade 1 to Grade 8)
        DB::table('academic_level_configs')
            ->where('academic_level', 'primary')
            ->update([
                'display_name' => 'Primary School',
                'description' => 'Primary education levels (Grade 1 to Grade 8)',
                'class_identifiers' => json_encode([
                    '1', '2', '3', '4', '5', '6', '7', '8',
                    'Class 1', 'Class 2', 'Class 3', 'Class 4', 
                    'Class 5', 'Class 6', 'Class 7', 'Class 8',
                    'Grade 1', 'Grade 2', 'Grade 3', 'Grade 4',
                    'Grade 5', 'Grade 6', 'Grade 7', 'Grade 8'
                ]),
                'numeric_range' => json_encode(['min' => 1, 'max' => 8]),
                'updated_at' => now()
            ]);

        // Update Secondary level configuration (Form 1 to Form 4)
        DB::table('academic_level_configs')
            ->where('academic_level', 'secondary')
            ->update([
                'display_name' => 'Secondary School',
                'description' => 'Secondary education levels (Form 1 to Form 4)',
                'class_identifiers' => json_encode([
                    'Form 1', 'Form 2', 'Form 3', 'Form 4',
                    'F1', 'F2', 'F3', 'F4'
                ]),
                'numeric_range' => json_encode(['min' => 101, 'max' => 104]), // Using 101-104 to avoid conflicts
                'updated_at' => now()
            ]);

        // Update High School level configuration (keep as is for now, may not be used)
        DB::table('academic_level_configs')
            ->where('academic_level', 'high_school')
            ->update([
                'display_name' => 'High School',
                'description' => 'High school levels (if applicable)',
                'class_identifiers' => json_encode([
                    '11', '12', 'Class 11', 'Class 12', 'Grade 11', 'Grade 12'
                ]),
                'numeric_range' => json_encode(['min' => 11, 'max' => 12]),
                'updated_at' => now()
            ]);

        // Log the changes made
        \Log::info('Academic level configurations updated successfully', [
            'kg_range' => '[-3, 0] for KG-1 to KG-3',
            'primary_range' => '[1, 8] for Grade 1 to Grade 8', 
            'secondary_range' => '[101, 104] for Form 1 to Form 4',
            'migration' => '2025_01_11_100000_fix_academic_level_configurations'
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Restore original configurations
        DB::table('academic_level_configs')
            ->where('academic_level', 'kg')
            ->update([
                'display_name' => 'Kindergarten',
                'description' => 'Pre-school and kindergarten students',
                'class_identifiers' => json_encode(['KG', 'PreK', 'Pre-K', 'Nursery', 'Pre-School']),
                'numeric_range' => json_encode(['min' => 0, 'max' => 0]),
                'updated_at' => now()
            ]);

        DB::table('academic_level_configs')
            ->where('academic_level', 'primary')
            ->update([
                'display_name' => 'Primary School',
                'description' => 'Elementary/Primary education levels',
                'class_identifiers' => json_encode(['1', '2', '3', '4', '5', 'Class 1', 'Class 2', 'Class 3', 'Class 4', 'Class 5']),
                'numeric_range' => json_encode(['min' => 1, 'max' => 5]),
                'updated_at' => now()
            ]);

        DB::table('academic_level_configs')
            ->where('academic_level', 'secondary')
            ->update([
                'display_name' => 'Secondary School',
                'description' => 'Middle and junior secondary education levels',
                'class_identifiers' => json_encode(['6', '7', '8', '9', '10', 'Class 6', 'Class 7', 'Class 8', 'Class 9', 'Class 10']),
                'numeric_range' => json_encode(['min' => 6, 'max' => 10]),
                'updated_at' => now()
            ]);

        \Log::info('Academic level configurations reverted to original state');
    }
};