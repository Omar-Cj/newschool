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
            $table->foreignId('journal_id')->nullable()->after('student_id')->constrained('journals')->nullOnDelete();
            $table->decimal('discount_amount', 16, 2)->nullable()->after('fine_amount');
            $table->enum('discount_type', ['fixed', 'percentage'])->nullable()->after('discount_amount');
            $table->string('transaction_reference')->nullable()->after('transaction_id');
            $table->text('payment_notes')->nullable()->after('transaction_reference');

            // Add index for journal lookups
            $table->index('journal_id');
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
            $table->dropForeign(['journal_id']);
            $table->dropIndex(['journal_id']);
            $table->dropColumn([
                'journal_id',
                'discount_amount',
                'discount_type',
                'transaction_reference',
                'payment_notes'
            ]);
        });
    }
};