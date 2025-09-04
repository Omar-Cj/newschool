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
            $table->string('generation_batch_id')->nullable()->after('id');
            $table->enum('generation_method', ['manual', 'bulk', 'automated'])->default('manual')->after('generation_batch_id');
            $table->date('due_date')->nullable()->after('generation_method');
            $table->decimal('late_fee_applied', 8, 2)->default(0)->after('due_date');
            $table->decimal('discount_applied', 8, 2)->default(0)->after('late_fee_applied');
            
            $table->index('generation_batch_id');
            $table->index(['due_date', 'generation_method']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('fees_collects', function (Blueprint $table) {
            $table->dropIndex(['fees_collects_generation_batch_id_index']);
            $table->dropIndex(['fees_collects_due_date_generation_method_index']);
            $table->dropColumn([
                'generation_batch_id',
                'generation_method', 
                'due_date',
                'late_fee_applied',
                'discount_applied'
            ]);
        });
    }
};
