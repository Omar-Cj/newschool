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
            // Add package_id column after school_id
            $table->unsignedBigInteger('package_id')->nullable()->after('school_id');

            // Add foreign key constraint to packages table
            $table->foreign('package_id')
                  ->references('id')
                  ->on('packages')
                  ->onDelete('cascade');

            // Add index for better query performance
            $table->index('package_id');
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
            $table->dropForeign(['package_id']);

            // Drop index
            $table->dropIndex(['package_id']);

            // Drop column
            $table->dropColumn('package_id');
        });
    }
};
