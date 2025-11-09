<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * CRITICAL FIX: Add school_id to tables that break multi-tenant isolation
     *
     * Priority Tables:
     * 1. branches - CRITICAL: Used by uploads and other school-specific data
     * 2. exam_entries, exam_entry_results - Academic data isolation
     * 3. subject_assigns, subject_assign_childrens - Curriculum isolation
     * 4. uploads - File/media isolation (currently has branch_id only)
     * 5. Translation tables - Per-school content customization
     *
     * @return void
     */
    public function up(): void
    {
        // ==================== CRITICAL PRIORITY ====================

        // 1. BRANCHES - Links schools to their organizational structure
        if (Schema::hasTable('branches') && !Schema::hasColumn('branches', 'school_id')) {
            Schema::table('branches', function (Blueprint $table) {
                $table->unsignedBigInteger('school_id')->nullable()->after('id');
                $table->foreign('school_id')->references('id')->on('schools')->onDelete('cascade');
                $table->index('school_id');
            });

            // Populate school_id for existing branches from users table
            DB::statement("
                UPDATE branches b
                INNER JOIN (
                    SELECT branch_id, school_id
                    FROM users
                    WHERE branch_id IS NOT NULL AND school_id IS NOT NULL
                    GROUP BY branch_id, school_id
                ) u ON b.id = u.branch_id
                SET b.school_id = u.school_id
            ");

            // Make school_id NOT NULL after population
            Schema::table('branches', function (Blueprint $table) {
                $table->unsignedBigInteger('school_id')->nullable(false)->change();
            });
        }

        // 2. UPLOADS - File/media must be school-isolated
        if (Schema::hasTable('uploads') && !Schema::hasColumn('uploads', 'school_id')) {
            Schema::table('uploads', function (Blueprint $table) {
                $table->unsignedBigInteger('school_id')->nullable()->after('id');
                $table->foreign('school_id')->references('id')->on('schools')->onDelete('cascade');
                $table->index('school_id');
            });

            // Populate school_id from branches
            DB::statement("
                UPDATE uploads u
                INNER JOIN branches b ON u.branch_id = b.id
                SET u.school_id = b.school_id
                WHERE b.school_id IS NOT NULL
            ");

            // For uploads without branch_id, assign to default school
            DB::statement("
                UPDATE uploads
                SET school_id = 1
                WHERE school_id IS NULL AND branch_id IS NULL
            ");
        }

        // ==================== HIGH PRIORITY ====================

        // 3. EXAM_ENTRIES - Academic assessment data
        if (Schema::hasTable('exam_entries') && !Schema::hasColumn('exam_entries', 'school_id')) {
            Schema::table('exam_entries', function (Blueprint $table) {
                $table->unsignedBigInteger('school_id')->nullable()->after('id');
                $table->foreign('school_id')->references('id')->on('schools')->onDelete('cascade');
                $table->index('school_id');
            });

            // Populate from classes table via class_id
            DB::statement("
                UPDATE exam_entries e
                INNER JOIN classes c ON e.class_id = c.id
                SET e.school_id = c.school_id
                WHERE c.school_id IS NOT NULL
            ");
        }

        // 4. EXAM_ENTRY_RESULTS - Academic results
        if (Schema::hasTable('exam_entry_results') && !Schema::hasColumn('exam_entry_results', 'school_id')) {
            Schema::table('exam_entry_results', function (Blueprint $table) {
                $table->unsignedBigInteger('school_id')->nullable()->after('id');
                $table->foreign('school_id')->references('id')->on('schools')->onDelete('cascade');
                $table->index('school_id');
            });

            // Populate from exam_entries
            DB::statement("
                UPDATE exam_entry_results r
                INNER JOIN exam_entries e ON r.exam_entry_id = e.id
                SET r.school_id = e.school_id
                WHERE e.school_id IS NOT NULL
            ");
        }

        // 5. SUBJECT_ASSIGNS - Subject assignment to classes
        if (Schema::hasTable('subject_assigns') && !Schema::hasColumn('subject_assigns', 'school_id')) {
            Schema::table('subject_assigns', function (Blueprint $table) {
                $table->unsignedBigInteger('school_id')->nullable()->after('id');
                $table->foreign('school_id')->references('id')->on('schools')->onDelete('cascade');
                $table->index('school_id');
            });

            // Populate from classes table
            DB::statement("
                UPDATE subject_assigns sa
                INNER JOIN classes c ON sa.classes_id = c.id
                SET sa.school_id = c.school_id
                WHERE c.school_id IS NOT NULL
            ");
        }

        // 6. SUBJECT_ASSIGN_CHILDRENS - Subject details
        if (Schema::hasTable('subject_assign_childrens') && !Schema::hasColumn('subject_assign_childrens', 'school_id')) {
            Schema::table('subject_assign_childrens', function (Blueprint $table) {
                $table->unsignedBigInteger('school_id')->nullable()->after('id');
                $table->foreign('school_id')->references('id')->on('schools')->onDelete('cascade');
                $table->index('school_id');
            });

            // Populate from subject_assigns
            DB::statement("
                UPDATE subject_assign_childrens sac
                INNER JOIN subject_assigns sa ON sac.subject_assign_id = sa.id
                SET sac.school_id = sa.school_id
                WHERE sa.school_id IS NOT NULL
            ");
        }

        // ==================== TRANSLATION TABLES ====================
        // These allow per-school content customization

        $translationTables = [
            'about_translates' => 'about_id',
            'class_translates' => 'class_id',
            'section_translates' => 'section_id',
            'setting_translates' => 'setting_id',
            'shift_translates' => 'shift_id',
            'slider_translates' => 'slider_id',
            'contact_info_translates' => 'contact_info_id',
            'counter_translates' => 'counter_id',
            'department_contact_translates' => 'department_contact_id',
            'event_translates' => 'event_id',
            'gallery_category_translates' => 'gallery_category_id',
            'news_translates' => 'news_id',
            'notice_board_translates' => 'notice_board_id',
            'page_translates' => 'page_id',
            'session_translates' => 'session_id',
            'gender_translates' => 'gender_id',
            'religon_translates' => 'religon_id',
        ];

        foreach ($translationTables as $table => $foreignKey) {
            if (Schema::hasTable($table) && !Schema::hasColumn($table, 'school_id')) {
                Schema::table($table, function (Blueprint $table) {
                    $table->unsignedBigInteger('school_id')->nullable();
                    $table->foreign('school_id')->references('id')->on('schools')->onDelete('cascade');
                    $table->index('school_id');
                });

                // Extract parent table name from foreign key
                $parentTable = str_replace(['_id', 'religon'], ['', 'religion'], $foreignKey);
                if ($parentTable === 'about') $parentTable = 'abouts';
                if ($parentTable === 'class') $parentTable = 'classes';
                if ($parentTable === 'section') $parentTable = 'sections';
                if ($parentTable === 'setting') $parentTable = 'settings';
                if ($parentTable === 'shift') $parentTable = 'shifts';
                if ($parentTable === 'slider') $parentTable = 'sliders';
                if ($parentTable === 'contact_info') $parentTable = 'contact_infos';
                if ($parentTable === 'counter') $parentTable = 'counters';
                if ($parentTable === 'department_contact') $parentTable = 'department_contacts';
                if ($parentTable === 'event') $parentTable = 'events';
                if ($parentTable === 'gallery_category') $parentTable = 'gallery_categories';
                if ($parentTable === 'news') $parentTable = 'news';
                if ($parentTable === 'notice_board') $parentTable = 'notice_boards';
                if ($parentTable === 'page') $parentTable = 'pages';
                if ($parentTable === 'session') $parentTable = 'sessions';
                if ($parentTable === 'gender') $parentTable = 'genders';
                if ($parentTable === 'religion') $parentTable = 'religions';

                // Populate school_id from parent table if it has school_id
                if (Schema::hasTable($parentTable) && Schema::hasColumn($parentTable, 'school_id')) {
                    DB::statement("
                        UPDATE {$table} t
                        INNER JOIN {$parentTable} p ON t.{$foreignKey} = p.id
                        SET t.school_id = p.school_id
                        WHERE p.school_id IS NOT NULL
                    ");
                }
            }
        }

        // ==================== CONTENT & OPERATIONAL TABLES ====================

        $contentTables = [
            'contact_infos',
            'contacts',
            'frequently_asked_questions',
            'testimonials',
            'memories',
            'memory_galleries',
            'messages',
            'subscribes',
            'page_sections',
        ];

        foreach ($contentTables as $table) {
            if (Schema::hasTable($table) && !Schema::hasColumn($table, 'school_id')) {
                Schema::table($table, function (Blueprint $table) {
                    $table->unsignedBigInteger('school_id')->nullable();
                    $table->foreign('school_id')->references('id')->on('schools')->onDelete('cascade');
                    $table->index('school_id');
                });

                // Assign to default school for now
                DB::table($table)->whereNull('school_id')->update(['school_id' => 1]);
            }
        }

        // ==================== ADMISSIONS ====================

        $admissionTables = [
            'online_admissions',
            'online_admission_fees_assigns',
            'online_admission_payments',
        ];

        foreach ($admissionTables as $table) {
            if (Schema::hasTable($table) && !Schema::hasColumn($table, 'school_id')) {
                Schema::table($table, function (Blueprint $table) {
                    $table->unsignedBigInteger('school_id')->nullable();
                    $table->foreign('school_id')->references('id')->on('schools')->onDelete('cascade');
                    $table->index('school_id');
                });
            }
        }

        // ==================== ACADEMIC OPERATIONS ====================

        if (Schema::hasTable('promote_students') && !Schema::hasColumn('promote_students', 'school_id')) {
            Schema::table('promote_students', function (Blueprint $table) {
                $table->unsignedBigInteger('school_id')->nullable();
                $table->foreign('school_id')->references('id')->on('schools')->onDelete('cascade');
                $table->index('school_id');
            });
        }

        if (Schema::hasTable('mark_sheet_approvals') && !Schema::hasColumn('mark_sheet_approvals', 'school_id')) {
            Schema::table('mark_sheet_approvals', function (Blueprint $table) {
                $table->unsignedBigInteger('school_id')->nullable();
                $table->foreign('school_id')->references('id')->on('schools')->onDelete('cascade');
                $table->index('school_id');
            });
        }

        // ==================== REPORTING ====================
        // SKIPPED: report_category, report_center, report_parameters
        // These are system-level tables (shared across all schools)
        // They should NOT have school_id column
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        // Drop in reverse order to handle foreign key constraints

        $allTables = [
            'branches', 'uploads', 'exam_entries', 'exam_entry_results',
            'subject_assigns', 'subject_assign_childrens',
            'about_translates', 'class_translates', 'section_translates',
            'setting_translates', 'shift_translates', 'slider_translates',
            'contact_info_translates', 'counter_translates', 'department_contact_translates',
            'event_translates', 'gallery_category_translates', 'news_translates',
            'notice_board_translates', 'page_translates', 'session_translates',
            'gender_translates', 'religon_translates',
            'contact_infos', 'contacts', 'frequently_asked_questions',
            'testimonials', 'memories', 'memory_galleries', 'messages',
            'subscribes', 'page_sections',
            'online_admissions', 'online_admission_fees_assigns', 'online_admission_payments',
            'promote_students', 'mark_sheet_approvals',
            // Removed: report_category, report_center, report_parameters (system-level tables)
        ];

        foreach ($allTables as $table) {
            if (Schema::hasTable($table) && Schema::hasColumn($table, 'school_id')) {
                Schema::table($table, function (Blueprint $table) {
                    $table->dropForeign(['school_id']);
                    $table->dropColumn('school_id');
                });
            }
        }
    }
};
