<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Add school_id column to payment_transactions table for proper multi-tenant isolation.
     * Backfills existing records from student relationship.
     */
    public function up(): void
    {
        Schema::table('payment_transactions', function (Blueprint $table) {
            // Add school_id column after id
            $table->foreignId('school_id')
                  ->after('id')
                  ->nullable() // Temporarily nullable for backfilling
                  ->constrained('schools')
                  ->cascadeOnDelete();
        });

        // Backfill existing payment_transactions with school_id from related student
        DB::statement('
            UPDATE payment_transactions pt
            INNER JOIN students s ON pt.student_id = s.id
            SET pt.school_id = s.school_id
            WHERE pt.school_id IS NULL
        ');

        // Make school_id non-nullable after backfilling
        Schema::table('payment_transactions', function (Blueprint $table) {
            $table->unsignedBigInteger('school_id')->nullable(false)->change();
        });

        // Add performance indexes
        Schema::table('payment_transactions', function (Blueprint $table) {
            // Index for school-scoped queries
            $table->index(['school_id', 'payment_date'], 'payment_transactions_school_date_idx');

            // Index for transaction number generation queries
            $table->index(['school_id', 'transaction_number'], 'payment_transactions_school_txn_idx');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('payment_transactions', function (Blueprint $table) {
            // Drop indexes first
            $table->dropIndex('payment_transactions_school_date_idx');
            $table->dropIndex('payment_transactions_school_txn_idx');

            // Drop foreign key constraint
            $table->dropForeign(['school_id']);

            // Drop column
            $table->dropColumn('school_id');
        });
    }
};
