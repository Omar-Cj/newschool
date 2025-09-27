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
        Schema::create('receipt_allocations', function (Blueprint $table) {
            $table->id();

            // Core allocation information
            $table->foreignId('receipt_id')->constrained()->cascadeOnDelete();
            $table->foreignId('fees_collect_id')->constrained()->cascadeOnDelete();

            // Fee identification
            $table->string('fee_name');
            $table->string('fee_type')->nullable();

            // Allocation amounts
            $table->decimal('allocated_amount', 10, 2);
            $table->decimal('allocation_percentage', 5, 2);

            // Fee context for transparency
            $table->decimal('fee_total_amount', 10, 2);
            $table->decimal('fee_balance_before', 10, 2);
            $table->decimal('fee_balance_after', 10, 2);

            // Allocation metadata
            $table->tinyInteger('allocation_order')->default(1);
            $table->enum('allocation_method', [
                'proportional',
                'priority',
                'chronological',
                'manual',
                'full_payment'
            ]);

            // Additional information
            $table->text('notes')->nullable();

            $table->timestamps();

            // Indexes for performance and reporting
            $table->index(['receipt_id', 'allocation_order']);
            $table->index('fees_collect_id');
            $table->index('allocation_method');
            $table->index('allocated_amount');

            // Ensure allocation integrity
            $table->unique(['receipt_id', 'fees_collect_id'], 'unique_receipt_fee_allocation');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('receipt_allocations');
    }
};