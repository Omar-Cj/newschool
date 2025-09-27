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
        Schema::create('parent_deposit_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('parent_deposit_id')->nullable()->constrained('parent_deposits')->cascadeOnDelete();
            $table->foreignId('parent_guardian_id')->constrained('parent_guardians')->cascadeOnDelete();
            $table->foreignId('student_id')->nullable()->constrained('students')->cascadeOnDelete();
            $table->enum('transaction_type', ['deposit', 'withdrawal', 'allocation', 'refund']);
            $table->decimal('amount', 10, 2);
            $table->decimal('balance_before', 10, 2)->default(0);
            $table->decimal('balance_after', 10, 2)->default(0);
            $table->datetime('transaction_date');
            $table->text('description');
            $table->foreignId('fees_collect_id')->nullable()->constrained('fees_collects')->nullOnDelete();
            $table->string('reference_number')->unique();
            $table->foreignId('created_by')->constrained('users');
            $table->foreignId('branch_id')->nullable()->constrained()->nullOnDelete();
            $table->timestamps();

            // Indexes for performance
            $table->index(['parent_guardian_id', 'transaction_date']);
            $table->index(['student_id', 'transaction_type']);
            $table->index(['transaction_type', 'transaction_date']);
            $table->index('fees_collect_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('parent_deposit_transactions');
    }
};