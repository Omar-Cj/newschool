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
        Schema::table('fees_collects', function (Blueprint $table) {
            $table->decimal('total_paid', 10, 2)->default(0)->after('amount');
            $table->enum('payment_status', ['unpaid', 'partial', 'paid'])->default('unpaid')->after('total_paid');
            $table->index(['payment_status', 'student_id']);
            $table->index(['total_paid', 'amount']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('fees_collects', function (Blueprint $table) {
            $table->dropIndex(['payment_status', 'student_id']);
            $table->dropIndex(['total_paid', 'amount']);
            $table->dropColumn(['total_paid', 'payment_status']);
        });
    }
};
