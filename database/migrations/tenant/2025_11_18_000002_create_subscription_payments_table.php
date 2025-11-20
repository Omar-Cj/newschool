<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Creates subscription_payments table to track all payment transactions
     * for subscriptions with approval workflow support.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('subscription_payments', function (Blueprint $table) {
            $table->id();

            // Foreign keys for relationships
            $table->foreignId('subscription_id')
                ->constrained('subscriptions')
                ->cascadeOnDelete()
                ->comment('Reference to subscription record');

            $table->foreignId('school_id')
                ->constrained('schools')
                ->cascadeOnDelete()
                ->comment('Reference to school for multi-tenant isolation');

            // Payment details
            $table->decimal('amount', 16, 2)
                ->comment('Payment amount');

            $table->string('payment_method', 50)
                ->comment('Payment method (e.g., bank_transfer, credit_card, paypal)');

            $table->string('transaction_id', 255)
                ->nullable()
                ->comment('External payment gateway transaction ID');

            $table->string('reference_number', 255)
                ->nullable()
                ->comment('Internal or external payment reference number');

            // Approval workflow
            $table->tinyInteger('status')
                ->default(0)
                ->comment('Payment status: 0=pending, 1=approved, 2=rejected');

            $table->foreignId('approved_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete()
                ->comment('User who approved/rejected the payment');

            $table->timestamp('approved_at')
                ->nullable()
                ->comment('Timestamp when payment was approved/rejected');

            $table->text('rejection_reason')
                ->nullable()
                ->comment('Reason for payment rejection');

            // Payment metadata
            $table->date('payment_date')
                ->comment('Date when payment was made');

            $table->string('invoice_number', 50)
                ->nullable()
                ->comment('Generated invoice number for this payment');

            $table->timestamps();

            // Performance indexes
            $table->index(['school_id', 'status'], 'idx_school_status');
            $table->index('subscription_id', 'idx_subscription');
            $table->index('payment_date', 'idx_payment_date');
            $table->index('transaction_id', 'idx_transaction_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('subscription_payments');
    }
};
