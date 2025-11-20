<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Adds performance indexes to subscriptions table for optimizing
     * access control checks and subscription queries.
     *
     * These indexes improve query performance for:
     * - Access control checks filtering by school_id, status, and expiry dates
     * - Subscription renewal queries
     * - Grace period expiry checks
     *
     * @return void
     */
    public function up()
    {
        Schema::table('subscriptions', function (Blueprint $table) {
            // Composite index for access control queries
            // Optimizes: WHERE school_id = ? AND status = ? AND expiry_date > ?
            $table->index(
                ['school_id', 'status', 'expiry_date'],
                'idx_school_status_expiry'
            );

            // Composite index for grace period checks
            // Optimizes: WHERE expiry_date <= ? AND grace_expiry_date >= ?
            $table->index(
                ['expiry_date', 'grace_expiry_date'],
                'idx_expiry_grace_period'
            );

            // Individual index on grace_expiry_date for cleanup jobs
            // Optimizes: WHERE grace_expiry_date < ?
            $table->index('grace_expiry_date', 'idx_grace_expiry');

            // Index for payment status filtering
            // Optimizes: WHERE payment_status = ?
            $table->index('payment_status', 'idx_payment_status');
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
            $table->dropIndex('idx_school_status_expiry');
            $table->dropIndex('idx_expiry_grace_period');
            $table->dropIndex('idx_grace_expiry');
            $table->dropIndex('idx_payment_status');
        });
    }
};
