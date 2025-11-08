<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * List of all tables that have school_id column (non-nullable, default 1)
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

            Log::info('Starting population of school_id for existing data');

            foreach ($tables as $tableName) {
                // Check if table exists before attempting to update
                if (!Schema::hasTable($tableName)) {
                    Log::warning("Table '{$tableName}' does not exist, skipping population");
                    continue;
                }

                // Check if school_id column exists
                if (!Schema::hasColumn($tableName, 'school_id')) {
                    Log::warning("Table '{$tableName}' does not have school_id column, skipping population");
                    continue;
                }

                // Get current record count
                $count = DB::table($tableName)->count();

                if ($count === 0) {
                    Log::info("Table '{$tableName}' is empty, skipping population");
                    continue;
                }

                // Update all records with null or 0 school_id to default value of 1
                try {
                    $updated = DB::table($tableName)
                        ->where(function ($query) {
                            $query->whereNull('school_id')
                                  ->orWhere('school_id', '=', 0);
                        })
                        ->update(['school_id' => 1]);

                    Log::info("Table '{$tableName}': Updated {$updated} records with school_id = 1 (Total records: {$count})");
                } catch (\Exception $e) {
                    Log::error("Error updating school_id in '{$tableName}': " . $e->getMessage());
                    throw $e;
                }
            }

            // Handle users table separately (special logic for role_id)
            if (Schema::hasTable('users')) {
                Log::info('Processing users table with role-based school_id logic');

                if (Schema::hasColumn('users', 'school_id')) {
                    $totalUsers = DB::table('users')->count();

                    if ($totalUsers === 0) {
                        Log::info("Users table is empty, skipping population");
                    } else {
                        // Set school_id = 1 for all regular users (role_id != 1)
                        // Keep school_id as NULL for admins (role_id = 1)
                        try {
                            $regularUsersUpdated = DB::table('users')
                                ->where(function ($query) {
                                    $query->whereNull('school_id')
                                          ->orWhere('school_id', '=', 0);
                                })
                                ->where(function ($query) {
                                    $query->where('role_id', '!=', 1)
                                          ->orWhereNull('role_id');
                                })
                                ->update(['school_id' => 1]);

                            Log::info("Users table: Updated {$regularUsersUpdated} regular users with school_id = 1");

                            // Keep school_id NULL for admins (role_id = 1)
                            $adminCount = DB::table('users')
                                ->where('role_id', '=', 1)
                                ->count();

                            Log::info("Users table: {$adminCount} admin users kept with NULL school_id (role_id = 1)");
                            Log::info("Users table: Total users processed = {$totalUsers}");
                        } catch (\Exception $e) {
                            Log::error("Error updating school_id in users table: " . $e->getMessage());
                            throw $e;
                        }
                    }
                } else {
                    Log::warning("Users table does not have school_id column, skipping population");
                }
            }

            Log::info('Successfully completed population of school_id for existing data');
        } catch (\Exception $e) {
            Log::error("Critical error during school_id population: " . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        try {
            Log::info('Reversing school_id population - resetting to NULL or 0');

            $tables = $this->getSchoolIdTables();

            foreach ($tables as $tableName) {
                // Check if table exists before attempting to update
                if (!Schema::hasTable($tableName)) {
                    Log::warning("Table '{$tableName}' does not exist, skipping reversal");
                    continue;
                }

                // Check if school_id column exists
                if (!Schema::hasColumn($tableName, 'school_id')) {
                    Log::warning("Table '{$tableName}' does not have school_id column, skipping reversal");
                    continue;
                }

                try {
                    // Reset all school_id values to NULL
                    $updated = DB::table($tableName)->update(['school_id' => null]);
                    Log::info("Table '{$tableName}': Reset {$updated} records to NULL school_id");
                } catch (\Exception $e) {
                    Log::warning("Error resetting school_id in '{$tableName}': " . $e->getMessage());
                    // Continue with other tables instead of throwing
                }
            }

            // Handle users table separately
            if (Schema::hasTable('users') && Schema::hasColumn('users', 'school_id')) {
                try {
                    $updated = DB::table('users')->update(['school_id' => null]);
                    Log::info("Users table: Reset {$updated} records to NULL school_id");
                } catch (\Exception $e) {
                    Log::warning("Error resetting school_id in users table: " . $e->getMessage());
                }
            }

            Log::info('Successfully reverted school_id population');
        } catch (\Exception $e) {
            Log::error("Critical error during school_id population reversal: " . $e->getMessage());
            throw $e;
        }
    }
};
