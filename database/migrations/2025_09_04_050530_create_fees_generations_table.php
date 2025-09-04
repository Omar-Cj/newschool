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
        Schema::create('fees_generations', function (Blueprint $table) {
            $table->id();
            $table->string('batch_id')->unique();
            $table->enum('status', ['pending', 'processing', 'completed', 'failed', 'cancelled'])->default('pending');
            $table->integer('total_students')->default(0);
            $table->integer('processed_students')->default(0);
            $table->integer('successful_students')->default(0);
            $table->integer('failed_students')->default(0);
            $table->decimal('total_amount', 10, 2)->default(0);
            $table->json('filters')->nullable(); // Store filter criteria used
            $table->text('notes')->nullable();
            $table->timestamp('started_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->foreignId('created_by')->constrained('users')->cascadeOnDelete();
            $table->foreignId('school_id')->nullable()->constrained()->cascadeOnDelete();
            $table->timestamps();

            $table->index(['status', 'created_at']);
            $table->index(['school_id', 'status']);
            $table->index('batch_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('fees_generations');
    }
};
