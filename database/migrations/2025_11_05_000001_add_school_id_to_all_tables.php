<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * List of all tables that require school_id column
     */
    private const TABLES_WITH_SCHOOL_ID = [
        'classes',
        'sections',
        'subjects',
        'class_setups',
        'class_setup_childrens',
        'class_routines',
        'class_routine_childrens',
        'session_class_students',
        'sessions',
        'academic_level_configs',
        'time_schedules',
        'shifts',
        'students',
        'student_categories',
        'student_services',
        'student_absent_notifications',
        'parent_guardians',
        'parent_deposits',
        'parent_deposit_transactions',
        'parent_balances',
        'staff',
        'departments',
        'designations',
        'leave_types',
        'leave_requests',
        'fees_types',
        'fees_groups',
        'fees_masters',
        'fees_master_childrens',
        'fees_assigns',
        'fees_assign_childrens',
        'fees_collects',
        'fees_generations',
        'fees_generation_logs',
        'receipts',
        'payment_transactions',
        'payment_transaction_allocations',
        'receipt_number_reservations',
        'assign_fees_discounts',
        'sibling_fees_discounts',
        'early_payment_discounts',
        'exam_types',
        'exam_assigns',
        'exam_assign_childrens',
        'exam_routines',
        'exam_routine_childrens',
        'marks_grades',
        'marks_registers',
        'marks_register_childrens',
        'examination_results',
        'examination_settings',
        'online_exams',
        'online_exam_children_questions',
        'online_exam_children_students',
        'question_banks',
        'question_bank_childrens',
        'question_groups',
        'books',
        'book_categories',
        'members',
        'member_categories',
        'issue_books',
        'attendances',
        'subject_attendances',
        'homework',
        'homework_students',
        'gmeets',
        'certificates',
        'id_cards',
        'notice_boards',
        'events',
        'news',
        'sliders',
        'galleries',
        'gallery_categories',
        'counters',
        'abouts',
        'pages',
        'searches',
        'account_heads',
        'incomes',
        'expenses',
        'expense_categories',
        'cash_transfers',
        'terms',
        'term_definitions',
        'sms_mail_logs',
        'sms_mail_templates',
        'system_notifications',
        'settings',
        'notification_settings',
        'online_admission_settings',
        'forum_posts',
        'forum_post_comments',
        'journals',
        'journal_audit_logs',
    ];

    /**
     * Run the migrations.
     */
    public function up(): void
    {
        foreach (self::TABLES_WITH_SCHOOL_ID as $table) {
            if (Schema::hasTable($table)) {
                Schema::table($table, function (Blueprint $table) {
                    // Check if school_id column doesn't exist before adding
                    if (!Schema::hasColumn($table->getTable(), 'school_id')) {
                        $table->unsignedBigInteger('school_id')->default(1)->after('id');

                        // Add index for performance optimization
                        $table->index('school_id', 'idx_' . $table->getTable() . '_school_id');
                    }
                });
            }
        }

        // Add school_id as nullable to users table
        if (Schema::hasTable('users')) {
            Schema::table('users', function (Blueprint $table) {
                if (!Schema::hasColumn('users', 'school_id')) {
                    $table->unsignedBigInteger('school_id')->nullable()->after('id');

                    // Add index for performance optimization
                    $table->index('school_id', 'idx_users_school_id');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Remove school_id from users table
        if (Schema::hasTable('users')) {
            Schema::table('users', function (Blueprint $table) {
                if (Schema::hasColumn('users', 'school_id')) {
                    $table->dropIndex('idx_users_school_id');
                    $table->dropColumn('school_id');
                }
            });
        }

        // Remove school_id from all other tables
        foreach (self::TABLES_WITH_SCHOOL_ID as $table) {
            if (Schema::hasTable($table)) {
                Schema::table($table, function (Blueprint $table) {
                    if (Schema::hasColumn($table->getTable(), 'school_id')) {
                        $table->dropIndex('idx_' . $table->getTable() . '_school_id');
                        $table->dropColumn('school_id');
                    }
                });
            }
        }
    }
};
