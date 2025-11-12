<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FeatureCheck
{
    /**
     * Map route-level feature groups to database permission attributes
     *
     * This mapping translates high-level feature groups used in routes
     * (like 'student_info', 'academic', 'fees') to the actual permission
     * attributes stored in the database.
     *
     * Based on package_permission_features and permissions tables structure.
     */
    private $featureMap = [
        // Staff Management
        'staff_manage' => ['users', 'roles', 'department', 'designation'],

        // Student Information System
        'student_info' => ['student', 'student_category', 'parent', 'promote_students', 'disabled_students', 'admission'],

        // Academic Management
        'academic' => ['classes', 'section', 'shift', 'class_setup', 'subject', 'subject_assign', 'class_room', 'time_schedule', 'class_routine', 'sessions'],

        // Fee Management
        'fees' => ['fees_type', 'fees_group', 'fees_master', 'fees_assign', 'fees_collect', 'fees_generation', 'discount_setup', 'parent_deposit', 'parent_statement'],

        // Examination System
        'examination' => ['exam_type', 'exam_assign', 'marks_grade', 'marks_register', 'homework', 'exam_setting', 'exam_entry', 'terms'],

        // Accounts & Finance
        'account' => ['income', 'expense', 'expense_category', 'account_head', 'journal', 'cash_transfer'],

        // Attendance System
        'attendance' => ['attendance', 'attendance_report'],

        // Library Management
        'library' => ['book', 'book_category', 'member', 'member_category', 'issue_book'],

        // Online Examination
        'online_examination' => ['online_exam', 'online_exam_type', 'question_group', 'question_bank'],

        // Online Admission
        'online_admission' => ['admission'],

        // Reporting System
        'report' => ['report_center', 'student_reports_read', 'marksheet', 'merit_list', 'progress_card', 'due_fees', 'fees_collection', 'account', 'attendance_report', 'class_routine', 'exam_routine'],

        // Routine Management
        'routine' => ['class_routine', 'exam_routine', 'time_schedule'],

        // Website Setup
        'website_setup' => ['sections', 'slider', 'about', 'counter', 'contact_info', 'dep_contact', 'news', 'event', 'subscribe'],

        // Gallery
        'gallery' => ['gallery', 'gallery_category'],

        // System Settings
        'setting' => ['general_settings', 'storage_settings', 'task_schedules', 'software_update', 'recaptcha_settings', 'payment_gateway_settings', 'email_settings', 'sms_settings', 'tax_setup'],

        // Language Settings
        'language' => ['language'],

        // Dashboard
        'dashboard' => ['dashboard'],
    ];

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @param  string  $feature  OLD feature key to check
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next, $feature)
    {
        // Bypass feature checks for single-school installations
        if (!env('APP_SAAS', false)) {
            return $next($request);
        }

        // Require authentication
        if (!Auth::check()) {
            return abort(403, 'Authentication required');
        }

        // Translate OLD feature key to NEW permission attributes
        // If no mapping exists, use the feature key as-is (for future-proofing)
        $newFeatures = $this->featureMap[$feature] ?? [$feature];

        // Check if user has ANY of the mapped features in their package
        if (hasAnyFeature($newFeatures)) {
            return $next($request);
        }

        return abort(403, 'Access Denied - Feature not available in your package');
    }
}
