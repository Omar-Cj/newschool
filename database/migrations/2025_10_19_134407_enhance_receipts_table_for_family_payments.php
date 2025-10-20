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
        Schema::table('receipts', function (Blueprint $table) {
            // Add denormalized fields for quick access without joins
            $table->string('student_name')->nullable()->after('student_id');
            $table->string('class', 100)->nullable()->after('student_name');
            $table->string('section', 50)->nullable()->after('class');
            $table->string('guardian_name')->nullable()->after('section');

            // Add discount tracking (aggregated from fees_collects)
            $table->decimal('discount_amount', 10, 2)->default(0)->after('total_amount');

            // Add payment session ID for grouping family payments
            $table->string('payment_session_id', 50)->nullable()->after('transaction_reference');

            // Add indexes for performance
            $table->index('payment_session_id');
            $table->index(['student_name', 'payment_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('receipts', function (Blueprint $table) {
            $table->dropIndex(['payment_session_id']);
            $table->dropIndex(['student_name', 'payment_date']);

            $table->dropColumn([
                'student_name',
                'class',
                'section',
                'guardian_name',
                'discount_amount',
                'payment_session_id'
            ]);
        });
    }
};
