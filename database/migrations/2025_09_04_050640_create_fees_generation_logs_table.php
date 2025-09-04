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
        Schema::create('fees_generation_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('fees_generation_id')->constrained()->cascadeOnDelete();
            $table->foreignId('student_id')->constrained()->cascadeOnDelete();
            $table->foreignId('fees_collect_id')->nullable()->constrained()->nullOnDelete();
            $table->enum('status', ['pending', 'success', 'failed', 'skipped'])->default('pending');
            $table->decimal('amount', 10, 2)->default(0);
            $table->text('error_message')->nullable();
            $table->json('fee_details')->nullable(); // Store individual fee breakdown
            $table->timestamps();

            $table->index(['fees_generation_id', 'status']);
            $table->index(['student_id', 'status']);
            $table->unique(['fees_generation_id', 'student_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('fees_generation_logs');
    }
};
