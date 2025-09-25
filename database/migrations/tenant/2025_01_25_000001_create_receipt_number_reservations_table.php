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
        Schema::create('receipt_number_reservations', function (Blueprint $table) {
            $table->id();
            $table->string('receipt_number')->unique()->comment('Reserved receipt number in format RCT-YYYY-NNNNNN');
            $table->unsignedBigInteger('reserved_by')->nullable()->comment('User ID who reserved the number');
            $table->timestamp('reserved_at')->comment('When the number was reserved');
            $table->timestamp('expires_at')->comment('When the reservation expires');
            $table->timestamps();

            // Indexes for performance
            $table->index('expires_at', 'idx_receipt_reservations_expires_at');
            $table->index(['receipt_number', 'expires_at'], 'idx_receipt_reservations_number_expires');
            $table->index('reserved_by', 'idx_receipt_reservations_user');

            // Foreign key constraint if users table exists
            if (Schema::hasTable('users')) {
                $table->foreign('reserved_by')->references('id')->on('users')->onDelete('set null');
            }
        });

        // Add comment to the table
        DB::statement("ALTER TABLE `receipt_number_reservations` COMMENT = 'Temporary reservations for receipt numbers to prevent gaps and duplicates during concurrent operations'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('receipt_number_reservations');
    }
};