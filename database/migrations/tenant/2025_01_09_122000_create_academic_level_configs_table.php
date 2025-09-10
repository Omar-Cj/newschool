<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('academic_level_configs', function (Blueprint $table) {
            $table->id();
            
            // Academic level definition
            $table->enum('academic_level', ['primary', 'secondary', 'high_school', 'kg'])
                  ->comment('Academic level being configured');
            
            $table->string('display_name', 100)
                  ->comment('Human-readable name for this academic level (e.g., "Elementary School", "Middle School")');
            
            $table->text('description')
                  ->nullable()
                  ->comment('Optional description of what this academic level represents');
            
            // Class/Grade mapping
            $table->json('class_identifiers')
                  ->comment('JSON array of class names/numbers that belong to this academic level');
            
            $table->json('numeric_range')
                  ->nullable()
                  ->comment('JSON object with min/max numeric class values for easy range checking');
            
            // Configuration options
            $table->integer('sort_order')
                  ->default(0)
                  ->comment('Display order for academic levels in interfaces');
            
            $table->boolean('is_active')
                  ->default(true)
                  ->comment('Whether this academic level configuration is currently active');
            
            $table->boolean('auto_assign_mandatory_services')
                  ->default(true)
                  ->comment('Automatically assign mandatory services when students are assigned to this level');
            
            // Audit trail
            $table->foreignId('created_by')
                  ->nullable()
                  ->constrained('users')
                  ->nullOnDelete()
                  ->comment('Admin user who created this configuration');
            
            $table->foreignId('updated_by')
                  ->nullable()
                  ->constrained('users')
                  ->nullOnDelete()
                  ->comment('Admin user who last updated this configuration');
            
            $table->timestamps();
            
            // Indexes for performance
            $table->index('academic_level', 'idx_academic_level_configs_level');
            $table->index(['is_active', 'sort_order'], 'idx_academic_level_configs_active_sort');
        });
        
        // Insert default academic level configurations
        DB::table('academic_level_configs')->insert([
            [
                'academic_level' => 'kg',
                'display_name' => 'Kindergarten',
                'description' => 'Pre-school and kindergarten students',
                'class_identifiers' => json_encode(['KG', 'PreK', 'Pre-K', 'Nursery', 'Pre-School']),
                'numeric_range' => json_encode(['min' => 0, 'max' => 0]),
                'sort_order' => 1,
                'is_active' => true,
                'auto_assign_mandatory_services' => true,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'academic_level' => 'primary',
                'display_name' => 'Primary School',
                'description' => 'Elementary/Primary education levels',
                'class_identifiers' => json_encode(['1', '2', '3', '4', '5', 'Class 1', 'Class 2', 'Class 3', 'Class 4', 'Class 5']),
                'numeric_range' => json_encode(['min' => 1, 'max' => 5]),
                'sort_order' => 2,
                'is_active' => true,
                'auto_assign_mandatory_services' => true,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'academic_level' => 'secondary',
                'display_name' => 'Secondary School',
                'description' => 'Middle and junior secondary education levels',
                'class_identifiers' => json_encode(['6', '7', '8', '9', '10', 'Class 6', 'Class 7', 'Class 8', 'Class 9', 'Class 10']),
                'numeric_range' => json_encode(['min' => 6, 'max' => 10]),
                'sort_order' => 3,
                'is_active' => true,
                'auto_assign_mandatory_services' => true,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'academic_level' => 'high_school',
                'display_name' => 'High School',
                'description' => 'Senior secondary and high school levels',
                'class_identifiers' => json_encode(['11', '12', 'Class 11', 'Class 12', 'Grade 11', 'Grade 12']),
                'numeric_range' => json_encode(['min' => 11, 'max' => 12]),
                'sort_order' => 4,
                'is_active' => true,
                'auto_assign_mandatory_services' => true,
                'created_at' => now(),
                'updated_at' => now()
            ]
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('academic_level_configs');
    }
};