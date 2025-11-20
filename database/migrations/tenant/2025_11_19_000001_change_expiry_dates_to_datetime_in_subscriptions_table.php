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
     * Change expiry_date and grace_expiry_date from date to datetime
     * to support minute-level precision for testing subscriptions
     *
     * @return void
     */
    public function up()
    {
        Schema::table('subscriptions', function (Blueprint $table) {
            // Change expiry_date from date to datetime
            $table->datetime('expiry_date')->nullable()->change();

            // Change grace_expiry_date from date to datetime
            $table->datetime('grace_expiry_date')->nullable()->change();
        });

        // Update existing records to add time component (00:00:00) if they don't have it
        DB::statement("UPDATE subscriptions SET expiry_date = CONCAT(expiry_date, ' 00:00:00') WHERE expiry_date IS NOT NULL AND expiry_date NOT LIKE '%:%'");
        DB::statement("UPDATE subscriptions SET grace_expiry_date = CONCAT(grace_expiry_date, ' 00:00:00') WHERE grace_expiry_date IS NOT NULL AND grace_expiry_date NOT LIKE '%:%'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('subscriptions', function (Blueprint $table) {
            // Change back to date type
            $table->date('expiry_date')->nullable()->change();
            $table->date('grace_expiry_date')->nullable()->change();
        });
    }
};
