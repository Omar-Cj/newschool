<?php

declare(strict_types=1);

namespace App\Services\Report;

use App\Models\ReportParameter;
use App\Repositories\Report\ReportRepository;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

/**
 * DependentParameterService
 *
 * Handles cascading/dependent dropdowns where child parameter values
 * depend on the selected value of a parent parameter
 */
class DependentParameterService
{
    /**
     * Constructor
     *
     * @param ReportRepository $reportRepository
     * @param BranchParameterService $branchParameterService
     */
    public function __construct(
        private ReportRepository $reportRepository,
        private BranchParameterService $branchParameterService
    ) {}

    /**
     * Resolve dependent values for a parameter based on parent value
     *
     * @param int $parameterId ID of the child parameter
     * @param mixed $parentValue Value selected in parent parameter
     * @return array Array of value/label pairs for the dropdown
     * @throws \Exception If parameter not found or query execution fails
     */
    public function resolveDependentValues(int $parameterId, $parentValue): array
    {
        Log::info('resolveDependentValues started', [
            'parameter_id' => $parameterId,
            'parent_value' => $parentValue,
            'parent_value_type' => gettype($parentValue)
        ]);

        $parameter = $this->reportRepository->getParameterById($parameterId);

        if (!$parameter) {
            Log::error('Parameter not found', ['parameter_id' => $parameterId]);
            throw new \Exception("Parameter not found with ID: {$parameterId}");
        }

        Log::info('Parameter loaded', [
            'parameter_id' => $parameterId,
            'parameter_name' => $parameter->name,
            'parameter_type' => $parameter->type,
            'parent_id' => $parameter->parent_id,
            'has_parent' => $parameter->hasParent(),
            'has_dynamic_query' => $parameter->hasDynamicQuery(),
            'has_static_values' => $parameter->hasStaticValues()
        ]);

        // Check if parameter has a parent dependency
        if (!$parameter->hasParent()) {
            Log::info('Parameter has no parent, returning all values', [
                'parameter_id' => $parameterId
            ]);
            // Not a dependent parameter, return all values
            return $this->reportRepository->getParameterValues($parameter, []);
        }

        // Get parent parameter to know its name
        $parentParameter = $this->reportRepository->getParameterById($parameter->parent_id);

        if (!$parentParameter) {
            Log::error('Parent parameter not found', [
                'parameter_id' => $parameterId,
                'parent_id' => $parameter->parent_id
            ]);
            throw new \Exception("Parent parameter not found with ID: {$parameter->parent_id}");
        }

        Log::info('Parent parameter loaded', [
            'parent_parameter_id' => $parentParameter->id,
            'parent_parameter_name' => $parentParameter->name,
            'parent_parameter_type' => $parentParameter->type
        ]);

        // Build parameter values array for query substitution
        $parameterValues = [
            $parentParameter->name => $parentValue
        ];

        Log::info('Calling getParameterValues', [
            'parameter_id' => $parameterId,
            'parameter_name' => $parameter->name,
            'parameter_values' => $parameterValues
        ]);

        try {
            $values = $this->reportRepository->getParameterValues($parameter, $parameterValues);

            Log::info('Parameter values retrieved', [
                'parameter_id' => $parameterId,
                'values_count' => count($values),
                'sample_values' => array_slice($values, 0, 3)
            ]);

            return $values;
        } catch (\Exception $e) {
            Log::error('Failed to resolve dependent parameter values', [
                'parameter_id' => $parameterId,
                'parameter_name' => $parameter->name,
                'parent_parameter_id' => $parameter->parent_id,
                'parent_parameter_name' => $parentParameter->name,
                'parent_value' => $parentValue,
                'parameter_values' => $parameterValues,
                'error' => $e->getMessage(),
                'error_class' => get_class($e),
                'trace' => $e->getTraceAsString()
            ]);

            throw new \Exception("Failed to load dependent values: {$e->getMessage()}");
        }
    }

    /**
     * Get all dependencies for a report's parameters
     * Returns a tree structure showing parent-child relationships
     *
     * @param int $reportId
     * @return array Dependency tree
     */
    public function getParameterDependencyTree(int $reportId): array
    {
        $allParameters = $this->reportRepository->getReportParameters($reportId);
        $rootParameters = $allParameters->whereNull('parent_id');

        return $this->buildDependencyTree($rootParameters, $allParameters);
    }

    /**
     * Recursively build dependency tree
     *
     * @param \Illuminate\Support\Collection $parameters
     * @param \Illuminate\Support\Collection $allParameters
     * @return array
     */
    private function buildDependencyTree($parameters, $allParameters): array
    {
        $tree = [];

        foreach ($parameters as $parameter) {
            $node = [
                'id' => $parameter->id,
                'name' => $parameter->name,
                'label' => $parameter->label,
                'type' => $parameter->type,
                'is_required' => $parameter->is_required === 1,
                'has_children' => false,
                'children' => []
            ];

            // Find children
            $children = $allParameters->where('parent_id', $parameter->id);

            if ($children->isNotEmpty()) {
                $node['has_children'] = true;
                $node['children'] = $this->buildDependencyTree($children, $allParameters);
            }

            $tree[] = $node;
        }

        return $tree;
    }

    /**
     * Validate parameter dependency chain
     * Ensures no circular dependencies exist
     *
     * @param int $parameterId
     * @param int|null $newParentId
     * @return bool
     * @throws \Exception If circular dependency detected
     */
    public function validateDependencyChain(int $parameterId, ?int $newParentId): bool
    {
        if ($newParentId === null) {
            return true; // No parent, no circular dependency possible
        }

        $visited = [];
        $currentId = $newParentId;

        while ($currentId !== null) {
            // Check for circular reference
            if ($currentId === $parameterId) {
                throw new \Exception('Circular dependency detected');
            }

            if (in_array($currentId, $visited)) {
                throw new \Exception('Circular dependency detected in chain');
            }

            $visited[] = $currentId;

            // Get parent of current parameter
            $parameter = $this->reportRepository->getParameterById($currentId);

            if (!$parameter) {
                break;
            }

            $currentId = $parameter->parent_id;
        }

        return true;
    }

    /**
     * Get all ancestor parameters for a given parameter
     *
     * @param int $parameterId
     * @return array Array of ancestor parameters from root to immediate parent
     */
    public function getAncestorParameters(int $parameterId): array
    {
        $ancestors = [];
        $currentId = $parameterId;

        while ($currentId !== null) {
            $parameter = $this->reportRepository->getParameterById($currentId);

            if (!$parameter || !$parameter->parent_id) {
                break;
            }

            $parent = $this->reportRepository->getParameterById($parameter->parent_id);

            if ($parent) {
                array_unshift($ancestors, [
                    'id' => $parent->id,
                    'name' => $parent->name,
                    'label' => $parent->label,
                    'type' => $parent->type,
                ]);
                $currentId = $parent->id;
            } else {
                break;
            }
        }

        return $ancestors;
    }

    /**
     * Batch resolve dependent values for multiple parameters
     *
     * @param array $requests Array of ['parameter_id' => int, 'parent_value' => mixed]
     * @return array Associative array of parameter_id => values
     */
    public function batchResolveDependentValues(array $requests): array
    {
        $results = [];

        foreach ($requests as $request) {
            $parameterId = $request['parameter_id'] ?? null;
            $parentValue = $request['parent_value'] ?? null;

            if (!$parameterId) {
                continue;
            }

            try {
                $results[$parameterId] = $this->resolveDependentValues($parameterId, $parentValue);
            } catch (\Exception $e) {
                Log::warning('Failed to resolve dependent values in batch', [
                    'parameter_id' => $parameterId,
                    'error' => $e->getMessage()
                ]);

                $results[$parameterId] = [
                    'error' => $e->getMessage(),
                    'values' => []
                ];
            }
        }

        return $results;
    }

    /**
     * Clear cache for dependent parameters when parent values change
     *
     * @param int $reportId
     * @return void
     */
    public function clearDependentParametersCache(int $reportId): void
    {
        $parameters = $this->reportRepository->getReportParameters($reportId);

        foreach ($parameters as $parameter) {
            if ($parameter->hasParent() || $parameter->hasDynamicQuery()) {
                $this->reportRepository->clearParameterValuesCache($parameter->id);
            }
        }
    }

    /**
     * Get initial values for all parameters in a report
     * Handles both independent and dependent parameters
     *
     * @param int $reportId
     * @param array $initialValues Optional initial values for some parameters
     * @return array
     */
    public function getInitialParameterValues(int $reportId, array $initialValues = []): array
    {
        // PREPEND BRANCH PARAMETER AS FIRST PARAMETER (System-level global parameter)
        $branchParamDefinition = $this->branchParameterService->getBranchParameterDefinition();

        // Get branch options from the query
        $branchQuery = json_decode($branchParamDefinition['values'], true)['query'] ?? null;
        $branchValues = [];

        if ($branchQuery) {
            try {
                $branchValues = $this->reportRepository->executeDynamicQuery($branchQuery)
                    ->map(fn($row) => [
                        'value' => $row['value'] ?? null,
                        'label' => $row['label'] ?? 'Unknown'
                    ])->toArray();
            } catch (\Exception $e) {
                Log::error('Failed to load branch parameter values', [
                    'error' => $e->getMessage()
                ]);
            }
        }

        // Build branch parameter data
        $branchParameter = [
            'id' => 0, // System parameter (not stored in report_parameters table)
            'name' => $branchParamDefinition['name'],
            'label' => $branchParamDefinition['label'],
            'type' => $branchParamDefinition['type'],
            'placeholder' => $branchParamDefinition['placeholder'] ?? 'Select Branch',
            'is_required' => $branchParamDefinition['is_required'],
            'default_value' => $branchParamDefinition['default_value'],
            'parent_id' => null,
            'display_order' => $branchParamDefinition['display_order'],
            'values' => $branchValues,
            'is_system_parameter' => true, // Flag to identify system parameters
        ];

        // Get report-specific parameters
        $parameters = $this->reportRepository->getReportParameters($reportId);
        $result = [];

        foreach ($parameters as $parameter) {
            $paramData = [
                'id' => $parameter->id,
                'name' => $parameter->name,
                'label' => $parameter->label,
                'type' => $parameter->type,
                'placeholder' => $parameter->placeholder,
                'is_required' => $parameter->is_required === 1,
                'default_value' => $parameter->default_value,
                'parent_id' => $parameter->parent_id,
                'display_order' => $parameter->display_order,
                'is_system_parameter' => false,
            ];

            // Load values for independent parameters or if parent value is provided
            if (!$parameter->hasParent()) {
                // Root level parameter - load all values
                $paramData['values'] = $this->reportRepository->getCachedParameterValues($parameter->id);
            } elseif (isset($initialValues[$parameter->parent->name])) {
                // Dependent parameter with parent value available
                $parentValue = $initialValues[$parameter->parent->name];
                $paramData['values'] = $this->resolveDependentValues($parameter->id, $parentValue);
            } else {
                // Dependent parameter without parent value - empty for now
                $paramData['values'] = [];
                $paramData['depends_on'] = $parameter->parent->name;
            }

            $result[] = $paramData;
        }

        // PREPEND branch parameter as the FIRST parameter in the list
        array_unshift($result, $branchParameter);

        Log::debug('Initial parameter values loaded with branch parameter', [
            'report_id' => $reportId,
            'total_parameters' => count($result),
            'branch_parameter_position' => 0,
            'branch_default_value' => $branchParameter['default_value'],
            'branch_values_count' => count($branchValues)
        ]);

        return $result;
    }

    /**
     * Execute parameter query with substitution
     * Replaces :parameter_name placeholders with actual values
     *
     * @param string $query SQL query with placeholders
     * @param array $parameterValues Associative array of parameter names to values
     * @return array Query results
     */
    private function executeParameterQuery(string $query, array $parameterValues): array
    {
        // Prepare bindings
        $bindings = [];
        $processedQuery = $query;

        // Replace named placeholders with positional placeholders
        foreach ($parameterValues as $paramName => $value) {
            if (strpos($processedQuery, ":{$paramName}") !== false) {
                $processedQuery = str_replace(":{$paramName}", '?', $processedQuery);
                $bindings[] = $value;
            }
        }

        try {
            $results = $this->reportRepository->executeDynamicQuery($processedQuery, $bindings);

            return $results->map(function ($row) {
                $values = array_values($row);
                return [
                    'value' => $values[0] ?? null,
                    'label' => $values[1] ?? $values[0] ?? 'Unknown'
                ];
            })->toArray();
        } catch (\Exception $e) {
            Log::error('Failed to execute parameter query', [
                'query' => $query,
                'bindings' => $bindings,
                'error' => $e->getMessage()
            ]);

            throw $e;
        }
    }
}
