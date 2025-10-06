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
        Schema::create('exam_entries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('session_id')->constrained('sessions')->cascadeOnDelete();
            $table->foreignId('term_id')->constrained('terms')->cascadeOnDelete();
            $table->string('grade')->nullable();
            $table->foreignId('class_id')->constrained('classes')->cascadeOnDelete();
            $table->foreignId('section_id')->constrained('sections')->cascadeOnDelete();
            $table->foreignId('branch_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('exam_type_id')->constrained('exam_types')->cascadeOnDelete();
            $table->foreignId('subject_id')->nullable()->constrained('subjects')->cascadeOnDelete();
            $table->boolean('is_all_subjects')->default(false);
            $table->enum('entry_method', ['manual', 'excel'])->default('manual');
            $table->string('upload_file_path')->nullable();
            $table->float('total_marks')->default(100);
            $table->enum('status', ['draft', 'completed', 'published'])->default('draft');
            $table->foreignId('created_by')->constrained('users')->cascadeOnDelete();
            $table->timestamp('published_at')->nullable();
            $table->timestamps();

            // Indexes for better query performance
            $table->index('branch_id');
            $table->index('grade');
            $table->index(['session_id', 'term_id']);
            $table->index(['class_id', 'section_id']);
            $table->index('status');

            // Unique constraint to prevent duplicate exam entries per branch
            // Each branch can have separate exam entry for same parameters
            $table->unique([
                'session_id',
                'term_id',
                'class_id',
                'section_id',
                'exam_type_id',
                'subject_id',
                'branch_id'
            ], 'unique_exam_entry_per_branch');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('exam_entries');
    }
};
