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
        Schema::table('expenses', function (Blueprint $table) {
            $table->foreignId('journal_id')
                  ->nullable()
                  ->after('expense_category_id')
                  ->constrained('journals')
                  ->nullOnDelete();

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
        Schema::table('expenses', function (Blueprint $table) {
            $table->dropForeign(['journal_id']);
            $table->dropIndex(['journal_id']);
            $table->dropColumn('journal_id');
        });
    }
};
