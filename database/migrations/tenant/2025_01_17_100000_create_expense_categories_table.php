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
        Schema::create('expense_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('code')->nullable();
            $table->text('description')->nullable();
            $table->tinyInteger('status')->default(App\Enums\Status::ACTIVE);
            $table->unsignedBigInteger('branch_id')->default(1);
            $table->timestamps();

            // Indexes for performance
            $table->index('branch_id');
            $table->index(['status', 'branch_id']);

            // Unique constraint: name must be unique per branch
            $table->unique(['name', 'branch_id'], 'expense_categories_name_branch_unique');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('expense_categories');
    }
};
