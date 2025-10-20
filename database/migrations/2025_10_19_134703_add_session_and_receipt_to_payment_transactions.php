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
        Schema::table('payment_transactions', function (Blueprint $table) {
            // Add payment session ID to group related transactions (e.g., family payments)
            $table->string('payment_session_id', 50)->nullable()->after('transaction_reference');

            // Link to the consolidated receipt
            $table->foreignId('receipt_id')->nullable()->after('payment_session_id')->constrained('receipts')->nullOnDelete();

            // Add indexes for performance
            $table->index('payment_session_id');
            $table->index('receipt_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('payment_transactions', function (Blueprint $table) {
            $table->dropForeign(['receipt_id']);
            $table->dropIndex(['payment_session_id']);
            $table->dropIndex(['receipt_id']);

            $table->dropColumn([
                'payment_session_id',
                'receipt_id'
            ]);
        });
    }
};
