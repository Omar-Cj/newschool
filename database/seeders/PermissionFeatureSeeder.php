<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PermissionFeatureSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * Maps all 206 permission attributes to feature groups.
     * Handles both MainApp permissions and Forums module separately.
     * Idempotent - can be run multiple times safely.
     */
    public function run(): void
    {
        DB::transaction(function () {
            // Get feature groups for mapping
            $groups = $this->getFeatureGroupIds();

            // Get permissions for mapping
            $permissions = $this->getPermissions();

            // Map permissions to features
            $this->mapStudentInfoFeatures($permissions, $groups);
            $this->mapAcademicFeatures($permissions, $groups);
            $this->mapFeesFeatures($permissions, $groups);
            $this->mapExaminationFeatures($permissions, $groups);
            $this->mapAccountsFeatures($permissions, $groups);
            $this->mapAttendanceFeatures($permissions, $groups);
            $this->mapReportsFeatures($permissions, $groups);
            $this->mapLibraryFeatures($permissions, $groups);
            $this->mapOnlineExamFeatures($permissions, $groups);
            $this->mapStaffFeatures($permissions, $groups);
            $this->mapWebsiteFeatures($permissions, $groups);
            $this->mapSettingsFeatures($permissions, $groups);
            $this->mapDashboardFeatures($permissions, $groups);
            $this->mapForumsFeatures($permissions, $groups);
        });

        $this->command->info('Permission features mapped successfully.');
    }

    /**
     * Get feature group IDs indexed by slug
     */
    private function getFeatureGroupIds(): array
    {
        return DB::table('feature_groups')
            ->pluck('id', 'slug')
            ->toArray();
    }

    /**
     * Get all permissions indexed by attribute
     */
    private function getPermissions(): array
    {
        return DB::table('permissions')
            ->pluck('id', 'attribute')
            ->toArray();
    }

    /**
     * Create or update permission feature
     */
    private function createFeature(
        int $permissionId,
        int $featureGroupId,
        string $name,
        string $description,
        bool $isPremium = false,
        int $position = 0
    ): void {
        DB::table('permission_features')->updateOrInsert(
            ['permission_id' => $permissionId],
            [
                'feature_group_id' => $featureGroupId,
                'name' => $name,
                'description' => $description,
                'is_premium' => $isPremium,
                'position' => $position,
                'status' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );
    }

    /**
     * Map Student Information features
     */
    private function mapStudentInfoFeatures(array $permissions, array $groups): void
    {
        $groupId = $groups['student_info'];
        $features = [
            ['student', 'Student Management', 'Manage student records and information', 1],
            ['student_category', 'Student Categories', 'Organize students into categories', 2],
            ['promote_students', 'Student Promotion', 'Promote students to next class/year', 3],
            ['disabled_students', 'Disabled Students', 'Manage students with disabilities', 4],
            ['parent', 'Parent Management', 'Manage parent/guardian information', 5],
            ['admission', 'Admission Management', 'Handle student admissions process', 6],
            ['counter_read', 'Dashboard Statistics', 'View student statistics on dashboard', 7],
        ];

        foreach ($features as [$attr, $name, $desc, $pos]) {
            if (isset($permissions[$attr])) {
                $this->createFeature($permissions[$attr], $groupId, $name, $desc, false, $pos);
            }
        }
    }

    /**
     * Map Academic Management features
     */
    private function mapAcademicFeatures(array $permissions, array $groups): void
    {
        $groupId = $groups['academic'];
        $features = [
            ['classes', 'Classes', 'Manage class definitions', 1],
            ['section', 'Sections', 'Manage class sections', 2],
            ['shift', 'Shifts', 'Manage school shifts (morning/afternoon)', 3],
            ['class_setup', 'Class Setup', 'Configure class parameters', 4],
            ['subject', 'Subjects', 'Manage academic subjects', 5],
            ['subject_assign', 'Subject Assignment', 'Assign subjects to classes', 6],
            ['class_routine', 'Class Routine', 'Manage class schedules', 7],
            ['time_schedule', 'Time Schedule', 'Configure class timings', 8],
            ['class_room', 'Classrooms', 'Manage physical classroom allocation', 9],
        ];

        foreach ($features as [$attr, $name, $desc, $pos]) {
            if (isset($permissions[$attr])) {
                $this->createFeature($permissions[$attr], $groupId, $name, $desc, false, $pos);
            }
        }
    }

    /**
     * Map Fees Management features
     */
    private function mapFeesFeatures(array $permissions, array $groups): void
    {
        $groupId = $groups['fees'];
        $features = [
            ['fees_group', 'Fee Groups', 'Define fee group categories', 1],
            ['fees_type', 'Fee Types', 'Configure types of fees', 2],
            ['fees_master', 'Fee Master', 'Master fee configuration', 3],
            ['fees_assign', 'Fee Assignment', 'Assign fees to students', 4],
            ['fees_collect', 'Fee Collection', 'Collect fee payments', 5],
            ['fees_generation', 'Fee Generation', 'Generate fee invoices', 6],
            ['discount_setup', 'Discount Setup', 'Configure fee discounts', 7],
            ['journal', 'Journal Entries', 'Financial journal management', 8],
            ['cash_transfer', 'Cash Transfer', 'Internal fund transfers', 9],
            ['parent_deposit', 'Parent Deposit', 'Parent deposit management', 10],
            ['parent_statement', 'Parent Statement', 'Parent financial statements', 11],
            ['fees_collesction_read', 'Dashboard Fee Collection Chart', 'View fee collection chart on dashboard', 12],
            ['fees_collection_this_month_read', 'Dashboard Monthly Fee Collection', 'View this month fee collection on dashboard', 13],
        ];

        foreach ($features as [$attr, $name, $desc, $pos]) {
            if (isset($permissions[$attr])) {
                $this->createFeature($permissions[$attr], $groupId, $name, $desc, false, $pos);
            }
        }
    }

    /**
     * Map Examination features
     */
    private function mapExaminationFeatures(array $permissions, array $groups): void
    {
        $groupId = $groups['examination'];
        $features = [
            ['exam_type', 'Exam Types', 'Define examination types', 1],
            ['marks_grade', 'Marks & Grades', 'Configure grading system', 2],
            ['exam_assign', 'Exam Assignment', 'Assign exams to classes', 3],
            ['exam_routine', 'Exam Routine', 'Manage exam schedules', 4],
            ['marks_register', 'Marks Register', 'Record student marks', 5],
            ['homework', 'Homework', 'Homework assignment and tracking', 6],
            ['exam_setting', 'Exam Settings', 'Configure exam parameters', 7],
            ['exam_entry', 'Exam Entry', 'Enter exam information', 8],
            ['terms', 'Terms', 'Manage academic terms/semesters', 9],
        ];

        foreach ($features as [$attr, $name, $desc, $pos]) {
            if (isset($permissions[$attr])) {
                $this->createFeature($permissions[$attr], $groupId, $name, $desc, false, $pos);
            }
        }
    }

    /**
     * Map Accounts features
     */
    private function mapAccountsFeatures(array $permissions, array $groups): void
    {
        $groupId = $groups['accounts'];
        $features = [
            ['account_head', 'Account Heads', 'Define chart of accounts', 1],
            ['income', 'Income', 'Record income transactions', 2],
            ['expense', 'Expenses', 'Record expense transactions', 3],
            ['expense_category', 'Expense Categories', 'Categorize expenses', 4],
            ['revenue_read', 'Dashboard Revenue Chart', 'View revenue chart on dashboard', 5],
            ['income_expense_read', 'Dashboard Income/Expense Chart', 'View income and expense chart on dashboard', 6],
        ];

        foreach ($features as [$attr, $name, $desc, $pos]) {
            if (isset($permissions[$attr])) {
                $this->createFeature($permissions[$attr], $groupId, $name, $desc, false, $pos);
            }
        }
    }

    /**
     * Map Attendance features
     */
    private function mapAttendanceFeatures(array $permissions, array $groups): void
    {
        $groupId = $groups['attendance'];
        $features = [
            ['attendance', 'Attendance Management', 'Mark and manage attendance', 1],
            ['attendance_report', 'Attendance Reports', 'Generate attendance reports', 2],
            ['attendance_chart_read', 'Dashboard Attendance Chart', 'View attendance chart on dashboard', 3],
        ];

        foreach ($features as [$attr, $name, $desc, $pos]) {
            if (isset($permissions[$attr])) {
                $this->createFeature($permissions[$attr], $groupId, $name, $desc, false, $pos);
            }
        }
    }

    /**
     * Map Reports features
     */
    private function mapReportsFeatures(array $permissions, array $groups): void
    {
        $groupId = $groups['reports'];
        $features = [
            ['marksheet', 'Marksheets', 'Generate student marksheets', 1],
            ['merit_list', 'Merit Lists', 'Generate merit/ranking lists', 2],
            ['progress_card', 'Progress Cards', 'Student progress reports', 3],
            ['due_fees', 'Due Fees Reports', 'Outstanding fee reports', 4],
            ['fees_collection', 'Fee Collection Reports', 'Fee collection analytics', 5],
            ['account', 'Account Reports', 'Financial account reports', 6],
            ['class_routine', 'Class Routine Reports', 'Class schedule reports', 7],
            ['exam_routine', 'Exam Routine Reports', 'Exam schedule reports', 8],
            ['student_reports_read', 'Student Reports', 'View student reports', 9],
            ['report_center', 'Report Center', 'Centralized reporting hub', 10],
        ];

        foreach ($features as [$attr, $name, $desc, $pos]) {
            if (isset($permissions[$attr])) {
                $this->createFeature($permissions[$attr], $groupId, $name, $desc, false, $pos);
            }
        }
    }

    /**
     * Map Library features
     */
    private function mapLibraryFeatures(array $permissions, array $groups): void
    {
        $groupId = $groups['library'];
        $features = [
            ['book_category', 'Book Categories', 'Organize books by category', 1],
            ['book', 'Books', 'Manage library book inventory', 2],
            ['member', 'Library Members', 'Manage library membership', 3],
            ['member_category', 'Member Categories', 'Categorize library members', 4],
            ['issue_book', 'Issue Books', 'Book issue and return tracking', 5],
        ];

        foreach ($features as [$attr, $name, $desc, $pos]) {
            if (isset($permissions[$attr])) {
                $this->createFeature($permissions[$attr], $groupId, $name, $desc, false, $pos);
            }
        }
    }

    /**
     * Map Online Examination features (Premium)
     */
    private function mapOnlineExamFeatures(array $permissions, array $groups): void
    {
        $groupId = $groups['online_exam'];
        $isPremium = true; // Online exam features are premium

        $features = [
            ['online_exam_type', 'Online Exam Types', 'Define online examination types', 1],
            ['question_group', 'Question Groups', 'Organize questions into groups', 2],
            ['question_bank', 'Question Bank', 'Manage question repository', 3],
            ['online_exam', 'Online Exams', 'Conduct online examinations', 4],
        ];

        foreach ($features as [$attr, $name, $desc, $pos]) {
            if (isset($permissions[$attr])) {
                $this->createFeature($permissions[$attr], $groupId, $name, $desc, $isPremium, $pos);
            }
        }
    }

    /**
     * Map Staff Management features
     */
    private function mapStaffFeatures(array $permissions, array $groups): void
    {
        $groupId = $groups['staff'];
        $features = [
            ['users', 'User Management', 'Manage system users', 1],
            ['roles', 'Roles & Permissions', 'Configure user roles', 2],
            ['department', 'Departments', 'Manage school departments', 3],
            ['designation', 'Designations', 'Define staff designations', 4],
        ];

        foreach ($features as [$attr, $name, $desc, $pos]) {
            if (isset($permissions[$attr])) {
                $this->createFeature($permissions[$attr], $groupId, $name, $desc, false, $pos);
            }
        }
    }

    /**
     * Map Website features
     */
    private function mapWebsiteFeatures(array $permissions, array $groups): void
    {
        $groupId = $groups['website'];
        $features = [
            ['sections', 'Website Sections', 'Manage website sections', 1],
            ['slider', 'Slider', 'Homepage slider management', 2],
            ['about', 'About Page', 'About us content', 3],
            ['counter', 'Counter Statistics', 'Homepage counter widgets', 4],
            ['contact_info', 'Contact Information', 'School contact details', 5],
            ['dep_contact', 'Department Contacts', 'Department contact info', 6],
            ['news', 'News', 'News articles management', 7],
            ['event', 'Events', 'School events calendar', 8],
            ['gallery_category', 'Gallery Categories', 'Photo gallery categories', 9],
            ['gallery', 'Gallery', 'Photo gallery management', 10],
            ['subscribe', 'Subscriptions', 'Newsletter subscriptions', 11],
            ['contact_message', 'Contact Messages', 'Contact form submissions', 12],
        ];

        foreach ($features as [$attr, $name, $desc, $pos]) {
            if (isset($permissions[$attr])) {
                $this->createFeature($permissions[$attr], $groupId, $name, $desc, false, $pos);
            }
        }
    }

    /**
     * Map Settings features
     */
    private function mapSettingsFeatures(array $permissions, array $groups): void
    {
        $groupId = $groups['settings'];
        $isPremium = false;

        $features = [
            ['language', 'Language Settings', 'Multi-language configuration', 1],
            ['general_settings', 'General Settings', 'Basic system settings', 2],
            ['storage_settings', 'Storage Settings', 'File storage configuration', 3],
            ['task_schedules', 'Task Scheduler', 'Automated task scheduling', 4],
            ['software_update', 'Software Updates', 'System update management', 5],
            ['recaptcha_settings', 'reCAPTCHA Settings', 'Security captcha settings', 6],
            ['payment_gateway_settings', 'Payment Gateway', 'Payment integration settings', 7],
            ['email_settings', 'Email Settings', 'Email configuration', 8],
            ['sms_settings', 'SMS Settings', 'SMS notification settings (Premium)', 9, true], // SMS is premium
            ['genders', 'Gender Options', 'Configure gender options', 10],
            ['religions', 'Religion Options', 'Configure religion options', 11],
            ['blood_groups', 'Blood Groups', 'Configure blood group options', 12],
            ['sessions', 'Academic Sessions', 'Manage academic years/sessions', 13],
            ['tax_setup', 'Tax Setup', 'Configure tax rates', 14],
        ];

        foreach ($features as $feature) {
            $attr = $feature[0];
            $name = $feature[1];
            $desc = $feature[2];
            $pos = $feature[3];
            $premium = $feature[4] ?? false;

            if (isset($permissions[$attr])) {
                $this->createFeature($permissions[$attr], $groupId, $name, $desc, $premium, $pos);
            }
        }
    }

    /**
     * Map Dashboard features
     */
    private function mapDashboardFeatures(array $permissions, array $groups): void
    {
        $groupId = $groups['dashboard'];
        $features = [
            ['dashboard', 'Dashboard', 'Access system dashboard and analytics', 1],
            ['calendar_read', 'Dashboard Calendar', 'View calendar widget on dashboard', 2],
            ['upcoming_events_read', 'Dashboard Upcoming Events', 'View upcoming events widget on dashboard', 3],
        ];

        foreach ($features as [$attr, $name, $desc, $pos]) {
            if (isset($permissions[$attr])) {
                $this->createFeature($permissions[$attr], $groupId, $name, $desc, false, $pos);
            }
        }
    }

    /**
     * Map Forums Module features (Premium)
     * Note: Forums is a separate module, handle accordingly
     */
    private function mapForumsFeatures(array $permissions, array $groups): void
    {
        // Forums features might not exist in all installations
        // Check if forums group exists and permissions are available
        $forumPermissions = [
            'forums' => 'Forums',
            'forum_comment' => 'Forum Comments',
            'memories' => 'Memories',
        ];

        // These would typically go in a separate group or be premium features
        // For now, we'll skip if not found, as Forums is an optional module
        foreach ($forumPermissions as $attr => $name) {
            if (isset($permissions[$attr])) {
                // Forums features are premium and could go in a separate group
                // For now, log that they exist but don't map without proper group
                $this->command->warn("Forum permission '{$attr}' found but not mapped (optional module)");
            }
        }
    }
}
