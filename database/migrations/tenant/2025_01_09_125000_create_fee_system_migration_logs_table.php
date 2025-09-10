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
        Schema::create('fee_system_migration_logs', function (Blueprint $table) {
            $table->id();
            $table->string('migration_name')->comment('Name of the migration executed');
            $table->enum('status', ['pending', 'running', 'completed', 'failed', 'rollback'])->default('pending');
            $table->json('migration_details')->nullable()->comment('Detailed information about the migration');
            $table->integer('fee_types_migrated')->default(0)->comment('Number of fee types processed');
            $table->integer('student_services_created')->default(0)->comment('Number of student services created');
            $table->integer('fees_collects_updated')->default(0)->comment('Number of fee collect records updated');
            $table->integer('discounts_migrated')->default(0)->comment('Number of discount records migrated');
            $table->text('errors')->nullable()->comment('Any errors encountered during migration');
            $table->text('notes')->nullable()->comment('Additional migration notes');
            $table->timestamp('migration_date')->nullable()->comment('When the migration was executed');
            $table->timestamps();
            
            // Indexes
            $table->index('migration_name');
            $table->index(['status', 'migration_date']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('fee_system_migration_logs');
    }
};