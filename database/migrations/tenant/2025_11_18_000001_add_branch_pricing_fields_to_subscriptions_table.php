<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Adds branch-based pricing and grace period fields to subscriptions table
     * to support multi-branch subscription billing.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('subscriptions', function (Blueprint $table) {
            // Branch count and pricing fields
            $table->integer('branch_count')
                ->default(1)
                ->after('payment_status')
                ->comment('Number of branches under this subscription');

            $table->decimal('total_price', 16, 2)
                ->nullable()
                ->after('branch_count')
                ->comment('Total price calculated as package_price × branch_count');

            // Grace period fields for subscription expiry
            $table->integer('grace_period_days')
                ->default(2)
                ->after('expiry_date')
                ->comment('Number of days grace period after expiry_date');

            $table->date('grace_expiry_date')
                ->nullable()
                ->after('grace_period_days')
                ->comment('Calculated as expiry_date + grace_period_days');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('subscriptions', function (Blueprint $table) {
            $table->dropColumn([
                'branch_count',
                'total_price',
                'grace_period_days',
                'grace_expiry_date'
            ]);
        });
    }
};
