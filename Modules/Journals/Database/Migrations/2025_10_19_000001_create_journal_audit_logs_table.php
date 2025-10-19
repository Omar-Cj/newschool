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
        Schema::create('journal_audit_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('journal_id')->constrained('journals')->cascadeOnDelete();
            $table->enum('action', ['opened', 'closed']);
            $table->foreignId('performed_by')->constrained('users')->cascadeOnDelete();
            $table->timestamp('performed_at');
            $table->text('notes')->nullable();
            $table->timestamps();

            // Indexes for faster queries
            $table->index(['journal_id', 'performed_at']);
            $table->index('performed_by');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('journal_audit_logs');
    }
};
