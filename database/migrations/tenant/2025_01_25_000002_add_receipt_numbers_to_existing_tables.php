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
        // Add receipt_number to payment_transactions if not exists
        if (Schema::hasTable('payment_transactions') && !Schema::hasColumn('payment_transactions', 'receipt_number')) {
            Schema::table('payment_transactions', function (Blueprint $table) {
                $table->string('receipt_number', 20)->nullable()->unique()->after('transaction_reference')
                    ->comment('Unified receipt number format: RCT-YYYY-NNNNNN');

                // Index for efficient lookups
                $table->index('receipt_number', 'idx_payment_transactions_receipt_number');
            });
        }

        // Add receipt_number to fees_collects if not exists
        if (Schema::hasTable('fees_collects') && !Schema::hasColumn('fees_collects', 'receipt_number')) {
            Schema::table('fees_collects', function (Blueprint $table) {
                $table->string('receipt_number', 20)->nullable()->unique()->after('transaction_reference')
                    ->comment('Unified receipt number format: RCT-YYYY-NNNNNN');

                // Index for efficient lookups
                $table->index('receipt_number', 'idx_fees_collects_receipt_number');
            });
        }

        // Create indexes for receipt number searches across both tables
        if (Schema::hasTable('payment_transactions') && Schema::hasColumn('payment_transactions', 'receipt_number')) {
            Schema::table('payment_transactions', function (Blueprint $table) {
                // Composite index for receipt searches with dates
                if (Schema::hasColumn('payment_transactions', 'payment_date')) {
                    $table->index(['receipt_number', 'payment_date'], 'idx_pt_receipt_date');
                }

                // Index for student receipt searches
                if (Schema::hasColumn('payment_transactions', 'student_id')) {
                    $table->index(['student_id', 'receipt_number'], 'idx_pt_student_receipt');
                }
            });
        }

        if (Schema::hasTable('fees_collects') && Schema::hasColumn('fees_collects', 'receipt_number')) {
            Schema::table('fees_collects', function (Blueprint $table) {
                // Composite index for receipt searches with dates
                if (Schema::hasColumn('fees_collects', 'date')) {
                    $table->index(['receipt_number', 'date'], 'idx_fc_receipt_date');
                }

                // Index for student receipt searches
                if (Schema::hasColumn('fees_collects', 'student_id')) {
                    $table->index(['student_id', 'receipt_number'], 'idx_fc_student_receipt');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Remove receipt_number column from payment_transactions
        if (Schema::hasTable('payment_transactions') && Schema::hasColumn('payment_transactions', 'receipt_number')) {
            Schema::table('payment_transactions', function (Blueprint $table) {
                $table->dropIndex('idx_payment_transactions_receipt_number');

                if (Schema::hasColumn('payment_transactions', 'payment_date')) {
                    $table->dropIndex('idx_pt_receipt_date');
                }

                if (Schema::hasColumn('payment_transactions', 'student_id')) {
                    $table->dropIndex('idx_pt_student_receipt');
                }

                $table->dropColumn('receipt_number');
            });
        }

        // Remove receipt_number column from fees_collects
        if (Schema::hasTable('fees_collects') && Schema::hasColumn('fees_collects', 'receipt_number')) {
            Schema::table('fees_collects', function (Blueprint $table) {
                $table->dropIndex('idx_fees_collects_receipt_number');

                if (Schema::hasColumn('fees_collects', 'date')) {
                    $table->dropIndex('idx_fc_receipt_date');
                }

                if (Schema::hasColumn('fees_collects', 'student_id')) {
                    $table->dropIndex('idx_fc_student_receipt');
                }

                $table->dropColumn('receipt_number');
            });
        }
    }
};