<?php

/**
 * PDF Configuration for Report Generation
 *
 * This configuration file defines settings for generating professional PDF reports
 * using DomPDF library through Laravel PDF package.
 */

return [

    /*
    |--------------------------------------------------------------------------
    | PDF Driver
    |--------------------------------------------------------------------------
    |
    | The default PDF driver to use. Options: 'dompdf', 'tcpdf', 'wkhtmltopdf'
    | For this project, we use DomPDF for optimal performance.
    |
    */
    'driver' => 'dompdf',

    /*
    |--------------------------------------------------------------------------
    | DomPDF Options
    |--------------------------------------------------------------------------
    |
    | Configuration options specific to DomPDF
    |
    */
    'dompdf' => [
        // Enable remote content loading (for external CSS/images)
        'enable_remote' => false,

        // Enable PHP rendering in PDFs (disabled for security)
        'enable_php' => false,

        // Enable CSS Float support
        'enable_css_float' => true,

        // Enable HTML5 parser
        'enable_html5_parser' => true,

        // Default font
        'default_font' => 'DejaVu Sans',

        // Font cache directory
        'font_cache' => storage_path('fonts/'),

        // Temporary directory
        'temp_dir' => sys_get_temp_dir(),

        // Default paper size
        'default_paper_size' => 'a4',

        // Default orientation
        'default_orientation' => 'landscape',

        // DPI setting for images
        'dpi' => 96,

        // Font height ratio
        'font_height_ratio' => 1.1,

        // Enable JavaScript
        'enable_javascript' => false,

        // JavaScript timeout (milliseconds)
        'javascript_timeout' => 10000,

        // Log output file
        'log_output_file' => storage_path('logs/dompdf.log'),

        // Enable font subsetting
        'enable_font_subsetting' => true,

        // PDF backend
        'pdf_backend' => 'CPDF',

        // Default media type
        'default_media_type' => 'print',

        // Default protocol (file://, http://, https://)
        'default_protocol' => 'file://',

        // Base host for file:// protocol
        'base_host' => null,

        // Base path for file:// protocol
        'base_path' => public_path(),

        // HTTP context options
        'http_context' => [
            'ssl' => [
                'verify_peer' => false,
                'verify_peer_name' => false,
                'allow_self_signed' => true,
            ],
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Performance Optimization
    |--------------------------------------------------------------------------
    |
    | Settings to optimize PDF generation performance
    |
    */
    'performance' => [
        // Enable caching for repeated PDF generations
        'cache_enabled' => env('PDF_CACHE_ENABLED', false),

        // Cache duration in seconds
        'cache_duration' => env('PDF_CACHE_DURATION', 3600),

        // Maximum execution time in seconds
        'max_execution_time' => env('PDF_MAX_EXECUTION_TIME', 120),

        // Memory limit for PDF generation
        'memory_limit' => env('PDF_MEMORY_LIMIT', '256M'),

        // Compress PDF output
        'compress' => env('PDF_COMPRESS', true),

        // Image quality (0-100)
        'image_quality' => env('PDF_IMAGE_QUALITY', 75),
    ],

    /*
    |--------------------------------------------------------------------------
    | Report-Specific Settings
    |--------------------------------------------------------------------------
    |
    | Custom settings for different types of reports
    |
    */
    'reports' => [
        'outstanding_payments' => [
            'paper_size' => 'a4',
            'orientation' => 'landscape',
            'title' => 'Outstanding Payments Report',
            'author' => config('app.name', 'School Management System'),
            'subject' => 'Financial Report',
            'keywords' => 'payments, outstanding, financial, report',
        ],

        'school_growth' => [
            'paper_size' => 'a4',
            'orientation' => 'landscape',
            'title' => 'School Growth Report',
            'author' => config('app.name', 'School Management System'),
            'subject' => 'Analytics Report',
            'keywords' => 'growth, analytics, schools, report',
        ],

        'payment_collection' => [
            'paper_size' => 'a4',
            'orientation' => 'landscape',
            'title' => 'Payment Collection Report',
            'author' => config('app.name', 'School Management System'),
            'subject' => 'Financial Report',
            'keywords' => 'payments, collection, financial, report',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Security Settings
    |--------------------------------------------------------------------------
    |
    | PDF security and encryption settings
    |
    */
    'security' => [
        // Enable PDF encryption
        'encryption' => env('PDF_ENCRYPTION_ENABLED', false),

        // Owner password (for editing)
        'owner_password' => env('PDF_OWNER_PASSWORD', null),

        // User password (for viewing)
        'user_password' => env('PDF_USER_PASSWORD', null),

        // Permissions
        'permissions' => [
            'print' => true,
            'modify' => false,
            'copy' => true,
            'annotate' => false,
        ],
    ],

];
