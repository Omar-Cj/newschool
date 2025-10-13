<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Repositories\Report\ReportRepository;
use App\Services\Report\ReportExecutionService;
use App\Services\Report\DependentParameterService;
use App\Services\ExportService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;

/**
 * ReportController
 *
 * Handles all report-related API endpoints including listing reports,
 * fetching parameters, executing reports, and exporting results
 */
class ReportController extends Controller
{
    /**
     * Constructor - inject dependencies
     *
     * @param ReportRepository $reportRepository
     * @param ReportExecutionService $executionService
     * @param DependentParameterService $dependentParameterService
     * @param ExportService $exportService
     */
    public function __construct(
        private ReportRepository $reportRepository,
        private ReportExecutionService $executionService,
        private DependentParameterService $dependentParameterService,
        private ExportService $exportService
    ) {
        // Apply authentication middleware based on route type
        // Web routes use 'auth', API routes use 'auth:sanctum'
    }

    /**
     * Display the Report Center web interface
     *
     * @return \Illuminate\View\View
     */
    public function indexWeb()
    {
        return view('reports.index');
    }

    /**
     * List all active reports grouped by category
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $module = $request->query('module');

            $reports = $this->reportRepository->getAllReportsGroupedByCategory($module);

            return response()->json([
                'success' => true,
                'message' => 'Reports retrieved successfully',
                'data' => [
                    'categories' => $reports
                ]
            ], 200);
        } catch (\Exception $e) {
            Log::error('Failed to fetch reports list', [
                'error' => $e->getMessage(),
                'user_id' => Auth::id()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch reports',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get detailed report information including parameters
     *
     * @param int $reportId
     * @return JsonResponse
     */
    public function show(int $reportId): JsonResponse
    {
        try {
            $report = $this->reportRepository->getReportById($reportId);

            if (!$report) {
                return response()->json([
                    'success' => false,
                    'message' => 'Report not found'
                ], 404);
            }

            // Check user permission - handle User model has role() (BelongsTo), not roles()
            $userRoles = Auth::user()->role ? [Auth::user()->role->name] : [];

            if (!$this->reportRepository->userCanAccessReport($report, $userRoles)) {
                return response()->json([
                    'success' => false,
                    'message' => 'You do not have permission to access this report'
                ], 403);
            }

            // Get parameter dependency tree
            $dependencyTree = $this->dependentParameterService->getParameterDependencyTree($reportId);

            return response()->json([
                'success' => true,
                'message' => 'Report details retrieved successfully',
                'data' => [
                    'report' => [
                        'id' => $report->id,
                        'name' => $report->name,
                        'description' => $report->description,
                        'module' => $report->module,
                        'category' => $report->category?->name,
                        'report_type' => $report->report_type,
                        'export_enabled' => $report->export_enabled === 1,
                    ],
                    'parameter_tree' => $dependencyTree
                ]
            ], 200);
        } catch (\Exception $e) {
            Log::error('Failed to fetch report details', [
                'report_id' => $reportId,
                'error' => $e->getMessage(),
                'user_id' => Auth::id()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch report details',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get parameters for a specific report with initial dropdown values
     *
     * @param int $reportId
     * @param Request $request
     * @return JsonResponse
     */
    public function getParameters(int $reportId, Request $request): JsonResponse
    {
        try {
            $report = $this->reportRepository->getReportById($reportId);

            if (!$report) {
                return response()->json([
                    'success' => false,
                    'message' => 'Report not found'
                ], 404);
            }

            // Get initial parameter values
            $initialValues = $request->query('initial_values', []);
            $parameters = $this->dependentParameterService->getInitialParameterValues(
                $reportId,
                $initialValues
            );

            return response()->json([
                'success' => true,
                'message' => 'Parameters retrieved successfully',
                'data' => [
                    'report' => [
                        'id' => $report->id,
                        'name' => $report->name,
                        'description' => $report->description,
                        'report_type' => $report->report_type,
                        'export_enabled' => $report->export_enabled === 1,
                    ],
                    'parameters' => $parameters
                ]
            ], 200);
        } catch (\Exception $e) {
            Log::error('Failed to fetch report parameters', [
                'report_id' => $reportId,
                'error' => $e->getMessage(),
                'user_id' => Auth::id()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch parameters',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get filtered values for dependent dropdowns
     *
     * @param int $parameterId
     * @param Request $request
     * @return JsonResponse
     */
    public function getDependentValues(int $parameterId, Request $request): JsonResponse
    {
        // Comprehensive logging for debugging
        Log::info('getDependentValues called', [
            'parameter_id' => $parameterId,
            'all_query_params' => $request->query(),
            'parent_value_from_query' => $request->query('parent_value'),
            'request_method' => $request->method(),
            'request_url' => $request->fullUrl(),
            'user_id' => Auth::id()
        ]);

        try {
            // Read from query parameter instead of body (frontend sends GET with query params)
            $parentValue = $request->query('parent_value');

            Log::info('Extracted parent value', [
                'parameter_id' => $parameterId,
                'parent_value' => $parentValue,
                'parent_value_type' => gettype($parentValue)
            ]);

            if (!$parentValue) {
                Log::warning('Parent value missing', [
                    'parameter_id' => $parameterId,
                    'all_query_params' => $request->query()
                ]);

                return response()->json([
                    'success' => false,
                    'message' => 'Parent value is required',
                    'values' => []
                ], 422);
            }

            Log::info('Calling resolveDependentValues', [
                'parameter_id' => $parameterId,
                'parent_value' => $parentValue
            ]);

            $values = $this->dependentParameterService->resolveDependentValues(
                $parameterId,
                $parentValue
            );

            Log::info('Dependent values resolved successfully', [
                'parameter_id' => $parameterId,
                'parent_value' => $parentValue,
                'values_count' => count($values),
                'sample_values' => array_slice($values, 0, 3)
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Dependent values retrieved successfully',
                'values' => $values
            ], 200);
        } catch (\Exception $e) {
            Log::error('Failed to load dependent values', [
                'parameter_id' => $parameterId,
                'parent_value' => $parentValue ?? null,
                'error' => $e->getMessage(),
                'error_class' => get_class($e),
                'trace' => $e->getTraceAsString(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to load options. Please try again.',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error',
                'values' => []
            ], 500);
        }
    }

    /**
     * Execute report with provided parameters
     *
     * @param int $reportId
     * @param Request $request
     * @return JsonResponse
     */
    public function execute(int $reportId, Request $request): JsonResponse
    {
        try {
            $report = $this->reportRepository->getReportById($reportId);

            if (!$report) {
                return response()->json([
                    'success' => false,
                    'message' => 'Report not found'
                ], 404);
            }

            // Check user permission - handle User model has role() (BelongsTo), not roles()
            $userRoles = Auth::user()->role ? [Auth::user()->role->name] : [];
            $this->executionService->checkUserPermission($report, $userRoles);

            // Get parameters from request
            $parameters = $request->input('parameters', []);

            // Execute report
            $result = $this->executionService->executeReport($reportId, $parameters);

            return response()->json([
                'success' => true,
                'message' => 'Report executed successfully',
                'data' => $result
            ], 200);
        } catch (\Exception $e) {
            Log::error('Failed to execute report', [
                'report_id' => $reportId,
                'error' => $e->getMessage(),
                'user_id' => Auth::id()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to execute report',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Export report results in specified format
     *
     * @param int $reportId
     * @param string $format excel|pdf|csv
     * @param Request $request
     * @return mixed
     */
    public function export(int $reportId, string $format, Request $request)
    {
        try {
            $report = $this->reportRepository->getReportById($reportId);

            if (!$report) {
                return response()->json([
                    'success' => false,
                    'message' => 'Report not found'
                ], 404);
            }

            // Check if export is enabled
            if ($report->export_enabled !== 1) {
                return response()->json([
                    'success' => false,
                    'message' => 'Export is not enabled for this report'
                ], 403);
            }

            // Check user permission - handle User model has role() (BelongsTo), not roles()
            $userRoles = Auth::user()->role ? [Auth::user()->role->name] : [];
            $this->executionService->checkUserPermission($report, $userRoles);

            // Validate format
            if (!in_array($format, ['excel', 'pdf', 'csv'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid export format. Allowed formats: excel, pdf, csv'
                ], 400);
            }

            // Get parameters and execute report
            $parameters = $request->input('parameters', []);
            $result = $this->executionService->executeReport($reportId, $parameters);

            // Generate filename
            $filename = $this->generateExportFilename($report->name, $format);

            // Export based on format
            switch ($format) {
                case 'excel':
                    return $this->exportToExcel($result, $filename);

                case 'pdf':
                    return $this->exportToPdf($result, $report, $filename);

                case 'csv':
                    return $this->exportToCsv($result, $filename);

                default:
                    return response()->json([
                        'success' => false,
                        'message' => 'Unsupported export format'
                    ], 400);
            }
        } catch (\Exception $e) {
            Log::error('Failed to export report', [
                'report_id' => $reportId,
                'format' => $format,
                'error' => $e->getMessage(),
                'user_id' => Auth::id()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to export report',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Generate export filename
     *
     * @param string $reportName
     * @param string $format
     * @return string
     */
    private function generateExportFilename(string $reportName, string $format): string
    {
        $sanitizedName = preg_replace('/[^A-Za-z0-9_\-]/', '_', $reportName);
        $timestamp = now()->format('Y-m-d_His');

        return "{$sanitizedName}_{$timestamp}.{$format}";
    }

    /**
     * Export to Excel format
     *
     * @param array $result
     * @param string $filename
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    private function exportToExcel(array $result, string $filename)
    {
        // Create a simple export class
        $export = new class($result) implements \Maatwebsite\Excel\Concerns\FromArray, \Maatwebsite\Excel\Concerns\WithHeadings {
            private $data;

            public function __construct($result)
            {
                $this->data = $result['data'] ?? [];
            }

            public function array(): array
            {
                if (isset($this->data['rows'])) {
                    return $this->data['rows'];
                }

                return is_array($this->data) ? $this->data : [];
            }

            public function headings(): array
            {
                if (isset($this->data['columns'])) {
                    return array_column($this->data['columns'], 'label');
                }

                if (!empty($this->data) && is_array($this->data)) {
                    $firstRow = is_array($this->data) ? reset($this->data) : [];
                    return array_keys($firstRow);
                }

                return [];
            }
        };

        return Excel::download($export, $filename);
    }

    /**
     * Export to PDF format using ExportService
     *
     * This method delegates to ExportService which:
     * - Resolves student name from p_student_id parameter
     * - Uses correct template (reports.pdf.template)
     * - Includes summary data for exam gradebooks
     * - Formats data properly for display
     *
     * @param array $result Report execution result from ReportExecutionService
     * @param \App\Models\ReportCenter $report Report model instance
     * @param string $filename Generated filename for download
     * @return \Illuminate\Http\Response
     */
    private function exportToPdf(array $result, $report, string $filename)
    {
        // Extract data components from result structure
        // Result structure: ['success' => true, 'report' => [...], 'data' => [...], 'meta' => [...]]
        $data = $result['data'] ?? [];
        $rows = $data['rows'] ?? [];
        $columns = $data['columns'] ?? [];

        // Extract parameters from meta for student name resolution
        $parameters = $result['meta']['parameters_used'] ?? [];

        // Build metadata structure expected by ExportService
        $metadata = [
            'name' => $report->name,
            'parameters' => $parameters,
            'procedure_name' => $report->procedure_name, // Add procedure name for conditional rendering
        ];

        // Include summary data if available (for exam gradebooks)
        if (isset($data['summary'])) {
            $metadata['summary'] = $data['summary'];

            Log::debug('PDF export with summary', [
                'report_name' => $report->name,
                'procedure_name' => $report->procedure_name,
                'has_summary' => true,
                'summary_row_count' => count($data['summary']['rows'] ?? []),
                'student_id' => $parameters['p_student_id'] ?? 'not set',
            ]);
        }

        // Use ExportService for consistent PDF generation with student name resolution
        return $this->exportService->exportPdf(
            $report->id,
            $rows,
            $columns,
            $metadata
        );
    }

    /**
     * Export to CSV format
     *
     * @param array $result
     * @param string $filename
     * @return \Symfony\Component\HttpFoundation\StreamedResponse
     */
    private function exportToCsv(array $result, string $filename)
    {
        $data = $result['data'] ?? [];
        $rows = $data['rows'] ?? $data;
        $columns = $data['columns'] ?? [];

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function() use ($rows, $columns) {
            $file = fopen('php://output', 'w');

            // Write headers
            if (!empty($columns)) {
                fputcsv($file, array_column($columns, 'label'));
            } elseif (!empty($rows) && is_array($rows)) {
                $firstRow = reset($rows);
                fputcsv($file, array_keys($firstRow));
            }

            // Write data rows
            foreach ($rows as $row) {
                fputcsv($file, is_array($row) ? $row : (array) $row);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Get report execution statistics
     *
     * @param int $reportId
     * @param Request $request
     * @return JsonResponse
     */
    public function statistics(int $reportId, Request $request): JsonResponse
    {
        try {
            $days = $request->query('days', 30);

            $statistics = $this->executionService->getReportStatistics($reportId, $days);

            return response()->json([
                'success' => true,
                'message' => 'Statistics retrieved successfully',
                'data' => $statistics
            ], 200);
        } catch (\Exception $e) {
            Log::error('Failed to fetch report statistics', [
                'report_id' => $reportId,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch statistics',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get all report categories
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function categories(Request $request): JsonResponse
    {
        try {
            $module = $request->query('module');

            $categories = $this->reportRepository->getAllCategories($module);

            return response()->json([
                'success' => true,
                'message' => 'Categories retrieved successfully',
                'data' => [
                    'categories' => $categories
                ]
            ], 200);
        } catch (\Exception $e) {
            Log::error('Failed to fetch report categories', [
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch categories',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
