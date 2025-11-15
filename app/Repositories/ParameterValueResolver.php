<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\ReportParameter;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

/**
 * ParameterValueResolver
 *
 * Resolves dynamic dropdown values for report parameters by executing SQL queries
 * or parsing static values. Handles parameter substitution and caching.
 */
class ParameterValueResolver
{
    /**
     * Cache duration in seconds (15 minutes).
     *
     * @var int
     */
    private const CACHE_DURATION = 900;

    /**
     * Maximum allowed query execution time in seconds.
     *
     * @var int
     */
    private const MAX_QUERY_TIMEOUT = 30;

    /**
     * Whitelisted SQL keywords for security.
     *
     * @var array<string>
     */
    private const ALLOWED_SQL_KEYWORDS = [
        'SELECT', 'FROM', 'WHERE', 'JOIN', 'LEFT', 'RIGHT', 'INNER', 'OUTER',
        'ON', 'AND', 'OR', 'ORDER BY', 'GROUP BY', 'HAVING', 'LIMIT',
        'AS', 'DISTINCT', 'IN', 'NOT', 'IS', 'NULL', 'LIKE', 'BETWEEN',
    ];

    /**
     * Resolve parameter values (static or dynamic).
     *
     * @param ReportParameter $parameter
     * @param string|null $parentValue
     * @return array Array of [['value' => ..., 'label' => ...], ...]
     */
    public function resolve(ReportParameter $parameter, ?string $parentValue = null): array
    {
        // Handle static values
        if ($parameter->hasStaticValues()) {
            return $this->parseStaticValues($parameter->values);
        }

        // Handle dynamic query
        if ($parameter->hasDynamicQuery()) {
            return $this->resolveDynamicValues($parameter, $parentValue);
        }

        return [];
    }

    /**
     * Resolve dynamic values from database query.
     *
     * @param ReportParameter $parameter
     * @param string|null $parentValue
     * @return array
     */
    private function resolveDynamicValues(ReportParameter $parameter, ?string $parentValue = null): array
    {
        $query = $parameter->getQueryString();

        if (empty($query)) {
            return [];
        }

        // Build cache key
        $cacheKey = $this->buildCacheKey($parameter->id, $parentValue);

        // Try to get from cache
        return Cache::remember($cacheKey, self::CACHE_DURATION, function () use ($query, $parameter, $parentValue) {
            try {
                // Prepare parameters for substitution
                $parameters = $this->prepareParameters($parameter, $parentValue);

                // Execute query with parameter substitution
                $results = $this->executeQuery($query, $parameters);

                return $this->formatQueryResults($results);
            } catch (\Exception $e) {
                Log::error('Failed to resolve parameter values', [
                    'parameter_id' => $parameter->id,
                    'parameter_name' => $parameter->name,
                    'parent_value' => $parentValue,
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString(),
                ]);

                return [];
            }
        });
    }

    /**
     * Execute SQL query with parameter substitution.
     *
     * @param string $query
     * @param array $parameters
     * @return array
     * @throws \Exception
     */
    public function executeQuery(string $query, array $parameters = []): array
    {
        // Validate query safety
        $this->validateQuery($query);

        // Substitute parameters in query
        $preparedQuery = $this->substituteParameters($query, $parameters);

        // Set query timeout
        DB::statement('SET SESSION MAX_EXECUTION_TIME=' . (self::MAX_QUERY_TIMEOUT * 1000));

        try {
            // Execute query
            $results = DB::select($preparedQuery);

            return $results;
        } catch (\Exception $e) {
            Log::error('Query execution failed', [
                'query' => $preparedQuery,
                'parameters' => $parameters,
                'error' => $e->getMessage(),
            ]);

            throw new \Exception('Failed to execute parameter query: ' . $e->getMessage());
        }
    }

    /**
     * Substitute parameter placeholders in query.
     *
     * @param string $query
     * @param array $parameters
     * @return string
     */
    public function substituteParameters(string $query, array $parameters): string
    {
        if (empty($parameters)) {
            return $query;
        }

        $substituted = $query;

        foreach ($parameters as $key => $value) {
            $placeholder = ':' . $key;

            // Escape value for SQL injection prevention
            $escapedValue = $this->escapeValue($value);

            $substituted = str_replace($placeholder, $escapedValue, $substituted);
        }

        return $substituted;
    }

    /**
     * Parse static JSON values into standardized format.
     *
     * @param string $jsonValues
     * @return array Array of [['value' => ..., 'label' => ...], ...]
     */
    public function parseStaticValues(string $jsonValues): array
    {
        $decoded = json_decode($jsonValues, true);

        if (!is_array($decoded)) {
            return [];
        }

        $formatted = [];

        foreach ($decoded as $key => $item) {
            // Handle different formats
            if (is_array($item)) {
                // Format: [['value' => 1, 'label' => 'Option 1'], ...]
                if (isset($item['value']) && isset($item['label'])) {
                    $formatted[] = [
                        'value' => $item['value'],
                        'label' => $item['label'],
                    ];
                }
                // Format: ['key' => 'value'] pairs
                else {
                    foreach ($item as $k => $v) {
                        $formatted[] = [
                            'value' => $k,
                            'label' => $v,
                        ];
                    }
                }
            }
            // Format: ['Option 1', 'Option 2', ...]
            elseif (is_string($item)) {
                $formatted[] = [
                    'value' => $item,
                    'label' => $item,
                ];
            }
            // Format: [1 => 'Option 1', 2 => 'Option 2']
            else {
                $formatted[] = [
                    'value' => $key,
                    'label' => $item,
                ];
            }
        }

        return $formatted;
    }

    /**
     * Validate query for security.
     *
     * @param string $query
     * @return void
     * @throws \Exception
     */
    private function validateQuery(string $query): void
    {
        $upperQuery = strtoupper(trim($query));

        // Must start with SELECT
        if (!str_starts_with($upperQuery, 'SELECT')) {
            throw new \Exception('Only SELECT queries are allowed for parameter values');
        }

        // Check for dangerous keywords
        $dangerousKeywords = [
            'DROP', 'DELETE', 'UPDATE', 'INSERT', 'TRUNCATE', 'ALTER',
            'CREATE', 'REPLACE', 'GRANT', 'REVOKE', 'EXECUTE', 'EXEC',
        ];

        foreach ($dangerousKeywords as $keyword) {
            if (str_contains($upperQuery, $keyword)) {
                throw new \Exception("Query contains dangerous keyword: {$keyword}");
            }
        }

        // Check for suspicious patterns
        $suspiciousPatterns = [
            '/;\s*SELECT/i',          // Multiple queries
            '/--/i',                   // SQL comments
            '/\/\*/i',                // Block comments
            '/UNION\s+SELECT/i',      // Union attacks
            '/INTO\s+(OUT|DUMP)FILE/i', // File operations
        ];

        foreach ($suspiciousPatterns as $pattern) {
            if (preg_match($pattern, $query)) {
                throw new \Exception('Query contains suspicious pattern: ' . $pattern);
            }
        }
    }

    /**
     * Escape value for SQL query.
     *
     * @param mixed $value
     * @return string
     */
    private function escapeValue($value): string
    {
        if (is_null($value)) {
            return 'NULL';
        }

        if (is_numeric($value)) {
            return (string) $value;
        }

        // Use PDO quote for string escaping
        return DB::connection()->getPdo()->quote($value);
    }

    /**
     * Format query results to standardized array structure.
     *
     * @param array $results
     * @return array
     */
    private function formatQueryResults(array $results): array
    {
        if (empty($results)) {
            return [];
        }

        $formatted = [];

        foreach ($results as $row) {
            $rowArray = (array) $row;

            // Expected format: columns named 'value' and 'label'
            if (isset($rowArray['value']) && isset($rowArray['label'])) {
                $formatted[] = [
                    'value' => $rowArray['value'],
                    'label' => $rowArray['label'],
                ];
            }
            // Fallback: use first two columns
            elseif (count($rowArray) >= 2) {
                $values = array_values($rowArray);
                $formatted[] = [
                    'value' => $values[0],
                    'label' => $values[1],
                ];
            }
            // Single column: use as both value and label
            elseif (count($rowArray) === 1) {
                $value = reset($rowArray);
                $formatted[] = [
                    'value' => $value,
                    'label' => $value,
                ];
            }
        }

        return $formatted;
    }

    /**
     * Prepare parameters for query substitution.
     *
     * @param ReportParameter $parameter
     * @param string|null $parentValue
     * @return array
     */
    private function prepareParameters(ReportParameter $parameter, ?string $parentValue = null): array
    {
        $parameters = [];

        // Add parent value if exists
        if ($parameter->hasParent() && !is_null($parentValue)) {
            $parent = $parameter->parent;
            if ($parent) {
                $parameters[$parent->name] = $parentValue;
                $parameters['parent_value'] = $parentValue;
            }
        }

        // Add current user context
        if (auth()->check()) {
            $parameters['user_id'] = auth()->id();
            $parameters['branch_id'] = auth()->user()->branch_id ?? null;
            $parameters['p_school_id'] = auth()->user()->school_id ?? null;
        }

        return $parameters;
    }

    /**
     * Build cache key for parameter values.
     *
     * @param int $parameterId
     * @param string|null $parentValue
     * @return string
     */
    private function buildCacheKey(int $parameterId, ?string $parentValue = null): string
    {
        $key = "report_parameter_values:{$parameterId}";

        if (!is_null($parentValue)) {
            $key .= ':' . md5($parentValue);
        }

        // Add user context to cache key for tenant isolation
        if (auth()->check()) {
            $branchId = auth()->user()->branch_id ?? 'global';
            $schoolId = auth()->user()->school_id ?? 'global';
            $key .= ":{$branchId}:{$schoolId}";
        }

        return $key;
    }

    /**
     * Clear cache for a specific parameter.
     *
     * @param int $parameterId
     * @return void
     */
    public function clearCache(int $parameterId): void
    {
        // Clear all variations of this parameter's cache
        $pattern = "report_parameter_values:{$parameterId}*";

        // Note: This is a simple implementation. For production, consider using
        // Redis tags or a more sophisticated cache invalidation strategy.
        Cache::forget($pattern);
    }

    /**
     * Clear all parameter value caches.
     *
     * @return void
     */
    public function clearAllCache(): void
    {
        // Clear all report parameter caches
        // Note: This is a simple implementation. For production, consider using
        // Redis tags or cache tagging for more efficient invalidation.
        Cache::flush();
    }
}
