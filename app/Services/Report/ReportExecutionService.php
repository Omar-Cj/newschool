<?php

declare(strict_types=1);

namespace App\Services\Report;

use App\Models\ReportCenter;
use App\Repositories\Report\ReportRepository;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

/**
 * ReportExecutionService
 *
 * Handles the execution of reports including parameter validation,
 * stored procedure invocation, and result transformation
 */
class ReportExecutionService
{
    /**
     * Constructor
     *
     * @param ReportRepository $reportRepository
     */
    public function __construct(
        private ReportRepository $reportRepository
    ) {}

    /**
     * Execute a report with provided parameters
     *
     * @param int $reportId
     * @param array $parameters User-provided parameter values
     * @return array Result containing data, metadata, and execution info
     * @throws \Exception If validation fails or execution errors occur
     */
    public function executeReport(int $reportId, array $parameters = []): array
    {
        $startTime = microtime(true);

        // Get report definition
        $report = $this->reportRepository->getReportById($reportId);

        if (!$report) {
            throw new \Exception("Report not found with ID: {$reportId}");
        }

        // Check if report is active
        if ($report->status !== 1) {
            throw new \Exception("Report is not active and cannot be executed");
        }

        // Validate parameters
        $validationErrors = $this->validateParameters($parameters, $reportId);

        if (!empty($validationErrors)) {
            throw new \Exception('Parameter validation failed: ' . json_encode($validationErrors));
        }

        // Prepare parameters for stored procedure
        $preparedParameters = $this->prepareParameters($report, $parameters);

        // Execute stored procedure
        try {
            $results = $this->executeStoredProcedure(
                $report->procedure_name,
                $preparedParameters
            );

            $executionTime = round((microtime(true) - $startTime) * 1000, 2);

            // Transform results based on report type
            $transformedResults = $this->transformResults($results, $report->report_type);

            // Add exam gradebook summary if applicable
            $transformedResults = $this->addExamGradebookSummary(
                $transformedResults,
                $report->procedure_name
            );

            // Add paid students summary if applicable
            $transformedResults = $this->addPaidStudentsSummary(
                $transformedResults,
                $report->procedure_name
            );

            Log::info('Report executed successfully', [
                'report_id' => $reportId,
                'report_name' => $report->name,
                'user_id' => Auth::id(),
                'execution_time_ms' => $executionTime,
                'result_count' => $results->count()
            ]);

            return [
                'success' => true,
                'report' => [
                    'id' => $report->id,
                    'name' => $report->name,
                    'description' => $report->description,
                    'type' => $report->report_type,
                ],
                'data' => $transformedResults,
                'meta' => [
                    'total_records' => $results->count(),
                    'execution_time_ms' => $executionTime,
                    'executed_at' => now()->toIso8601String(),
                    'executed_by' => Auth::id(),
                    'parameters_used' => $this->sanitizeParametersForLog($parameters),
                ],
            ];
        } catch (\Exception $e) {
            Log::error('Report execution failed', [
                'report_id' => $reportId,
                'report_name' => $report->name,
                'user_id' => Auth::id(),
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            throw new \Exception("Failed to execute report: {$e->getMessage()}");
        }
    }

    /**
     * Validate parameters against report definition
     *
     * @param array $parameters
     * @param int $reportId
     * @return array Validation errors
     */
    public function validateParameters(array $parameters, int $reportId): array
    {
        return $this->reportRepository->validateReportParameters($reportId, $parameters);
    }

    /**
     * Prepare parameters for stored procedure execution
     * Converts parameters to the correct order and format
     *
     * @param ReportCenter $report
     * @param array $inputParameters
     * @return array Ordered array of parameter values
     */
    private function prepareParameters(ReportCenter $report, array $inputParameters): array
    {
        $reportParameters = $this->reportRepository->getReportParameters($report->id);
        $preparedParameters = [];

        foreach ($reportParameters as $param) {
            $value = $inputParameters[$param->name] ?? $param->default_value ?? null;

            // Convert empty strings to null for optional parameters
            if ($value === '' && !$param->isRequired()) {
                $value = null;
            }

            // Handle multiselect - convert array to comma-separated string
            if ($param->type === 'multiselect' && is_array($value)) {
                $value = implode(',', $value);
            }

            // Type casting
            $value = $this->castParameterValue($value, $param->type);

            $preparedParameters[] = $value;
        }

        return $preparedParameters;
    }

    /**
     * Cast parameter value to appropriate type
     *
     * @param mixed $value
     * @param string $type
     * @return mixed
     */
    private function castParameterValue($value, string $type)
    {
        if ($value === null) {
            return null;
        }

        switch ($type) {
            case 'number':
                return is_numeric($value) ? (float) $value : null;
            case 'date':
            case 'datetime':
                return $value;
            case 'checkbox':
                return (bool) $value ? 1 : 0;
            default:
                return (string) $value;
        }
    }

    /**
     * Execute stored procedure with parameter binding
     *
     * @param string $procedureName
     * @param array $parameters
     * @return Collection
     */
    public function executeStoredProcedure(string $procedureName, array $parameters): Collection
    {
        return $this->reportRepository->executeStoredProcedure($procedureName, $parameters);
    }

    /**
     * Transform results based on report type
     *
     * @param Collection $results
     * @param string $reportType
     * @return array
     */
    public function transformResults(Collection $results, string $reportType): array
    {
        switch ($reportType) {
            case 'tabular':
                return $this->transformTabularResults($results);

            case 'summary':
                return $this->transformSummaryResults($results);

            case 'chart':
                return $this->transformChartResults($results);

            case 'custom':
            default:
                return $results->toArray();
        }
    }

    /**
     * Transform results for tabular display
     *
     * @param Collection $results
     * @return array
     */
    private function transformTabularResults(Collection $results): array
    {
        if ($results->isEmpty()) {
            return [
                'columns' => [],
                'rows' => []
            ];
        }

        // Extract columns from first result
        $firstRow = $results->first();
        $columns = array_keys($firstRow);

        return [
            'columns' => array_map(function ($col) {
                return [
                    'field' => $col,
                    'label' => ucwords(str_replace('_', ' ', $col)),
                    'sortable' => true,
                ];
            }, $columns),
            'rows' => $results->toArray()
        ];
    }

    /**
     * Transform results for summary display
     *
     * @param Collection $results
     * @return array
     */
    private function transformSummaryResults(Collection $results): array
    {
        if ($results->isEmpty()) {
            return [];
        }

        // Assume first row contains summary metrics
        $summary = $results->first();

        return array_map(function ($key, $value) {
            return [
                'metric' => ucwords(str_replace('_', ' ', $key)),
                'value' => $value,
                'formatted' => $this->formatSummaryValue($value)
            ];
        }, array_keys($summary), $summary);
    }

    /**
     * Transform results for chart display
     *
     * @param Collection $results
     * @return array
     */
    private function transformChartResults(Collection $results): array
    {
        if ($results->isEmpty()) {
            return [
                'labels' => [],
                'datasets' => []
            ];
        }

        $firstRow = $results->first();
        $keys = array_keys($firstRow);

        // Assume first column is label, rest are data series
        $labelKey = $keys[0];
        $dataKeys = array_slice($keys, 1);

        $labels = $results->pluck($labelKey)->toArray();

        $datasets = [];
        foreach ($dataKeys as $dataKey) {
            $datasets[] = [
                'label' => ucwords(str_replace('_', ' ', $dataKey)),
                'data' => $results->pluck($dataKey)->toArray()
            ];
        }

        return [
            'labels' => $labels,
            'datasets' => $datasets
        ];
    }

    /**
     * Format summary value for display
     *
     * @param mixed $value
     * @return string
     */
    private function formatSummaryValue($value): string
    {
        if (is_numeric($value)) {
            // Format numbers with commas
            if (is_float($value) || strpos((string)$value, '.') !== false) {
                return number_format((float) $value, 2);
            }
            return number_format((int) $value);
        }

        return (string) $value;
    }

    /**
     * Sanitize parameters for logging (remove sensitive data)
     *
     * @param array $parameters
     * @return array
     */
    private function sanitizeParametersForLog(array $parameters): array
    {
        $sanitized = [];
        $sensitiveKeys = ['password', 'token', 'secret', 'api_key'];

        foreach ($parameters as $key => $value) {
            if (in_array(strtolower($key), $sensitiveKeys)) {
                $sanitized[$key] = '***REDACTED***';
            } else {
                $sanitized[$key] = $value;
            }
        }

        return $sanitized;
    }

    /**
     * Get report execution statistics
     *
     * @param int $reportId
     * @param int $days Number of days to look back
     * @return array
     */
    public function getReportStatistics(int $reportId, int $days = 30): array
    {
        // This would typically query a report_executions log table
        // For now, return placeholder structure
        return [
            'report_id' => $reportId,
            'period_days' => $days,
            'total_executions' => 0,
            'avg_execution_time_ms' => 0,
            'last_executed_at' => null,
            'most_used_parameters' => []
        ];
    }

    /**
     * Validate that user has permission to execute report
     *
     * @param ReportCenter $report
     * @param array $userRoles
     * @return bool
     * @throws \Exception If user doesn't have permission
     */
    public function checkUserPermission(ReportCenter $report, array $userRoles): bool
    {
        if (!$this->reportRepository->userCanAccessReport($report, $userRoles)) {
            throw new \Exception('You do not have permission to access this report');
        }

        return true;
    }

    /**
     * Add exam gradebook summary calculations
     *
     * Calculates per-column totals and grand total for GetStudentGradebook procedure
     * Only applies to gradebook report type
     *
     * @param array $data Transformed report data with columns and rows
     * @param string $procedureName Stored procedure name
     * @return array Enhanced data with summary metadata
     */
    private function addExamGradebookSummary(array $data, string $procedureName): array
    {
        // Only apply summary to GetStudentGradebook procedure
        if ($procedureName !== 'GetStudentGradebook') {
            return $data;
        }

        // Ensure data structure contains rows and columns
        if (!isset($data['rows']) || !isset($data['columns']) || empty($data['rows'])) {
            return $data;
        }

        try {
            $rows = $data['rows'];
            $columns = $data['columns'];

            // Initialize totals array
            $totals = [];
            $columnLabels = [];

            // Identify numeric columns (all except first column "Subject Name")
            $numericColumns = [];
            foreach ($columns as $index => $column) {
                // Skip first column (Subject Name)
                if ($index === 0) {
                    continue;
                }

                $field = $column['field'] ?? null;
                if ($field) {
                    $numericColumns[] = $field;
                    $columnLabels[] = $column['label'] ?? $field;
                    $totals[$field] = 0;
                }
            }

            // Calculate sum for each numeric column
            foreach ($rows as $row) {
                foreach ($numericColumns as $field) {
                    $value = $row[$field] ?? 0;

                    // Convert to numeric, handling null and non-numeric values
                    if (is_numeric($value)) {
                        $totals[$field] += (float) $value;
                    }
                }
            }

            // Calculate grand total (sum of all column totals)
            $grandTotal = array_sum($totals);

            // Restructure summary data to row-based format for easier PDF rendering
            $summaryRows = [];
            foreach ($columnLabels as $index => $label) {
                // Clean exam name - remove any " Total" suffix if present to avoid duplicates
                $cleanLabel = preg_replace('/\s+Total$/i', '', trim($label));

                $summaryRows[] = [
                    'exam_name' => $cleanLabel,
                    'total_marks' => $totals[$numericColumns[$index]] ?? 0
                ];
            }

            // Add "Total All Exams" as final row instead of separate grandTotal field
            $summaryRows[] = [
                'exam_name' => 'Total All Exams',
                'total_marks' => $grandTotal
            ];

            // Add summary to data structure in new format (no separate grandTotal)
            $data['summary'] = [
                'rows' => $summaryRows,
            ];

            Log::debug('Gradebook summary calculated', [
                'procedure' => $procedureName,
                'total_columns' => count($numericColumns),
                'grand_total' => $grandTotal,
                'summary_rows' => count($summaryRows),
                'includes_total_row' => true,
            ]);

        } catch (\Exception $e) {
            // Log error but don't fail the report
            Log::warning('Failed to calculate gradebook summary', [
                'procedure' => $procedureName,
                'error' => $e->getMessage(),
            ]);
        }

        return $data;
    }

    /**
     * Add paid students financial summary calculations
     *
     * Calculates financial totals: paid amount, deposit used, discount, and grand total
     * Only applies to GetPaidStudentsReport procedure
     *
     * @param array $data Transformed report data with columns and rows
     * @param string $procedureName Stored procedure name
     * @return array Enhanced data with financial summary
     */
    private function addPaidStudentsSummary(array $data, string $procedureName): array
    {
        // Only apply summary to GetPaidStudentsReport procedure
        if ($procedureName !== 'GetPaidStudentsReport') {
            return $data;
        }

        // Ensure data structure contains rows
        if (!isset($data['rows']) || empty($data['rows'])) {
            return $data;
        }

        try {
            $rows = $data['rows'];

            // Initialize financial totals
            $paidAmount = 0;
            $depositUsed = 0;
            $discount = 0;

            // Sum financial columns from all rows
            foreach ($rows as $row) {
                // Add paid amount (check multiple possible column names)
                $paidAmount += (float) ($row['paid_amount'] ?? $row['amount_paid'] ?? 0);

                // Add deposit used
                $depositUsed += (float) ($row['deposit_used'] ?? $row['deposit'] ?? 0);

                // Add discount
                $discount += (float) ($row['discount'] ?? $row['discount_amount'] ?? 0);
            }

            // Calculate grand total (paid amount + deposit used)
            $grandTotal = $paidAmount + $depositUsed;

            // Create summary rows (similar to gradebook structure)
            $summaryRows = [
                [
                    'metric' => 'Paid Amount',
                    'value' => $paidAmount
                ],
                [
                    'metric' => 'Deposit Used',
                    'value' => $depositUsed
                ],
                [
                    'metric' => 'Discount',
                    'value' => $discount
                ],
                [
                    'metric' => 'Grand Total',
                    'value' => $grandTotal
                ]
            ];

            // Add summary to data structure
            $data['summary'] = [
                'rows' => $summaryRows,
                'type' => 'financial' // Distinguish from gradebook summary
            ];

            Log::debug('Paid students summary calculated', [
                'procedure' => $procedureName,
                'paid_amount' => $paidAmount,
                'deposit_used' => $depositUsed,
                'discount' => $discount,
                'grand_total' => $grandTotal,
            ]);

        } catch (\Exception $e) {
            // Log error but don't fail the report
            Log::warning('Failed to calculate paid students summary', [
                'procedure' => $procedureName,
                'error' => $e->getMessage(),
            ]);
        }

        return $data;
    }
}
