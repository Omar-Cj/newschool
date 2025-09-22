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
        Schema::create('payment_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('fees_collect_id')->constrained('fees_collects')->cascadeOnDelete();
            $table->foreignId('student_id')->constrained('students')->cascadeOnDelete();
            $table->string('transaction_number', 50)->unique();
            $table->date('payment_date');
            $table->decimal('amount', 10, 2);
            $table->tinyInteger('payment_method')->comment('1=cash, 2=stripe, 3=zaad, 4=edahab, 5=paypal');
            $table->string('payment_gateway', 50)->nullable();
            $table->string('transaction_reference', 100)->nullable();
            $table->text('payment_notes')->nullable();
            $table->foreignId('journal_id')->nullable()->constrained('journals')->nullOnDelete();
            $table->foreignId('collected_by')->constrained('users')->cascadeOnDelete();
            $table->foreignId('branch_id')->nullable()->constrained('branches')->nullOnDelete();
            $table->timestamps();

            // Indexes for performance
            $table->index(['fees_collect_id', 'payment_date']);
            $table->index(['student_id', 'payment_date']);
            $table->index('transaction_number');
            $table->index('payment_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payment_transactions');
    }
};
