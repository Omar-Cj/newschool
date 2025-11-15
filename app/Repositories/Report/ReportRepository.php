<?php

declare(strict_types=1);

namespace App\Repositories\Report;

use App\Models\ReportCenter;
use App\Models\ReportParameter;
use App\Models\ReportCategory;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

/**
 * ReportRepository
 *
 * Handles all database operations for the report center system including
 * fetching report metadata, parameters, and executing dynamic queries
 */
class ReportRepository
{
    /**
     * Get all active reports grouped by category
     *
     * @param string|null $module Filter by specific module
     * @return Collection Collection of categories with their reports
     */
    public function getAllReportsGroupedByCategory(?string $module = null): Collection
    {
        $query = ReportCenter::with(['category', 'parameters'])
            ->active()
            ->orderBy('display_order');

        if ($module) {
            $query->where('module', $module);
        }

        $reports = $query->get();

        // Group by category
        return $reports->groupBy(function ($report) {
            return $report->category_id ?? 0;
        })->map(function ($categoryReports, $categoryId) {
            $category = $categoryId > 0
                ? ReportCategory::find($categoryId)
                : (object)[
                    'id' => 0,
                    'name' => 'Uncategorized',
                    'icon' => 'folder',
                    'display_order' => 999
                ];

            return [
                'id' => $category->id,
                'name' => $category->name,
                'icon' => $category->icon ?? 'folder',
                'module' => $category->module ?? null,
                'display_order' => $category->display_order ?? 999,
                'reports' => $categoryReports->map(function ($report) {
                    return [
                        'id' => $report->id,
                        'name' => $report->name,
                        'description' => $report->description,
                        'report_type' => $report->report_type,
                        'export_enabled' => $report->export_enabled === 1,
                        'parameter_count' => $report->parameters->count(),
                    ];
                })->values()
            ];
        })->sortBy(function ($item) {
            return $item['display_order'] ?? 999;
        })->values();
    }

    /**
     * Get report by ID with full details
     *
     * @param int $reportId
     * @return ReportCenter|null
     */
    public function getReportById(int $reportId): ?ReportCenter
    {
        return ReportCenter::with(['category', 'parameters' => function ($query) {
            $query->ordered();
        }])->find($reportId);
    }

    /**
     * Get all parameters for a specific report
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
     * Get root level parameters (no parent dependency)
     *
     * @param int $reportId
     * @return Collection
     */
    public function getRootParameters(int $reportId): Collection
    {
        return ReportParameter::where('report_id', $reportId)
            ->whereNull('parent_id')
            ->ordered()
            ->get();
    }

    /**
     * Get child parameters that depend on a parent parameter
     *
     * @param int $parentParameterId
     * @return Collection
     */
    public function getChildParameters(int $parentParameterId): Collection
    {
        return ReportParameter::where('parent_id', $parentParameterId)
            ->ordered()
            ->get();
    }

    /**
     * Get parameter by ID
     *
     * @param int $parameterId
     * @return ReportParameter|null
     */
    public function getParameterById(int $parameterId): ?ReportParameter
    {
        return ReportParameter::find($parameterId);
    }

    /**
     * Execute a dynamic query for dropdown values
     *
     * @param string $query SQL query string
     * @param array $bindings Parameter bindings for the query
     * @return Collection Results from the query
     */
    public function executeDynamicQuery(string $query, array $bindings = []): Collection
    {
        try {
            $results = DB::select($query, $bindings);
            return collect($results)->map(function ($item) {
                return (array) $item;
            });
        } catch (\Exception $e) {
            Log::error('Failed to execute dynamic query for parameter values', [
                'query' => $query,
                'bindings' => $bindings,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * Get dropdown values for a parameter
     * Handles both static JSON arrays and dynamic queries
     *
     * @param ReportParameter $parameter
     * @param array $parentValues Parent parameter values for dependent dropdowns
     * @return array
     */
    public function getParameterValues(ReportParameter $parameter, array $parentValues = []): array
    {
        Log::info('getParameterValues called', [
            'parameter_id' => $parameter->id,
            'parameter_name' => $parameter->name,
            'parameter_type' => $parameter->type,
            'has_static_values' => $parameter->hasStaticValues(),
            'has_dynamic_query' => $parameter->hasDynamicQuery(),
            'parent_values' => $parentValues,
            'values_field_content' => substr($parameter->values ?? '', 0, 200)
        ]);

        // Return static values if available
        if ($parameter->hasStaticValues()) {
            Log::info('Returning static values', [
                'parameter_id' => $parameter->id
            ]);
            $staticValues = $parameter->getParsedValues();
            Log::info('Static values returned', [
                'parameter_id' => $parameter->id,
                'values_count' => count($staticValues),
                'sample_values' => array_slice($staticValues, 0, 3)
            ]);
            return $staticValues;
        }

        // Execute dynamic query
        if ($parameter->hasDynamicQuery()) {
            $query = $parameter->getQueryString();

            Log::info('Processing dynamic query', [
                'parameter_id' => $parameter->id,
                'query' => $query,
                'parent_values' => $parentValues
            ]);

            if (!$query) {
                Log::warning('Query string is empty', [
                    'parameter_id' => $parameter->id
                ]);
                return [];
            }

            // Replace parameter placeholders with actual values
            $bindings = $this->prepareQueryBindings($query, $parentValues);

            Log::info('Query bindings prepared', [
                'parameter_id' => $parameter->id,
                'bindings' => $bindings,
                'original_query' => $query
            ]);

            $processedQuery = $this->replacePlaceholdersWithPositional($query, $bindings);

            Log::info('Query after placeholder replacement', [
                'parameter_id' => $parameter->id,
                'processed_query' => $processedQuery,
                'binding_values' => $bindings
            ]);

            try {
                $results = $this->executeDynamicQuery($processedQuery, $bindings);

                Log::info('Query executed successfully', [
                    'parameter_id' => $parameter->id,
                    'results_count' => $results->count(),
                    'raw_results_sample' => $results->take(3)->toArray()
                ]);

                // Transform to value/label format
                $transformed = $results->map(function ($row) {
                    // Assume first column is value, second is label
                    $values = array_values($row);
                    return [
                        'value' => $values[0] ?? null,
                        'label' => $values[1] ?? $values[0] ?? 'Unknown'
                    ];
                })->toArray();

                Log::info('Results transformed', [
                    'parameter_id' => $parameter->id,
                    'transformed_count' => count($transformed),
                    'sample_transformed' => array_slice($transformed, 0, 3)
                ]);

                return $transformed;
            } catch (\Exception $e) {
                Log::error('Failed to fetch parameter values', [
                    'parameter_id' => $parameter->id,
                    'parameter_name' => $parameter->name,
                    'query' => $processedQuery,
                    'bindings' => array_values($bindings),
                    'error' => $e->getMessage(),
                    'error_class' => get_class($e),
                    'trace' => $e->getTraceAsString()
                ]);
                return [];
            }
        }

        Log::warning('No values to return', [
            'parameter_id' => $parameter->id,
            'has_static_values' => $parameter->hasStaticValues(),
            'has_dynamic_query' => $parameter->hasDynamicQuery()
        ]);

        return [];
    }

    /**
     * Prepare query bindings by extracting parameter values
     *
     * @param string $query
     * @param array $parameterValues
     * @return array Associative array of parameter names to values
     */
    private function prepareQueryBindings(string $query, array $parameterValues): array
    {
        // Inject authenticated user context parameters
        if (auth()->check()) {
            $parameterValues['user_id'] = $parameterValues['user_id'] ?? auth()->id();
            $parameterValues['branch_id'] = $parameterValues['branch_id'] ?? (auth()->user()->branch_id ?? null);
            $parameterValues['p_school_id'] = $parameterValues['p_school_id'] ?? (auth()->user()->school_id ?? null);
        }

        $bindings = [];

        // Extract ALL :placeholder occurrences (including duplicates)
        preg_match_all('/:([a-zA-Z_][a-zA-Z0-9_]*)/', $query, $matches);

        if (!empty($matches[0])) {
            // Add binding value for EACH occurrence
            foreach ($matches[0] as $placeholder) {
                $paramName = str_replace(':', '', $placeholder);
                $bindings[] = $parameterValues[$paramName] ?? null;
            }
        }

        Log::info('Parameter bindings prepared', [
            'query' => $query,
            'parameter_values' => $parameterValues,
            'bindings' => $bindings,
            'placeholder_count' => count($matches[0] ?? [])
        ]);

        return $bindings;
    }

    /**
     * Replace named placeholders with positional ? placeholders
     *
     * @param string $query
     * @param array $parameterNames
     * @return string
     */
    private function replacePlaceholdersWithPositional(string $query, array $bindings): string
    {
        // Replace ALL :paramName with ? in sequential order
        $processedQuery = preg_replace('/:([a-zA-Z_][a-zA-Z0-9_]*)/', '?', $query);

        Log::info('Query placeholder replacement', [
            'original_query' => $query,
            'processed_query' => $processedQuery,
            'binding_count' => count($bindings)
        ]);

        return $processedQuery;
    }

    /**
     * Execute stored procedure with parameters
     *
     * @param string $procedureName
     * @param array $parameters
     * @return Collection
     */
    public function executeStoredProcedure(string $procedureName, array $parameters = []): Collection
    {
        try {
            // Build CALL statement with proper parameter placeholders
            $placeholders = array_fill(0, count($parameters), '?');
            $sql = sprintf('CALL %s(%s)', $procedureName, implode(', ', $placeholders));

            Log::info('Executing stored procedure', [
                'procedure' => $procedureName,
                'sql' => $sql,
                'parameters' => $parameters
            ]);

            $results = DB::select($sql, $parameters);

            return collect($results)->map(function ($item) {
                return (array) $item;
            });
        } catch (\Exception $e) {
            Log::error('Failed to execute stored procedure', [
                'procedure' => $procedureName,
                'parameters' => $parameters,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            throw $e;
        }
    }

    /**
     * Get all report categories
     *
     * @param string|null $module
     * @return Collection
     */
    public function getAllCategories(?string $module = null): Collection
    {
        $query = ReportCategory::query()->orderBy('display_order');

        if ($module) {
            $query->where('module', $module);
        }

        return $query->get();
    }

    /**
     * Check if user has permission to access report
     *
     * @param ReportCenter $report
     * @param array $userRoles
     * @return bool
     */
    public function userCanAccessReport(ReportCenter $report, array $userRoles): bool
    {
        // If no roles specified, report is accessible to all
        if (empty($report->roles) || !is_array($report->roles)) {
            return true;
        }

        // Check if user has any of the required roles
        return !empty(array_intersect($report->roles, $userRoles));
    }

    /**
     * Validate parameters against report definition
     *
     * @param int $reportId
     * @param array $inputParameters
     * @return array Validation errors (empty if valid)
     */
    public function validateReportParameters(int $reportId, array $inputParameters): array
    {
        $errors = [];
        $reportParameters = $this->getReportParameters($reportId);

        foreach ($reportParameters as $param) {
            $paramName = $param->name;
            $value = $inputParameters[$paramName] ?? null;

            // Check required parameters
            if ($param->isRequired() && empty($value) && $value !== '0' && $value !== 0) {
                $errors[$paramName] = "{$param->label} is required";
                continue;
            }

            // Type validation
            if (!empty($value)) {
                switch ($param->type) {
                    case 'number':
                        if (!is_numeric($value)) {
                            $errors[$paramName] = "{$param->label} must be a number";
                        }
                        break;
                    case 'date':
                        if (!$this->isValidDate($value)) {
                            $errors[$paramName] = "{$param->label} must be a valid date";
                        }
                        break;
                    case 'multiselect':
                        if (!is_array($value)) {
                            $errors[$paramName] = "{$param->label} must be an array";
                        }
                        break;
                }
            }
        }

        return $errors;
    }

    /**
     * Check if string is a valid date
     *
     * @param mixed $date
     * @return bool
     */
    private function isValidDate($date): bool
    {
        if (!is_string($date)) {
            return false;
        }

        $d = \DateTime::createFromFormat('Y-m-d', $date);
        return $d && $d->format('Y-m-d') === $date;
    }

    /**
     * Cache parameter values for frequently accessed dropdowns
     *
     * @param int $parameterId
     * @param array $parentValues
     * @param int $ttl Cache TTL in seconds
     * @return array
     */
    public function getCachedParameterValues(int $parameterId, array $parentValues = [], int $ttl = 3600): array
    {
        $parameter = $this->getParameterById($parameterId);

        if (!$parameter) {
            return [];
        }

        // Don't cache dependent parameters
        if ($parameter->hasParent() && !empty($parentValues)) {
            return $this->getParameterValues($parameter, $parentValues);
        }

        // Cache static or independent dynamic values with tenant isolation
        $cacheKey = "report_parameter_values_{$parameterId}";

        // Add user context to cache key for tenant isolation
        if (auth()->check()) {
            $branchId = auth()->user()->branch_id ?? 'global';
            $schoolId = auth()->user()->school_id ?? 'global';
            $cacheKey .= ":{$branchId}:{$schoolId}";
        }

        return Cache::remember($cacheKey, $ttl, function () use ($parameter, $parentValues) {
            return $this->getParameterValues($parameter, $parentValues);
        });
    }

    /**
     * Clear cached parameter values
     *
     * @param int $parameterId
     * @return void
     */
    public function clearParameterValuesCache(int $parameterId): void
    {
        $cacheKey = "report_parameter_values_{$parameterId}";
        Cache::forget($cacheKey);
    }
}
