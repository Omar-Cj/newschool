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
        Schema::create('receipts', function (Blueprint $table) {
            $table->id();

            // Core receipt information
            $table->string('receipt_number', 20)->unique();
            $table->foreignId('student_id')->constrained()->cascadeOnDelete();
            $table->date('payment_date');
            $table->decimal('total_amount', 10, 2);

            // Payment details
            $table->tinyInteger('payment_method');
            $table->json('payment_method_details')->nullable();
            $table->string('transaction_reference')->nullable();
            $table->foreignId('collected_by')->constrained('users');

            // Receipt classification
            $table->enum('receipt_type', ['payment', 'partial_payment', 'refund', 'adjustment']);
            $table->enum('payment_status', ['completed', 'partial', 'refunded', 'voided']);

            // Additional information
            $table->text('notes')->nullable();
            $table->json('receipt_data')->nullable();

            // Polymorphic relationship to source (PaymentTransaction or FeesCollect)
            $table->string('source_type');
            $table->unsignedBigInteger('source_id');
            $table->index(['source_type', 'source_id']);

            // Organizational context
            $table->foreignId('branch_id')->nullable()->constrained('branches')->nullOnDelete();
            $table->foreignId('academic_year_id')->nullable()->constrained('sessions');
            $table->foreignId('session_id')->nullable()->constrained('sessions');

            // Voiding/reversal information
            $table->timestamp('voided_at')->nullable();
            $table->foreignId('voided_by')->nullable()->constrained('users');
            $table->string('void_reason')->nullable();

            $table->timestamps();

            // Indexes for performance
            $table->index('receipt_number');
            $table->index('payment_date');
            $table->index(['student_id', 'payment_date']);
            $table->index(['payment_method', 'payment_date']);
            $table->index(['collected_by', 'payment_date']);
            $table->index('receipt_type');
            $table->index('payment_status');
            $table->index('voided_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('receipts');
    }
};