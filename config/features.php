<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Feature Access Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration for feature-based access control system
    | Maps features to routes, defines premium features, and upgrade URLs
    |
    */

    /**
     * Access denied message shown to users
     */
    'access_denied_message' => 'This feature is not available in your current package. Please upgrade to access this functionality.',

    /**
     * Whether to display feature names in error messages
     * Set to false in production to avoid information leakage
     */
    'display_feature_names' => env('APP_DEBUG', false),

    /**
     * Cache duration for feature access data (in seconds)
     * Default: 1 hour
     */
    'cache_ttl' => 3600,

    /**
     * Premium features requiring higher-tier packages
     * Used for UI badges and upgrade prompts
     */
    'premium_features' => [
        'multi_branch',
        'vehicle_tracking',
        'advanced_reporting',
        'api_access',
        'custom_branding',
        'sms_integration',
        'bulk_operations',
        'data_export',
    ],

    /**
     * Feature to route mapping
     * Maps feature attributes to route names for access control
     */
    'feature_routes' => [
        'student_management' => [
            'student.index',
            'student.create',
            'student.store',
            'student.edit',
            'student.update',
            'student.destroy',
        ],
        'attendance' => [
            'attendance.index',
            'attendance.mark',
            'attendance.report',
            'attendance.student',
        ],
        'examination' => [
            'examination.index',
            'examination.create',
            'examination.grade',
            'examination.result',
            'examination.publish',
        ],
        'online_examination' => [
            'online-exam.index',
            'online-exam.create',
            'online-exam.start',
            'online-exam.submit',
        ],
        'fees_management' => [
            'fees.index',
            'fees.collect',
            'fees.report',
            'fees.invoice',
        ],
        'library' => [
            'library.index',
            'library.books',
            'library.issue',
            'library.return',
        ],
        'multi_branch' => [
            'branch.index',
            'branch.create',
            'branch.switch',
        ],
        'vehicle_tracking' => [
            'vehicle.index',
            'vehicle.track',
            'vehicle.routes',
        ],
    ],

    /**
     * Feature groups for organized access control
     * Groups related features together
     */
    'feature_groups' => [
        'academic' => [
            'student_management',
            'teacher_management',
            'class_management',
            'subject_management',
        ],
        'assessment' => [
            'attendance',
            'examination',
            'online_examination',
            'grading',
        ],
        'financial' => [
            'fees_management',
            'payment_gateway',
            'financial_reports',
        ],
        'communication' => [
            'notifications',
            'sms_integration',
            'email_integration',
            'live_chat',
        ],
        'advanced' => [
            'multi_branch',
            'vehicle_tracking',
            'api_access',
            'custom_branding',
        ],
    ],

    /**
     * Menu structure with feature requirements
     * Used by MenuGeneratorService to filter menu items
     */
    'menu_structure' => [
        [
            'title' => 'Dashboard',
            'url' => '/dashboard',
            'icon' => 'fa-home',
            // No feature required - always visible
        ],
        [
            'title' => 'Students',
            'icon' => 'fa-users',
            'feature_required' => 'student_management',
            'permission_required' => 'student.view',
            'children' => [
                [
                    'title' => 'All Students',
                    'url' => '/students',
                    'permission_required' => 'student.view',
                ],
                [
                    'title' => 'Add Student',
                    'url' => '/students/create',
                    'permission_required' => 'student.create',
                ],
                [
                    'title' => 'Student Promotion',
                    'url' => '/students/promotion',
                    'permission_required' => 'student.promote',
                ],
            ],
        ],
        [
            'title' => 'Attendance',
            'icon' => 'fa-calendar-check',
            'feature_required' => 'attendance',
            'permission_required' => 'attendance.view',
            'children' => [
                [
                    'title' => 'Mark Attendance',
                    'url' => '/attendance/mark',
                    'permission_required' => 'attendance.mark',
                ],
                [
                    'title' => 'Attendance Report',
                    'url' => '/attendance/report',
                    'permission_required' => 'attendance.report',
                ],
            ],
        ],
        [
            'title' => 'Examinations',
            'icon' => 'fa-file-alt',
            'feature_required' => 'examination',
            'permission_required' => 'examination.view',
            'children' => [
                [
                    'title' => 'Exam Schedule',
                    'url' => '/examination/schedule',
                ],
                [
                    'title' => 'Grade Entry',
                    'url' => '/examination/grade',
                    'permission_required' => 'examination.grade',
                ],
                [
                    'title' => 'Results',
                    'url' => '/examination/results',
                ],
            ],
        ],
        [
            'title' => 'Online Exams',
            'icon' => 'fa-laptop',
            'feature_required' => 'online_examination',
            'permission_required' => 'online_exam.view',
            'badge' => 'Premium',
            'badge_class' => 'badge-warning',
            'children' => [
                [
                    'title' => 'Manage Exams',
                    'url' => '/online-exam',
                ],
                [
                    'title' => 'Question Bank',
                    'url' => '/online-exam/questions',
                ],
            ],
        ],
        [
            'title' => 'Fees',
            'icon' => 'fa-money-bill',
            'feature_required' => 'fees_management',
            'permission_required' => 'fees.view',
            'children' => [
                [
                    'title' => 'Collect Fees',
                    'url' => '/fees/collect',
                    'permission_required' => 'fees.collect',
                ],
                [
                    'title' => 'Fee Reports',
                    'url' => '/fees/report',
                ],
            ],
        ],
        [
            'title' => 'Library',
            'icon' => 'fa-book',
            'feature_required' => 'library',
            'permission_required' => 'library.view',
            'children' => [
                [
                    'title' => 'Books',
                    'url' => '/library/books',
                ],
                [
                    'title' => 'Issue Book',
                    'url' => '/library/issue',
                    'permission_required' => 'library.issue',
                ],
            ],
        ],
        [
            'title' => 'Multi-Branch',
            'icon' => 'fa-building',
            'feature_required' => 'multi_branch',
            'role_required' => [1, 2], // Admin and Super Admin only
            'badge' => 'Premium',
            'badge_class' => 'badge-danger',
            'hide_if_empty' => true,
            'children' => [
                [
                    'title' => 'Manage Branches',
                    'url' => '/branches',
                ],
                [
                    'title' => 'Branch Reports',
                    'url' => '/branches/reports',
                ],
            ],
        ],
        [
            'title' => 'Vehicle Tracking',
            'icon' => 'fa-bus',
            'feature_required' => 'vehicle_tracking',
            'permission_required' => 'vehicle.view',
            'badge' => 'Premium',
            'badge_class' => 'badge-danger',
        ],
    ],

    /**
     * Upgrade URLs by package type
     */
    'upgrade_urls' => [
        'basic' => '/subscription/packages/standard',
        'standard' => '/subscription/packages/premium',
        'premium' => '/subscription/packages/enterprise',
    ],

    /**
     * Contact support URL for feature requests
     */
    'support_url' => '/contact/support',

    /**
     * Features documentation URL
     */
    'documentation_url' => 'https://docs.example.com/features',
];
