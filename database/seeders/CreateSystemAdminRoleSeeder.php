<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CreateSystemAdminRoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * Create role_id = 0 for Main System Admin (multi-tenant admin)
     * role_id = 0: Main System Admin (school_id = NULL, manages all schools)
     * role_id = 1: School Super Admin (has school_id, manages one school)
     */
    public function run()
    {
        $this->command->info('Creating Main System Admin role (role_id = 0)...');

        // First, delete the incorrectly created role ID 8 if it exists
        $incorrectRole = DB::table('roles')->where('id', 8)->where('slug', 'main-system-admin')->first();
        if ($incorrectRole) {
            $this->command->warn('Found incorrectly created role ID 8. Deleting it...');
            DB::table('roles')->where('id', 8)->where('slug', 'main-system-admin')->delete();
        }

        // Check if role 0 already exists
        $existingRole = DB::table('roles')->where('id', 0)->first();

        if ($existingRole) {
            $this->command->warn('Role ID 0 already exists. Skipping creation.');
        } else {
            // Insert the Main System Admin role with ID = 0
            // We need to use raw SQL to force ID = 0 (bypass auto-increment)
            DB::statement("SET SESSION sql_mode='NO_AUTO_VALUE_ON_ZERO'");

            DB::table('roles')->insert([
                'id' => 0,
                'name' => 'Main System Admin',
                'slug' => 'main-system-admin',
                'status' => '1',
                'permissions' => json_encode([
                    // Full system permissions - can manage everything including schools
                    "counter_read", "fees_collesction_read", "revenue_read",
                    "fees_collection_this_month_read", "income_expense_read",
                    "upcoming_events_read", "attendance_chart_read", "calendar_read",
                    "student_read", "student_create", "student_update", "student_delete",
                    "student_category_read", "student_category_create", "student_category_update", "student_category_delete",
                    "promote_students_read", "promote_students_create",
                    "disabled_students_read", "disabled_students_create",
                    "parent_read", "parent_create", "parent_update", "parent_delete",
                    "admission_read", "admission_create", "admission_update", "admission_delete",
                    "classes_read", "classes_create", "classes_update", "classes_delete",
                    "section_read", "section_create", "section_update", "section_delete",
                    "shift_read", "shift_create", "shift_update", "shift_delete",
                    "class_setup_read", "class_setup_create", "class_setup_update", "class_setup_delete",
                    "subject_read", "subject_create", "subject_update", "subject_delete",
                    "subject_assign_read", "subject_assign_create", "subject_assign_update", "subject_assign_delete",
                    "class_routine_read", "class_routine_create", "class_routine_update", "class_routine_delete",
                    "time_schedule_read", "time_schedule_create", "time_schedule_update", "time_schedule_delete",
                    "class_room_read", "class_room_create", "class_room_update", "class_room_delete",
                    "fees_group_read", "fees_group_create", "fees_group_update", "fees_group_delete",
                    "fees_type_read", "fees_type_create", "fees_type_update", "fees_type_delete",
                    "fees_master_read", "fees_master_create", "fees_master_update", "fees_master_delete",
                    "fees_assign_read", "fees_assign_create", "fees_assign_update", "fees_assign_delete",
                    "fees_collect_read", "fees_collect_create", "fees_collect_update", "fees_collect_delete",
                    "exam_type_read", "exam_type_create", "exam_type_update", "exam_type_delete",
                    "marks_grade_read", "marks_grade_create", "marks_grade_update", "marks_grade_delete",
                    "exam_assign_read", "exam_assign_create", "exam_assign_update", "exam_assign_delete",
                    "exam_routine_read", "exam_routine_create", "exam_routine_update", "exam_routine_delete",
                    "marks_register_read", "marks_register_create", "marks_register_update", "marks_register_delete",
                    "homework_read", "homework_create", "homework_update", "homework_delete",
                    "exam_setting_read", "exam_setting_update",
                    "account_head_read", "account_head_create", "account_head_update", "account_head_delete",
                    "income_read", "income_create", "income_update", "income_delete",
                    "expense_read", "expense_create", "expense_update", "expense_delete",
                    "attendance_read", "attendance_create",
                    "report_marksheet_read", "report_merit_list_read", "report_progress_card_read",
                    "report_due_fees_read", "report_fees_collection_read", "report_account_read",
                    "report_class_routine_read", "report_exam_routine_read", "report_attendance_read",
                    "language_read", "language_create", "language_update", "language_update_terms", "language_delete",
                    "user_read", "user_create", "user_update", "user_delete",
                    "role_read", "role_create", "role_update", "role_delete",
                    "department_read", "department_create", "department_update", "department_delete",
                    "designation_read", "designation_create", "designation_update", "designation_delete",
                    "page_sections_read", "page_sections_update",
                    "slider_read", "slider_create", "slider_update", "slider_delete",
                    "about_read", "about_create", "about_update", "about_delete",
                    "counter_create", "counter_update", "counter_delete",
                    "contact_info_read", "contact_info_create", "contact_info_update", "contact_info_delete",
                    "dep_contact_read", "dep_contact_create", "dep_contact_update", "dep_contact_delete",
                    "news_read", "news_create", "news_update", "news_delete",
                    "event_read", "event_create", "event_update", "event_delete",
                    "gallery_category_read", "gallery_category_create", "gallery_category_update", "gallery_category_delete",
                    "gallery_read", "gallery_create", "gallery_update", "gallery_delete",
                    "subscribe_read", "contact_message_read",
                    "general_settings_read", "general_settings_update",
                    "storage_settings_read", "storage_settings_update",
                    "task_schedules_read", "task_schedules_update",
                    "software_update_read", "software_update_update",
                    "recaptcha_settings_read", "recaptcha_settings_update",
                    "payment_gateway_settings_read", "payment_gateway_settings_update",
                    "email_settings_read", "email_settings_update",
                    "sms_settings_read", "sms_settings_update",
                    "gender_read", "gender_create", "gender_update", "gender_delete",
                    "religion_read", "religion_create", "religion_update", "religion_delete",
                    "blood_group_read", "blood_group_create", "blood_group_update", "blood_group_delete",
                    "session_read", "session_create", "session_update", "session_delete",
                    "book_category_read", "book_category_create", "book_category_update", "book_category_delete",
                    "book_read", "book_create", "book_update", "book_delete",
                    "member_read", "member_create", "member_update", "member_delete",
                    "member_category_read", "member_category_create", "member_category_update", "member_category_delete",
                    "issue_book_read", "issue_book_create", "issue_book_update", "issue_book_delete",
                    "online_exam_type_read", "online_exam_type_create", "online_exam_type_update", "online_exam_type_delete",
                    "question_group_read", "question_group_create", "question_group_update", "question_group_delete",
                    "question_bank_read", "question_bank_create", "question_bank_update", "question_bank_delete",
                    "online_exam_read", "online_exam_create", "online_exam_update", "online_exam_delete",
                    "forum_list", "forum_create", "forum_update", "forum_delete", "forum_feeds",
                    "forum_comment_list", "forum_comment_create", "forum_comment_update", "forum_comment_delete",
                    "memory_list", "memory_create", "memory_update", "memory_delete",
                    "exam_entry_publish",
                    // School management permissions (MainApp)
                    "school_read", "school_create", "school_update", "school_delete",
                ]),
                'created_at' => now(),
                'updated_at' => now(),
                'branch_id' => 1,  // Default branch value (required by DB constraint)
            ]);

            DB::statement("SET SESSION sql_mode=''");

            $this->command->info('✅ Main System Admin role (ID: 0) created successfully!');
        }

        // Create a new System Admin user with role_id = 0
        $this->command->info('Creating System Admin user with role_id = 0...');

        $existingSystemAdmin = DB::table('users')
            ->where('email', 'system-admin@system.local')
            ->first();

        if ($existingSystemAdmin) {
            $this->command->warn('System Admin user already exists. Updating role...');
            DB::table('users')
                ->where('email', 'system-admin@system.local')
                ->update(['role_id' => 0]);
        } else {
            DB::table('users')->insert([
                'name' => 'System Admin',
                'email' => 'system-admin@system.local',
                'password' => bcrypt('password'), // Remember to change this!
                'role_id' => 0,
                'school_id' => null,  // System admin has no school
                'status' => '1',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        $this->command->info('✅ System Admin user created/updated successfully!');

        // Show the role structure
        $this->command->info("\n📋 Current Role Structure:");
        $this->command->line("  role_id = 0: Main System Admin (manages all schools, school_id = NULL)");
        $this->command->line("  role_id = 1: School Super Admin (manages one school, has school_id)");
        $this->command->line("  role_id = 2: School Admin");
        $this->command->line("  role_id = 3: Staff");
        $this->command->line("  role_id = 4: Accounting");
        $this->command->line("  role_id = 5: Teacher");
        $this->command->line("  role_id = 6: Student");
        $this->command->line("  role_id = 7: Guardian");
    }
}
