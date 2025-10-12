<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\ReportCenter;
use App\Models\ReportParameter;
use App\Models\ReportCategory;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Repositories\ParameterValueResolver;

/**
 * ReportRepository
 *
 * Repository for managing report data access and operations.
 * Provides methods for fetching reports, parameters, and executing report queries.
 */
class ReportRepository
{
    /**
     * Parameter value resolver instance.
     *
     * @var ParameterValueResolver
     */
    private ParameterValueResolver $valueResolver;

    /**
     * Create a new repository instance.
     *
     * @param ParameterValueResolver $valueResolver
     */
    public function __construct(ParameterValueResolver $valueResolver)
    {
        $this->valueResolver = $valueResolver;
    }

    /**
     * Get all active reports grouped by category.
     *
     * @param string|null $module Filter by module
     * @return Collection Collection of categories with their reports
     */
    public function getAllReportsGroupedByCategory(?string $module = null): Collection
    {
        $query = ReportCategory::with(['reports' => function ($q) use ($module) {
            $q->active()->with('parameters');

            if (!is_null($module)) {
                $q->where('module', $module);
            }

            $q->orderBy('name');
        }])
        ->ordered()
        ->whereHas('reports', function ($q) use ($module) {
            $q->active();

            if (!is_null($module)) {
                $q->where('module', $module);
            }
        });

        return $query->get();
    }

    /**
     * Get all active reports.
     *
     * @return Collection
     */
    public function getAllReports(): Collection
    {
        return ReportCenter::active()
            ->with(['category', 'parameters'])
            ->orderBy('name')
            ->get();
    }

    /**
     * Get a single report by ID with its parameters.
     *
     * @param int $reportId
     * @return ReportCenter|null
     */
    public function getReportById(int $reportId): ?ReportCenter
    {
        return ReportCenter::with(['category', 'parameters' => function ($query) {
            $query->ordered();
        }])
        ->find($reportId);
    }

    /**
     * Get report with parameters and resolved dropdown values.
     *
     * @param int $reportId
     * @return ReportCenter|null
     */
    public function getReportWithParameters(int $reportId): ?ReportCenter
    {
        $report = $this->getReportById($reportId);

        if (!$report) {
            return null;
        }

        // Resolve dropdown values for all parameters
        foreach ($report->parameters as $parameter) {
            if ($parameter->isDropdown()) {
                $parameter->dropdown_values = $this->resolveParameterDropdownValues(
                    $parameter->id,
                    null
                );
            }
        }

        return $report;
    }

    /**
     * Get ordered parameters for a report.
     *
     * @param int $reportId
     * @return Collection
     */
    public function getReportParameters(int $reportId): Collection
    {
        return ReportParameter::where('report_id', $reportId)
            ->ordered()
            ->get();
    }

    /**
     * Get a single parameter by ID.
     *
     * @param int $parameterId
     * @return ReportParameter|null
     */
    public function getParameterById(int $parameterId): ?ReportParameter
    {
        return ReportParameter::with(['report', 'parent'])->find($parameterId);
    }

    /**
     * Get dropdown values for a parameter (with optional parent value for cascading).
     *
     * @param int $parameterId
     * @param string|null $parentValue
     * @return array
     */
    public function resolveParameterDropdownValues(int $parameterId, ?string $parentValue = null): array
    {
        $parameter = $this->getParameterById($parameterId);

        if (!$parameter || !$parameter->isDropdown()) {
            return [];
        }

        try {
            return $this->valueResolver->resolve($parameter, $parentValue);
        } catch (\Exception $e) {
            Log::error('Failed to resolve parameter dropdown values', [
                'parameter_id' => $parameterId,
                'parent_value' => $parentValue,
                'error' => $e->getMessage(),
            ]);

            return [];
        }
    }

    /**
     * Get reports filtered by module.
     *
     * @param string $module
     * @return Collection
     */
    public function getReportsByModule(string $module): Collection
    {
        return ReportCenter::active()
            ->byModule($module)
            ->with(['category', 'parameters'])
            ->orderBy('name')
            ->get();
    }

    /**
     * Get reports filtered by category.
     *
     * @param int $categoryId
     * @return Collection
     */
    public function getReportsByCategory(int $categoryId): Collection
    {
        return ReportCenter::active()
            ->byCategory($categoryId)
            ->with(['category', 'parameters'])
            ->orderBy('name')
            ->get();
    }

    /**
     * Get reports filtered by type.
     *
     * @param string $reportType
     * @return Collection
     */
    public function getReportsByType(string $reportType): Collection
    {
        return ReportCenter::active()
            ->byType($reportType)
            ->with(['category', 'parameters'])
            ->orderBy('name')
            ->get();
    }

    /**
     * Get all report categories.
     *
     * @param string|null $module Filter by module
     * @return Collection
     */
    public function getAllCategories(?string $module = null): Collection
    {
        $query = ReportCategory::ordered();

        if (!is_null($module)) {
            $query->byModule($module);
        }

        return $query->get();
    }

    /**
     * Get category by ID.
     *
     * @param int $categoryId
     * @return ReportCategory|null
     */
    public function getCategoryById(int $categoryId): ?ReportCategory
    {
        return ReportCategory::find($categoryId);
    }

    /**
     * Execute a report with provided parameters.
     *
     * @param int $reportId
     * @param array $parameters Key-value pairs of parameter names and values
     * @return array Report execution results
     * @throws \Exception
     */
    public function executeReport(int $reportId, array $parameters = []): array
    {
        $report = $this->getReportById($reportId);

        if (!$report) {
            throw new \Exception("Report with ID {$reportId} not found");
        }

        if (!$report->isActive()) {
            throw new \Exception("Report '{$report->name}' is not active");
        }

        // Validate required parameters
        $this->validateRequiredParameters($report, $parameters);

        // Execute stored procedure
        try {
            $results = $this->executeStoredProcedure(
                $report->procedure_name,
                $report->parameters,
                $parameters
            );

            return [
                'success' => true,
                'report' => [
                    'id' => $report->id,
                    'name' => $report->name,
                    'description' => $report->description,
                    'type' => $report->report_type,
                ],
                'parameters' => $parameters,
                'data' => $results,
                'executed_at' => now()->toISOString(),
            ];
        } catch (\Exception $e) {
            Log::error('Report execution failed', [
                'report_id' => $reportId,
                'report_name' => $report->name,
                'procedure' => $report->procedure_name,
                'parameters' => $parameters,
                'error' => $e->getMessage(),
            ]);

            throw new \Exception('Failed to execute report: ' . $e->getMessage());
        }
    }

    /**
     * Execute a stored procedure with parameters.
     *
     * @param string $procedureName
     * @param Collection $parameterDefinitions
     * @param array $parameterValues
     * @return array
     */
    private function executeStoredProcedure(
        string $procedureName,
        Collection $parameterDefinitions,
        array $parameterValues
    ): array {
        // Build parameter list for procedure call
        $orderedParams = $parameterDefinitions->ordered();
        $paramList = [];

        foreach ($orderedParams as $param) {
            $value = $parameterValues[$param->name] ?? $param->default_value;

            // Format value based on type
            $formattedValue = $this->formatParameterValue($value, $param->value_type);
            $paramList[] = $formattedValue;
        }

        // Build CALL statement
        $paramString = implode(', ', $paramList);
        $callStatement = "CALL {$procedureName}({$paramString})";

        Log::info('Executing stored procedure', [
            'procedure' => $procedureName,
            'statement' => $callStatement,
        ]);

        // Execute procedure
        $results = DB::select($callStatement);

        return $this->formatResults($results);
    }

    /**
     * Format parameter value for stored procedure call.
     *
     * @param mixed $value
     * @param string|null $valueType
     * @return string
     */
    private function formatParameterValue($value, ?string $valueType): string
    {
        if (is_null($value)) {
            return 'NULL';
        }

        if (in_array($valueType, ['int', 'integer', 'number'])) {
            return (string) intval($value);
        }

        if (in_array($valueType, ['float', 'decimal'])) {
            return (string) floatval($value);
        }

        if (in_array($valueType, ['date', 'datetime', 'string', 'text'])) {
            return DB::connection()->getPdo()->quote($value);
        }

        // Default: treat as string
        return DB::connection()->getPdo()->quote($value);
    }

    /**
     * Format query results to array.
     *
     * @param array $results
     * @return array
     */
    private function formatResults(array $results): array
    {
        return array_map(function ($row) {
            return (array) $row;
        }, $results);
    }

    /**
     * Validate that all required parameters are provided.
     *
     * @param ReportCenter $report
     * @param array $parameters
     * @return void
     * @throws \Exception
     */
    private function validateRequiredParameters(ReportCenter $report, array $parameters): void
    {
        $requiredParams = $report->getRequiredParameters();
        $missingParams = [];

        foreach ($requiredParams as $param) {
            if (!isset($parameters[$param->name]) || $parameters[$param->name] === '') {
                $missingParams[] = $param->label;
            }
        }

        if (!empty($missingParams)) {
            throw new \Exception(
                'Missing required parameters: ' . implode(', ', $missingParams)
            );
        }
    }

    /**
     * Get report execution statistics.
     *
     * @param int $reportId
     * @return array
     */
    public function getReportStats(int $reportId): array
    {
        $report = $this->getReportById($reportId);

        if (!$report) {
            return [];
        }

        return [
            'id' => $report->id,
            'name' => $report->name,
            'module' => $report->module,
            'category' => $report->category ? $report->category->name : null,
            'status' => $report->isActive() ? 'active' : 'inactive',
            'total_parameters' => $report->parameters->count(),
            'required_parameters' => $report->getRequiredParameters()->count(),
            'optional_parameters' => $report->getOptionalParameters()->count(),
            'export_enabled' => $report->isExportEnabled(),
            'created_at' => $report->created_at->toISOString(),
            'updated_at' => $report->updated_at->toISOString(),
        ];
    }

    /**
     * Search reports by name or description.
     *
     * @param string $searchTerm
     * @return Collection
     */
    public function searchReports(string $searchTerm): Collection
    {
        return ReportCenter::active()
            ->where(function ($query) use ($searchTerm) {
                $query->where('name', 'LIKE', "%{$searchTerm}%")
                      ->orWhere('description', 'LIKE', "%{$searchTerm}%");
            })
            ->with(['category', 'parameters'])
            ->orderBy('name')
            ->get();
    }

    /**
     * Check if user can access report based on role.
     *
     * @param int $reportId
     * @param string $userRole
     * @return bool
     */
    public function canAccessReport(int $reportId, string $userRole): bool
    {
        $report = $this->getReportById($reportId);

        if (!$report) {
            return false;
        }

        return $report->canAccessByRole($userRole);
    }

    /**
     * Get reports accessible by specific role.
     *
     * @param string $role
     * @return Collection
     */
    public function getReportsByRole(string $role): Collection
    {
        return ReportCenter::active()
            ->where(function ($query) use ($role) {
                $query->whereJsonContains('roles', $role)
                      ->orWhereNull('roles')
                      ->orWhere('roles', '[]');
            })
            ->with(['category', 'parameters'])
            ->orderBy('name')
            ->get();
    }
}
