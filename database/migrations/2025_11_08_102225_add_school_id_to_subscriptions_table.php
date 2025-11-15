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
        Schema::table('subscriptions', function (Blueprint $table) {
            // Add school_id column after branch_id
            $table->unsignedBigInteger('school_id')->nullable()->after('branch_id');

            // Add foreign key constraint
            $table->foreign('school_id')
                  ->references('id')
                  ->on('schools')
                  ->onDelete('cascade');

            // Add index for better query performance
            $table->index('school_id');
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
            // Drop foreign key first
            $table->dropForeign(['school_id']);

            // Drop index
            $table->dropIndex(['school_id']);

            // Drop column
            $table->dropColumn('school_id');
        });
    }
};
