<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Fix transaction_number unique constraint to be school-scoped instead of globally unique.
     * This allows each school to have independent transaction number sequences.
     */
    public function up(): void
    {
        Schema::table('payment_transactions', function (Blueprint $table) {
            // Drop the existing global unique constraint on transaction_number
            $table->dropUnique(['transaction_number']);

            // Add composite unique constraint for (transaction_number, school_id)
            // This allows each school to have independent transaction number sequences
            // e.g., School 1 can have PAY-2025-000001, School 2 can also have PAY-2025-000001
            $table->unique(
                ['transaction_number', 'school_id'],
                'payment_transactions_transaction_school_unique'
            );
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('payment_transactions', function (Blueprint $table) {
            // Drop the school-scoped unique constraint
            $table->dropUnique('payment_transactions_transaction_school_unique');

            // Restore the global unique constraint on transaction_number
            // WARNING: This may fail if multiple schools have same transaction numbers
            $table->unique('transaction_number');
        });
    }
};
