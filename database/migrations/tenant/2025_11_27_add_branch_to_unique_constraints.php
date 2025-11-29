<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Extend unique constraints to include branch_id for multi-branch support.
     * This allows each branch within a school to have independent batch_id and
     * transaction_number sequences.
     */
    public function up(): void
    {
        // 1. Fix fees_generations table
        Schema::table('fees_generations', function (Blueprint $table) {
            // Drop the existing school-scoped unique constraint
            $table->dropUnique('fees_generations_batch_school_unique');

            // Add composite unique constraint including branch_id
            $table->unique(
                ['batch_id', 'school_id', 'branch_id'],
                'fees_generations_batch_school_branch_unique'
            );
        });

        // 2. Fix payment_transactions table
        Schema::table('payment_transactions', function (Blueprint $table) {
            // Drop the existing school-scoped unique constraint
            $table->dropUnique('payment_transactions_transaction_school_unique');

            // Add composite unique constraint including branch_id
            $table->unique(
                ['transaction_number', 'school_id', 'branch_id'],
                'payment_transactions_transaction_school_branch_unique'
            );
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('fees_generations', function (Blueprint $table) {
            $table->dropUnique('fees_generations_batch_school_branch_unique');
            $table->unique(['batch_id', 'school_id'], 'fees_generations_batch_school_unique');
        });

        Schema::table('payment_transactions', function (Blueprint $table) {
            $table->dropUnique('payment_transactions_transaction_school_branch_unique');
            $table->unique(['transaction_number', 'school_id'], 'payment_transactions_transaction_school_unique');
        });
    }
};
