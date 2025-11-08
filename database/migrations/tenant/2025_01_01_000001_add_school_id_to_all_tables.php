<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * List of tables that require school_id column (unsigned big integer, default 1, not nullable)
     */
    protected function getSchoolIdTables(): array
    {
        return [
            // Academic tables
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

            // Student tables
            'students',
            'student_categories',
            'student_services',
            'student_absent_notifications',
            'parent_guardians',
            'parent_deposits',
            'parent_deposit_transactions',
            'parent_balances',

            // Staff tables
            'staff',
            'departments',
            'designations',
            'leave_types',
            'leave_requests',

            // Fees tables
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

            // Exam tables
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

            // Library tables
            'books',
            'book_categories',
            'members',
            'member_categories',
            'issue_books',

            // Attendance and homework tables
            'attendances',
            'subject_attendances',
            'homework',
            'homework_students',

            // Communication and miscellaneous tables
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

            // Accounting tables
            'account_heads',
            'incomes',
            'expenses',
            'expense_categories',
            'cash_transfers',
            'terms',
            'term_definitions',

            // Communication settings tables
            'sms_mail_logs',
            'sms_mail_templates',
            'system_notifications',

            // Settings tables
            'settings',
            'notification_settings',
            'online_admission_settings',

            // Forum tables
            'forum_posts',
            'forum_post_comments',

            // Journal tables
            'journals',
            'journal_audit_logs',
        ];
    }

    /**
     * Run the migrations.
     */
    public function up(): void
    {
        try {
            $tables = $this->getSchoolIdTables();

            foreach ($tables as $tableName) {
                // Check if table exists before attempting to add column
                if (!Schema::hasTable($tableName)) {
                    Log::warning("Table '{$tableName}' does not exist, skipping school_id addition");
                    continue;
                }

                Schema::table($tableName, function (Blueprint $blueprint) use ($tableName) {
                    // Only add school_id if it doesn't already exist
                    if (!Schema::hasColumn($tableName, 'school_id')) {
                        // Add school_id column after 'id' if the table has 'id' column
                        if (Schema::hasColumn($tableName, 'id')) {
                            $blueprint->unsignedBigInteger('school_id')
                                ->default(1)
                                ->after('id');
                        } else {
                            // For tables without 'id', add at the beginning
                            $blueprint->unsignedBigInteger('school_id')->default(1);
                        }

                        // Add index for performance
                        $blueprint->index('school_id');

                        Log::info("Added school_id column to '{$tableName}' table");
                    } else {
                        Log::info("school_id column already exists in '{$tableName}' table, skipping");
                    }
                });
            }

            // Handle users table separately (nullable school_id)
            if (Schema::hasTable('users')) {
                Schema::table('users', function (Blueprint $blueprint) {
                    if (!Schema::hasColumn('users', 'school_id')) {
                        $blueprint->unsignedBigInteger('school_id')
                            ->nullable()
                            ->after('id');

                        $blueprint->index('school_id');

                        Log::info("Added nullable school_id column to 'users' table");
                    } else {
                        Log::info("school_id column already exists in 'users' table, skipping");
                    }
                });
            }

            Log::info("Successfully completed school_id migration");
        } catch (\Exception $e) {
            Log::error("Error during school_id migration: " . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        try {
            $tables = $this->getSchoolIdTables();

            foreach ($tables as $tableName) {
                // Check if table exists before attempting to drop column
                if (!Schema::hasTable($tableName)) {
                    Log::warning("Table '{$tableName}' does not exist, skipping school_id removal");
                    continue;
                }

                Schema::table($tableName, function (Blueprint $blueprint) use ($tableName) {
                    if (Schema::hasColumn($tableName, 'school_id')) {
                        // Drop the index first
                        try {
                            $blueprint->dropIndex([
                                'school_id'
                            ]);
                        } catch (\Exception $e) {
                            Log::warning("Could not drop index for school_id in '{$tableName}': " . $e->getMessage());
                        }

                        // Drop the column
                        $blueprint->dropColumn('school_id');
                        Log::info("Removed school_id column from '{$tableName}' table");
                    }
                });
            }

            // Handle users table separately
            if (Schema::hasTable('users')) {
                Schema::table('users', function (Blueprint $blueprint) {
                    if (Schema::hasColumn('users', 'school_id')) {
                        try {
                            $blueprint->dropIndex([
                                'school_id'
                            ]);
                        } catch (\Exception $e) {
                            Log::warning("Could not drop index for school_id in 'users': " . $e->getMessage());
                        }

                        $blueprint->dropColumn('school_id');
                        Log::info("Removed school_id column from 'users' table");
                    }
                });
            }

            Log::info("Successfully reverted school_id migration");
        } catch (\Exception $e) {
            Log::error("Error during school_id migration rollback: " . $e->getMessage());
            throw $e;
        }
    }
};
