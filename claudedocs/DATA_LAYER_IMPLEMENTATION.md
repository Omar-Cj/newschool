# Data Layer Implementation - Metadata-Driven Reporting System

## Overview
Complete implementation of Eloquent models and repositories for the metadata-driven reporting system. This layer provides the foundation for dynamic report generation with configurable parameters.

## Architecture

### Database Schema
- **report_center**: Master registry of all reports
- **report_parameters**: Dynamic parameter definitions for each report
- **report_category**: Organizational categories for reports

### Files Created

```
app/Models/
├── ReportCategory.php        - Category organization model
├── ReportCenter.php           - Main report registry model
└── ReportParameter.php        - Parameter definition model

app/Repositories/
├── ReportRepository.php       - Main data access layer
└── ParameterValueResolver.php - Dynamic dropdown value resolver
```

## Model Details

### 1. ReportCategory Model
**Location**: `/app/Models/ReportCategory.php`

#### Properties
- `id`: Primary key
- `name`: Category name
- `module`: Module association (e.g., 'Finance', 'Academic')
- `icon`: Icon identifier for UI
- `display_order`: Sort order for display
- `created_at`, `updated_at`: Timestamps

#### Relationships
- `reports()`: HasMany relationship to ReportCenter

#### Scopes
- `ordered()`: Orders by display_order and name
- `byModule($module)`: Filters by module

#### Methods
- `getActiveReportsCountAttribute()`: Computed property for active report count

### 2. ReportCenter Model
**Location**: `/app/Models/ReportCenter.php`

#### Properties
- `id`: Primary key
- `name`: Report display name
- `description`: Report description
- `module`: Module association
- `category_id`: Foreign key to report_category
- `status`: Active (1) or Inactive (0)
- `procedure_name`: Stored procedure to execute
- `report_type`: Type (tabular, chart, etc.)
- `export_enabled`: Export capability flag
- `roles`: JSON array of allowed roles
- `created_at`, `updated_at`, `deleted_at`: Timestamps

#### Features
- Soft deletes enabled
- JSON casting for `roles` array
- Multi-branch support via BaseModel (if MultiBranch module enabled)

#### Relationships
- `category()`: BelongsTo ReportCategory
- `parameters()`: HasMany ReportParameter

#### Scopes
- `active()`: Only active reports (status = 1)
- `byModule($module)`: Filter by module
- `byCategory($categoryId)`: Filter by category
- `byType($reportType)`: Filter by report type

#### Methods
- `isActive()`: Check if report is active
- `isExportEnabled()`: Check if export is enabled
- `canAccessByRole($role)`: Check role-based access
- `getOrderedParameters()`: Get parameters ordered by display_order
- `getRequiredParameters()`: Get only required parameters
- `getOptionalParameters()`: Get only optional parameters

### 3. ReportParameter Model
**Location**: `/app/Models/ReportParameter.php`

#### Properties
- `id`: Primary key
- `report_id`: Foreign key to report_center
- `name`: Parameter name (e.g., 'p_start_date')
- `label`: Display label (e.g., 'Start Date')
- `type`: UI component type (text, date, select, etc.)
- `placeholder`: Placeholder text
- `value_type`: Data type (int, string, date, etc.)
- `values`: JSON for static values or query definition
- `default_value`: Default value
- `parent_id`: Foreign key for dependent parameters
- `is_required`: Required flag (0 or 1)
- `display_order`: Sort order
- `validation_rules`: JSON array of Laravel validation rules

#### Constants
```php
public const TYPES = [
    'text', 'number', 'date', 'datetime',
    'select', 'multiselect', 'checkbox', 'radio'
];
```

#### Relationships
- `report()`: BelongsTo ReportCenter
- `parent()`: BelongsTo ReportParameter (self-referential)
- `children()`: HasMany ReportParameter (dependent parameters)

#### Scopes
- `ordered()`: Orders by display_order and id
- `required()`: Only required parameters
- `optional()`: Only optional parameters

#### Methods
- `hasParent()`: Check if parameter has parent
- `isRequired()`: Check if required
- `isDropdown()`: Check if select/multiselect type
- `hasStaticValues()`: Check if values are static JSON array
- `hasDynamicQuery()`: Check if values come from database query
- `getParsedValues()`: Get parsed static values as array
- `getQueryString()`: Get SQL query for dynamic values
- `getValidationRulesArray()`: Get validation rules with type-based defaults

#### Values Format Examples

**Static Values**:
```json
[
    {"value": 1, "label": "Option 1"},
    {"value": 2, "label": "Option 2"}
]
```

**Dynamic Query**:
```json
{
    "source": "query",
    "query": "SELECT id AS value, name AS label FROM classes WHERE status = 1"
}
```

## Repository Details

### 1. ReportRepository
**Location**: `/app/Repositories/ReportRepository.php`

Main data access layer for reports with comprehensive query methods.

#### Constructor
```php
public function __construct(ParameterValueResolver $valueResolver)
```
Injects ParameterValueResolver for dropdown value resolution.

#### Core Methods

##### Report Retrieval
```php
// Get all reports grouped by category
public function getAllReportsGroupedByCategory(?string $module = null): Collection

// Get all active reports
public function getAllReports(): Collection

// Get single report with parameters
public function getReportById(int $reportId): ?ReportCenter

// Get report with resolved dropdown values
public function getReportWithParameters(int $reportId): ?ReportCenter
```

##### Parameter Management
```php
// Get ordered parameters for report
public function getReportParameters(int $reportId): Collection

// Get single parameter
public function getParameterById(int $parameterId): ?ReportParameter

// Resolve dropdown values (with parent value for cascading)
public function resolveParameterDropdownValues(
    int $parameterId,
    ?string $parentValue = null
): array
```

##### Filtering Methods
```php
// Filter by module
public function getReportsByModule(string $module): Collection

// Filter by category
public function getReportsByCategory(int $categoryId): Collection

// Filter by type
public function getReportsByType(string $reportType): Collection

// Filter by role
public function getReportsByRole(string $role): Collection
```

##### Category Methods
```php
// Get all categories
public function getAllCategories(?string $module = null): Collection

// Get category by ID
public function getCategoryById(int $categoryId): ?ReportCategory
```

##### Report Execution
```php
// Execute report with parameters
public function executeReport(int $reportId, array $parameters = []): array
```

Executes the stored procedure with provided parameters and returns results.

**Return Format**:
```php
[
    'success' => true,
    'report' => [
        'id' => 1,
        'name' => 'Unpaid Students Report',
        'description' => '...',
        'type' => 'tabular'
    ],
    'parameters' => ['p_start_date' => '2025-01-01', ...],
    'data' => [...], // Query results
    'executed_at' => '2025-10-11T19:04:23.000000Z'
]
```

##### Utility Methods
```php
// Get report statistics
public function getReportStats(int $reportId): array

// Search reports by name/description
public function searchReports(string $searchTerm): Collection

// Check user access by role
public function canAccessReport(int $reportId, string $userRole): bool
```

### 2. ParameterValueResolver
**Location**: `/app/Repositories/ParameterValueResolver.php`

Resolves dropdown values from static JSON or dynamic SQL queries with security and caching.

#### Security Features
- Query validation (only SELECT allowed)
- SQL injection prevention via parameterized queries
- Dangerous keyword detection (DROP, DELETE, UPDATE, etc.)
- Suspicious pattern detection (UNION, comments, etc.)
- Query timeout enforcement (30 seconds max)

#### Core Methods

##### Value Resolution
```php
// Resolve parameter values (static or dynamic)
public function resolve(ReportParameter $parameter, ?string $parentValue = null): array
```

Returns standardized format:
```php
[
    ['value' => 1, 'label' => 'Option 1'],
    ['value' => 2, 'label' => 'Option 2']
]
```

##### Query Execution
```php
// Execute SQL query with parameter substitution
public function executeQuery(string $query, array $parameters = []): array

// Substitute :parameter_name placeholders
public function substituteParameters(string $query, array $parameters): string
```

##### Static Value Parsing
```php
// Parse JSON values to standardized format
public function parseStaticValues(string $jsonValues): array
```

Handles multiple formats:
- `[{"value": 1, "label": "Option 1"}]`
- `["Option 1", "Option 2"]`
- `{1: "Option 1", 2: "Option 2"}`

#### Caching Strategy
- Cache duration: 15 minutes (900 seconds)
- Cache key format: `report_parameter_values:{param_id}:{parent_hash}:{branch_id}`
- Tenant-aware caching (branch_id isolation)

##### Cache Methods
```php
// Clear cache for specific parameter
public function clearCache(int $parameterId): void

// Clear all parameter caches
public function clearAllCache(): void
```

#### Parameter Substitution
Automatically substitutes these placeholders in queries:
- `:parameter_name` - Parent parameter value
- `:parent_value` - Generic parent value
- `:user_id` - Current authenticated user ID
- `:branch_id` - Current user's branch ID

Example:
```sql
-- Query definition
SELECT id AS value, name AS label
FROM sections
WHERE class_id = :p_class_id
  AND branch_id = :branch_id

-- Substituted query (when p_class_id = 5, branch_id = 2)
SELECT id AS value, name AS label
FROM sections
WHERE class_id = 5
  AND branch_id = 2
```

## Usage Examples

### Example 1: Get All Reports Grouped by Category
```php
$repository = new ReportRepository(new ParameterValueResolver());

// Get all reports grouped by category
$categories = $repository->getAllReportsGroupedByCategory();

foreach ($categories as $category) {
    echo $category->name . ":\n";

    foreach ($category->reports as $report) {
        echo "  - " . $report->name . "\n";
        echo "    Parameters: " . $report->parameters->count() . "\n";
    }
}
```

### Example 2: Get Report with Resolved Parameters
```php
$repository = new ReportRepository(new ParameterValueResolver());

// Get report with resolved dropdown values
$report = $repository->getReportWithParameters(1);

foreach ($report->parameters as $param) {
    echo $param->label . " (" . $param->type . ")\n";

    if ($param->isDropdown() && isset($param->dropdown_values)) {
        echo "  Options:\n";
        foreach ($param->dropdown_values as $option) {
            echo "    - {$option['label']} ({$option['value']})\n";
        }
    }
}
```

### Example 3: Execute Report
```php
$repository = new ReportRepository(new ParameterValueResolver());

try {
    $result = $repository->executeReport(1, [
        'p_start_date' => '2025-01-01',
        'p_end_date' => '2025-10-11',
        'p_class_id' => 5,
        'p_status' => 1
    ]);

    if ($result['success']) {
        echo "Report executed successfully\n";
        echo "Rows returned: " . count($result['data']) . "\n";

        foreach ($result['data'] as $row) {
            print_r($row);
        }
    }
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
```

### Example 4: Cascading Dropdowns
```php
$repository = new ReportRepository(new ParameterValueResolver());

// Get class dropdown values
$classes = $repository->resolveParameterDropdownValues(1); // class parameter

// When user selects class ID 5, get sections for that class
$sections = $repository->resolveParameterDropdownValues(2, '5'); // section parameter, parent value = 5
```

### Example 5: Role-Based Access
```php
$repository = new ReportRepository(new ParameterValueResolver());

$userRole = auth()->user()->role->name;

// Get reports accessible by current user role
$accessibleReports = $repository->getReportsByRole($userRole);

// Check access to specific report
$canAccess = $repository->canAccessReport(1, $userRole);

if ($canAccess) {
    echo "User can access this report\n";
} else {
    echo "Access denied\n";
}
```

## Validation

All parameters support automatic validation rule generation:

```php
$parameter = ReportParameter::find(1);
$rules = $parameter->getValidationRulesArray();

// Example output for required date parameter:
// ['required', 'date']

// Example output for optional number parameter:
// ['nullable', 'numeric']
```

## Security Considerations

### 1. Query Validation
- Only SELECT statements allowed
- Blocks dangerous keywords: DROP, DELETE, UPDATE, INSERT, etc.
- Detects SQL injection patterns: UNION, comments, multiple queries
- Enforces query timeout (30 seconds)

### 2. Parameter Escaping
- Uses PDO quote for string values
- Validates numeric types
- NULL handling
- Prevents SQL injection via prepared parameters

### 3. Tenant Isolation
- Automatically applies branch_id filtering (if MultiBranch enabled)
- Cache keys include branch context
- Parameter substitution includes user/branch context

### 4. Role-Based Access Control
- Reports can restrict access by role
- Repository provides access checking methods
- Empty roles array = no restrictions

## Performance Optimization

### Caching
- Dropdown values cached for 15 minutes
- Tenant-aware cache keys
- Parent-value aware for cascading dropdowns

### Query Optimization
- Eager loading relationships (`with()`)
- Indexed columns: status, category_id, report_id, display_order
- Scoped queries to reduce data load

### Best Practices
1. Always use `active()` scope for production queries
2. Eager load relationships when fetching multiple reports
3. Use `getOrderedParameters()` to maintain UI consistency
4. Clear parameter cache after updating values definitions
5. Monitor stored procedure execution time

## Error Handling

All methods implement try-catch blocks with logging:

```php
try {
    $results = $repository->executeReport($reportId, $params);
} catch (\Exception $e) {
    // Error logged to Laravel log with context
    // Exception message returned to caller
}
```

Log entries include:
- Report/parameter IDs and names
- Query statements
- Parameter values
- Error messages and stack traces

## Testing Recommendations

### Unit Tests
1. Test model relationships
2. Test scopes with various filters
3. Test value parsing (static/dynamic)
4. Test validation rule generation

### Feature Tests
1. Test report execution with valid parameters
2. Test missing required parameters
3. Test cascading dropdown resolution
4. Test role-based access control
5. Test query security validation

### Integration Tests
1. Test with actual stored procedures
2. Test multi-tenant isolation
3. Test caching behavior
4. Test query performance

## Next Steps

To complete the reporting system:

1. **Create Controllers**: API endpoints for frontend consumption
2. **Create Services**: Business logic layer for complex operations
3. **Create Form Requests**: Input validation
4. **Create Resources**: API response formatting
5. **Frontend Integration**: Vue/React components
6. **Export Functionality**: PDF/Excel/CSV generation
7. **Scheduled Reports**: Background job execution
8. **Report Logging**: Audit trail for executions

## File Locations Summary

```
/home/eng-omar/remote-projects/new_school_system/
├── app/
│   ├── Models/
│   │   ├── ReportCategory.php          ✓ Created
│   │   ├── ReportCenter.php            ✓ Created
│   │   └── ReportParameter.php         ✓ Created
│   └── Repositories/
│       ├── ReportRepository.php        ✓ Created
│       └── ParameterValueResolver.php  ✓ Created
├── Tasks.md                            ✓ Created
└── claudedocs/
    └── DATA_LAYER_IMPLEMENTATION.md    ✓ This file
```

## Conclusion

The data layer implementation provides a complete foundation for the metadata-driven reporting system. All models include comprehensive relationships, scopes, and helper methods. The repository layer provides secure, cached, and performant data access with role-based filtering and dynamic parameter resolution.

All code follows Laravel best practices, PSR-12 standards, and includes extensive PHPDoc documentation.
