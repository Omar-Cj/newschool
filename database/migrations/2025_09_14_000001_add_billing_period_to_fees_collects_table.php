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
        Schema::table('fees_collects', function (Blueprint $table) {
            // Add billing period fields for monthly fee tracking
            $table->string('billing_period', 7)->nullable()->comment('Format: YYYY-MM (e.g., 2024-10)');
            $table->integer('billing_year')->nullable()->comment('Billing year for efficient querying');
            $table->tinyInteger('billing_month')->nullable()->comment('Billing month (1-12) for efficient querying');

            // Add indexes for efficient querying
            $table->index(['student_id', 'fee_type_id', 'billing_period'], 'student_fee_billing_period_idx');
            $table->index(['billing_year', 'billing_month'], 'billing_year_month_idx');
            $table->index('billing_period', 'billing_period_idx');

            // Add compound index for preventing duplicates (will be enforced in application layer)
            $table->index(['student_id', 'fee_type_id', 'billing_period', 'academic_year_id'], 'unique_billing_period_idx');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('fees_collects', function (Blueprint $table) {
            // Drop indexes first
            $table->dropIndex('student_fee_billing_period_idx');
            $table->dropIndex('billing_year_month_idx');
            $table->dropIndex('billing_period_idx');
            $table->dropIndex('unique_billing_period_idx');

            // Drop billing period columns
            $table->dropColumn(['billing_period', 'billing_year', 'billing_month']);
        });
    }
};