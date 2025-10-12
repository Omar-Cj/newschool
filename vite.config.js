import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/sass/app.scss',
                'resources/js/app.js',
                'resources/js/components/DynamicReportForm.js',
                'resources/js/components/DependencyHandler.js',
                'resources/js/services/ReportApiService.js',
                'resources/js/utils/FormValidation.js',
            ],
            refresh: true,
        }),
    ],
    build: {
        rollupOptions: {
            output: {
                manualChunks: {
                    'report-components': [
                        'resources/js/components/DynamicReportForm.js',
                        'resources/js/components/DependencyHandler.js',
                    ],
                    'report-services': [
                        'resources/js/services/ReportApiService.js',
                        'resources/js/utils/FormValidation.js',
                    ],
                },
            },
        },
    },
});
