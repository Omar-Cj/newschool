<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Enhanced Fee System Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration options for the enhanced service-based fee system
    |
    */

    // Enable enhanced fee system by default (can be overridden by database setting)
    'use_enhanced_system' => env('USE_ENHANCED_FEE_SYSTEM', false),

    // Academic level configuration
    'academic_levels' => [
        'kg' => 'Kindergarten',
        'primary' => 'Primary School',
        'secondary' => 'Secondary School', 
        'high_school' => 'High School',
        'all' => 'All Levels'
    ],

    // Fee categories
    'fee_categories' => [
        'academic' => 'Academic Fees',
        'transport' => 'Transportation',
        'meal' => 'Meals & Cafeteria',
        'accommodation' => 'Accommodation',
        'activity' => 'Activities & Sports',
        'other' => 'Other Fees'
    ],

    // Discount types available
    'discount_types' => [
        'none' => 'No Discount',
        'percentage' => 'Percentage Discount',
        'fixed' => 'Fixed Amount Discount',
        'override' => 'Override Amount'
    ],

    // Default settings for new services
    'defaults' => [
        'due_date_offset' => 30, // Days from term start
        'academic_level' => 'all',
        'category' => 'academic',
        'is_mandatory_for_level' => false,
    ],

    // Service generation settings
    'service_generation' => [
        'batch_size' => 1000, // Students processed per batch
        'auto_create_services' => true, // Auto-create services during enrollment
        'notify_on_generation' => true, // Send notifications after generation
    ],

    // Migration settings
    'migration' => [
        'preserve_legacy_data' => true, // Keep legacy data after migration
        'verify_migration' => true, // Verify data integrity after migration
        'backup_before_migration' => true,
    ],

    // Academic level detection patterns
    'level_detection_patterns' => [
        'kg' => [
            'keywords' => ['kg', 'kindergarten', 'nursery', 'pre-school', 'reception', 'prep'],
            'numeric_range' => ['min' => 0, 'max' => 0]
        ],
        'primary' => [
            'keywords' => ['primary', 'elementary', 'grade', 'class', 'std'],
            'numeric_range' => ['min' => 1, 'max' => 8]  // Updated: Classes 1-8 = Primary
        ],
        'secondary' => [
            'keywords' => ['secondary', 'middle', 'junior', 'form'],  // Added 'form' keyword
            'form_range' => ['min' => 1, 'max' => 4],  // Form 1-4 = Secondary
            'numeric_range' => ['min' => 9, 'max' => 10]  // Updated: Grades 9-10 for fallback systems
        ],
        'high_school' => [
            'keywords' => ['high', 'senior', 'college', 'form'],
            'form_range' => ['min' => 5, 'max' => 6],  // Form 5-6 if applicable
            'numeric_range' => ['min' => 11, 'max' => 12]
        ]
    ],

    // System validation rules
    'validation' => [
        'required_migrations' => [
            '2025_01_09_120000_enhance_fees_types_table',
            '2025_01_09_121000_create_student_services_table',
            '2025_01_09_122000_create_academic_level_configs_table',
            '2025_01_09_125000_create_fee_system_migration_logs_table'
        ],
        'required_tables' => [
            'student_services',
            'academic_level_configs',
            'fee_system_migration_logs'
        ],
        'required_columns' => [
            'fees_types.academic_level',
            'fees_types.category',
            'fees_types.amount',
            'fees_collects.fee_type_id'
        ]
    ]
];