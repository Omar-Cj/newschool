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
        Schema::create('parent_deposits', function (Blueprint $table) {
            $table->id();
            $table->foreignId('parent_guardian_id')->constrained('parent_guardians')->cascadeOnDelete();
            $table->foreignId('student_id')->nullable()->constrained('students')->cascadeOnDelete();
            $table->decimal('amount', 10, 2);
            $table->datetime('deposit_date');
            $table->tinyInteger('payment_method')->comment('1=Cash, 3=Zaad, 4=Edahab');
            $table->string('transaction_reference')->nullable();
            $table->text('deposit_reason')->nullable();
            $table->enum('status', ['pending', 'completed', 'failed'])->default('completed');
            $table->foreignId('collected_by')->constrained('users');
            $table->foreignId('branch_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('academic_year_id')->constrained('sessions');
            $table->foreignId('journal_id')->nullable()->constrained()->nullOnDelete();
            $table->string('deposit_number')->unique();
            $table->timestamps();

            // Indexes for performance
            $table->index(['parent_guardian_id', 'deposit_date']);
            $table->index(['student_id', 'status']);
            $table->index(['academic_year_id', 'branch_id']);
            $table->index('deposit_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('parent_deposits');
    }
};