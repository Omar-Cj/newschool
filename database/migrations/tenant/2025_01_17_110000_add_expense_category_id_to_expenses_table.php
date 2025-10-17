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
            // Add expense_category_id as nullable initially for data migration
            $table->foreignId('expense_category_id')
                  ->nullable()
                  ->after('name')
                  ->constrained('expense_categories')
                  ->nullOnDelete();

            // Add index for better query performance
            $table->index('expense_category_id');
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
            // Drop foreign key and index
            $table->dropForeign(['expense_category_id']);
            $table->dropIndex(['expense_category_id']);

            // Drop column
            $table->dropColumn('expense_category_id');
        });
    }
};
