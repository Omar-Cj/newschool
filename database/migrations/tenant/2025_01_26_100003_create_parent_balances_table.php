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
        Schema::create('parent_balances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('parent_guardian_id')->constrained('parent_guardians')->cascadeOnDelete();
            $table->foreignId('student_id')->nullable()->constrained('students')->cascadeOnDelete();
            $table->decimal('available_balance', 10, 2)->default(0);
            $table->decimal('reserved_balance', 10, 2)->default(0);
            $table->decimal('total_deposits', 10, 2)->default(0);
            $table->decimal('total_withdrawals', 10, 2)->default(0);
            $table->datetime('last_transaction_date')->nullable();
            $table->foreignId('academic_year_id')->constrained('sessions');
            $table->foreignId('branch_id')->nullable()->constrained()->nullOnDelete();
            $table->timestamps();

            // Unique constraint to prevent duplicate balance records
            $table->unique(['parent_guardian_id', 'student_id', 'academic_year_id'], 'unique_parent_student_balance');

            // Indexes for performance
            $table->index(['parent_guardian_id', 'academic_year_id']);
            $table->index(['student_id', 'available_balance']);
            $table->index('last_transaction_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('parent_balances');
    }
};