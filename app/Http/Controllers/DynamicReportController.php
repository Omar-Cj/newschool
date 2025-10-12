<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Services\ExportService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

/**
 * Controller for dynamic report generation and export
 * Handles multiple export formats with security and validation
 */
class DynamicReportController extends Controller
{
    protected ExportService $exportService;

    /**
     * Constructor with dependency injection
     *
     * @param ExportService $exportService
     */
    public function __construct(ExportService $exportService)
    {
        $this->exportService = $exportService;

        // Apply authentication middleware
        $this->middleware('auth');

        // Apply permission middleware (adjust permission names as needed)
        $this->middleware('permission:reports.export')->only(['export', 'downloadExport']);
    }

    /**
     * Export report to specified format
     *
     * @param Request $request
     * @param int $reportId Report identifier
     * @return mixed Export response or job confirmation
     */
    public function export(Request $request, int $reportId)
    {
        try {
            // Validate request
            $validator = Validator::make($request->all(), [
                'format' => 'required|string|in:excel,pdf,csv',
                'results' => 'required|array',
                'columns' => 'required|array',
                'columns.*.key' => 'required|string',
                'columns.*.label' => 'required|string',
                'columns.*.type' => 'nullable|string|in:string,number,currency,percentage,date,datetime,boolean',
                'metadata' => 'nullable|array',
                'metadata.name' => 'nullable|string',
                'metadata.parameters' => 'nullable|array',
                'force_async' => 'nullable|boolean',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors(),
                ], 422);
            }

            $validated = $validator->validated();

            // Security: Ensure user has permission to access this report
            // (You can add additional permission checks here based on your requirements)
            if (!$this->canAccessReport($reportId)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized access to report',
                ], 403);
            }

            // Prepare metadata
            $metadata = $validated['metadata'] ?? [];
            $metadata['generated_at'] = now()->format('Y-m-d H:i:s');
            $metadata['generated_by'] = auth()->user()->name;

            // Process export
            $result = $this->exportService->export(
                $reportId,
                $validated['format'],
                $validated['results'],
                $validated['columns'],
                $metadata,
                $validated['force_async'] ?? false
            );

            // If queued, return JSON response
            if (is_array($result)) {
                return response()->json([
                    'success' => true,
                    'data' => $result,
                ]);
            }

            // Otherwise, return file download
            return $result;

        } catch (\InvalidArgumentException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 400);

        } catch (\RuntimeException $e) {
            Log::error('Export failed', [
                'report_id' => $reportId,
                'error' => $e->getMessage(),
                'user_id' => auth()->id(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Export failed. Please try again.',
            ], 500);

        } catch (\Exception $e) {
            Log::error('Unexpected export error', [
                'report_id' => $reportId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'An unexpected error occurred.',
            ], 500);
        }
    }

    /**
     * Download previously generated export
     *
     * @param Request $request
     * @return mixed File download or error response
     */
    public function downloadExport(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'key' => 'required|string',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid download key',
                ], 422);
            }

            $downloadKey = $request->input('key');

            // Retrieve download information from cache
            $downloadInfo = Cache::get($downloadKey);

            if (!$downloadInfo) {
                return response()->json([
                    'success' => false,
                    'message' => 'Download link expired or invalid',
                ], 404);
            }

            // Verify file exists
            $disk = config('filesystems.default');
            if (!Storage::disk($disk)->exists($downloadInfo['path'])) {
                Cache::forget($downloadKey);
                return response()->json([
                    'success' => false,
                    'message' => 'Export file not found',
                ], 404);
            }

            // Get file content
            $content = Storage::disk($disk)->get($downloadInfo['path']);

            // Determine content type
            $contentType = match($downloadInfo['format']) {
                'excel' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                'pdf' => 'application/pdf',
                'csv' => 'text/csv',
                default => 'application/octet-stream',
            };

            // Delete cache entry (one-time download)
            Cache::forget($downloadKey);

            // Return file download
            return response($content)
                ->header('Content-Type', $contentType)
                ->header('Content-Disposition', "attachment; filename=\"{$downloadInfo['filename']}\"");

        } catch (\Exception $e) {
            Log::error('Export download failed', [
                'error' => $e->getMessage(),
                'user_id' => auth()->id(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Download failed. Please try again.',
            ], 500);
        }
    }

    /**
     * Quick export endpoint for common formats
     * Accepts query execution results directly
     *
     * @param Request $request
     * @return mixed Export response
     */
    public function quickExport(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255',
                'format' => 'required|string|in:excel,pdf,csv',
                'data' => 'required|array',
                'columns' => 'required|array',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'errors' => $validator->errors(),
                ], 422);
            }

            $validated = $validator->validated();

            // Generate temporary report ID
            $reportId = time();

            $metadata = [
                'name' => $validated['name'],
                'generated_at' => now()->format('Y-m-d H:i:s'),
                'generated_by' => auth()->user()->name,
            ];

            return $this->exportService->export(
                $reportId,
                $validated['format'],
                $validated['data'],
                $validated['columns'],
                $metadata,
                false // Never queue quick exports
            );

        } catch (\Exception $e) {
            Log::error('Quick export failed', [
                'error' => $e->getMessage(),
                'user_id' => auth()->id(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Export failed. Please try again.',
            ], 500);
        }
    }

    /**
     * Check if user can access specific report
     * Override this method to implement your permission logic
     *
     * @param int $reportId
     * @return bool
     */
    protected function canAccessReport(int $reportId): bool
    {
        $user = auth()->user();

        // Multi-tenant check
        if (property_exists($user, 'school_id')) {
            // Add your report-to-school relationship check here
            // Example: return Report::where('id', $reportId)->where('school_id', $user->school_id)->exists();
        }

        // For now, allow all authenticated users
        // Customize this based on your permission system
        return true;
    }

    /**
     * Get export formats and their limits
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getExportOptions()
    {
        return response()->json([
            'success' => true,
            'data' => [
                'formats' => [
                    [
                        'value' => 'excel',
                        'label' => 'Excel (XLSX)',
                        'icon' => 'file-excel',
                        'max_rows' => null, // No limit
                        'supports_formatting' => true,
                    ],
                    [
                        'value' => 'pdf',
                        'label' => 'PDF',
                        'icon' => 'file-pdf',
                        'max_rows' => 2000,
                        'supports_formatting' => true,
                    ],
                    [
                        'value' => 'csv',
                        'label' => 'CSV',
                        'icon' => 'file-csv',
                        'max_rows' => null, // No limit
                        'supports_formatting' => false,
                    ],
                ],
                'async_threshold' => 500,
                'column_types' => [
                    'string', 'number', 'currency', 'percentage',
                    'date', 'datetime', 'boolean',
                ],
            ],
        ]);
    }

    /**
     * Cleanup old export files
     * Can be called via scheduled task or manually
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function cleanupExports(Request $request)
    {
        try {
            // Only allow admins to run cleanup
            if (!auth()->user()->hasRole('admin')) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized',
                ], 403);
            }

            $hoursOld = $request->input('hours_old', 24);
            $deletedCount = \App\Jobs\GenerateReportExportJob::cleanupOldExports($hoursOld);

            return response()->json([
                'success' => true,
                'message' => "Cleaned up {$deletedCount} old export file(s)",
                'deleted_count' => $deletedCount,
            ]);

        } catch (\Exception $e) {
            Log::error('Export cleanup failed', [
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Cleanup failed',
            ], 500);
        }
    }
}
