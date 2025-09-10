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
        Schema::table('fees_types', function (Blueprint $table) {
            // Academic level targeting - determines which students this fee applies to
            $table->enum('academic_level', ['primary', 'secondary', 'high_school', 'kg', 'all'])
                  ->default('all')
                  ->after('description')
                  ->comment('Academic level this fee type applies to');
            
            // Base amount for this service
            $table->decimal('amount', 16, 2)
                  ->default(0.00)
                  ->after('academic_level')
                  ->comment('Default/base amount for this service');
            
            // Due date calculation from term start
            $table->integer('due_date_offset')
                  ->default(30)
                  ->after('amount')
                  ->comment('Days from term start when this fee is due');
            
            // Whether this service is mandatory for the specified academic level
            $table->boolean('is_mandatory_for_level')
                  ->default(false)
                  ->after('due_date_offset')
                  ->comment('Required for students in the specified academic level');
            
            // Service categorization for better organization
            $table->enum('category', ['academic', 'transport', 'meal', 'accommodation', 'activity', 'other'])
                  ->default('academic')
                  ->after('is_mandatory_for_level')
                  ->comment('Category of service for organization');
        });
        
        // Add indexes for performance optimization
        Schema::table('fees_types', function (Blueprint $table) {
            $table->index(['academic_level', 'status'], 'idx_fees_types_level_status');
            $table->index(['category', 'status'], 'idx_fees_types_category_status');
            $table->index(['is_mandatory_for_level', 'academic_level'], 'idx_fees_types_mandatory_level');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('fees_types', function (Blueprint $table) {
            // Drop indexes first
            $table->dropIndex('idx_fees_types_level_status');
            $table->dropIndex('idx_fees_types_category_status');
            $table->dropIndex('idx_fees_types_mandatory_level');
            
            // Drop columns
            $table->dropColumn([
                'academic_level',
                'amount',
                'due_date_offset',
                'is_mandatory_for_level',
                'category'
            ]);
        });
    }
};