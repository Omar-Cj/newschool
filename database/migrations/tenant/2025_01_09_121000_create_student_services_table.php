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
        Schema::create('student_services', function (Blueprint $table) {
            $table->id();
            
            // Core relationships
            $table->foreignId('student_id')
                  ->constrained('students')
                  ->cascadeOnDelete()
                  ->comment('Student subscribed to this service');
            
            $table->foreignId('fee_type_id')
                  ->constrained('fees_types')
                  ->cascadeOnDelete()
                  ->comment('Service/fee type being subscribed to');
            
            $table->foreignId('academic_year_id')
                  ->constrained('sessions')
                  ->cascadeOnDelete()
                  ->comment('Academic year this subscription applies to');
            
            // Service pricing and customization
            $table->decimal('amount', 16, 2)
                  ->default(0.00)
                  ->comment('Custom amount - can override fees_types.amount');
            
            $table->date('due_date')
                  ->nullable()
                  ->comment('Calculated or custom due date for this service');
            
            // Comprehensive discount system
            $table->enum('discount_type', ['none', 'percentage', 'fixed', 'override'])
                  ->default('none')
                  ->comment('Type of discount applied to this service');
            
            $table->decimal('discount_value', 16, 2)
                  ->default(0.00)
                  ->comment('Discount amount or percentage value');
            
            $table->decimal('final_amount', 16, 2)
                  ->default(0.00)
                  ->comment('Final calculated amount after applying discounts');
            
            // Subscription management
            $table->timestamp('subscription_date')
                  ->nullable()
                  ->comment('When this service was assigned to the student');
            
            $table->boolean('is_active')
                  ->default(true)
                  ->comment('Whether this service subscription is currently active');
            
            $table->text('notes')
                  ->nullable()
                  ->comment('Reason for discount, special conditions, or admin notes');
            
            // Audit trail
            $table->foreignId('created_by')
                  ->nullable()
                  ->constrained('users')
                  ->nullOnDelete()
                  ->comment('Admin user who created this subscription');
            
            $table->foreignId('updated_by')
                  ->nullable()
                  ->constrained('users')
                  ->nullOnDelete()
                  ->comment('Admin user who last updated this subscription');
            
            $table->timestamps();
            
            // Indexes for performance optimization
            $table->index(['student_id', 'academic_year_id'], 'idx_student_services_student_year');
            $table->index(['fee_type_id', 'is_active'], 'idx_student_services_type_active');
            $table->index(['is_active', 'due_date'], 'idx_student_services_active_due');
            $table->index(['academic_year_id', 'is_active'], 'idx_student_services_year_active');
            $table->index('subscription_date', 'idx_student_services_subscription_date');
            
            // Unique constraint to prevent duplicate subscriptions
            $table->unique(
                ['student_id', 'fee_type_id', 'academic_year_id'], 
                'unq_student_services_student_type_year'
            );
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('student_services');
    }
};