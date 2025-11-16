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
        Schema::table('students', function (Blueprint $table) {
            $table->foreignId('bus_id')
                  ->nullable()
                  ->after('residance_address')
                  ->constrained('buses')
                  ->nullOnDelete();

            $table->index('bus_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('students', function (Blueprint $table) {
            $table->dropForeign(['bus_id']);
            $table->dropIndex(['bus_id']);
            $table->dropColumn('bus_id');
        });
    }
};
