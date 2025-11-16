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
        Schema::create('buses', function (Blueprint $table) {
            $table->id();
            $table->string('area_name');
            $table->string('bus_number', 100)->nullable();
            $table->integer('capacity')->nullable();
            $table->string('driver_name')->nullable();
            $table->string('driver_phone', 50)->nullable();
            $table->string('license_plate', 100)->nullable();
            $table->tinyInteger('status')->default(App\Enums\Status::ACTIVE);
            $table->unsignedBigInteger('branch_id')->default(1);
            $table->timestamps();

            // Indexes
            $table->index('branch_id');
            $table->index(['status', 'branch_id']);

            // Unique constraint
            $table->unique(['area_name', 'branch_id'], 'buses_area_name_branch_unique');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('buses');
    }
};
