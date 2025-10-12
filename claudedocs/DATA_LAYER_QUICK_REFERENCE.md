# Data Layer Quick Reference

## Files Created

### Models
- `/app/Models/ReportCategory.php` - 91 lines
- `/app/Models/ReportCenter.php` - 212 lines
- `/app/Models/ReportParameter.php` - 287 lines

### Repositories
- `/app/Repositories/ReportRepository.php` - 495 lines
- `/app/Repositories/ParameterValueResolver.php` - 403 lines

### Documentation
- `/claudedocs/DATA_LAYER_IMPLEMENTATION.md` - Complete implementation guide
- `/Tasks.md` - Task tracking

**Total Lines of Code**: ~1,488 lines (excluding documentation)

## Quick Usage Guide

### 1. Basic Report Retrieval

```php
use App\Repositories\ReportRepository;
use App\Repositories\ParameterValueResolver;

$repository = new ReportRepository(new ParameterValueResolver());

// Get all reports grouped by category
$categories = $repository->getAllReportsGroupedByCategory();

// Get reports for specific module
$financeReports = $repository->getReportsByModule('Finance');

// Get single report with parameters
$report = $repository->getReportById(1);
```

### 2. Parameter Value Resolution

```php
// Get dropdown options for a parameter
$classOptions = $repository->resolveParameterDropdownValues(1);
// Returns: [['value' => 1, 'label' => 'Class A'], ...]

// Cascading dropdown (e.g., sections depend on class)
$sectionOptions = $repository->resolveParameterDropdownValues(2, '5'); // parent class ID = 5
```

### 3. Report Execution

```php
try {
    $result = $repository->executeReport(1, [
        'p_start_date' => '2025-01-01',
        'p_end_date' => '2025-10-11',
        'p_class_id' => 5,
        'p_status' => 1
    ]);

    // Access results
    $reportData = $result['data'];
    $reportInfo = $result['report'];
    $executedAt = $result['executed_at'];
} catch (\Exception $e) {
    // Handle error
    Log::error('Report execution failed: ' . $e->getMessage());
}
```

### 4. Role-Based Access

```php
// Check if user can access report
$userRole = auth()->user()->role->name;
$canAccess = $repository->canAccessReport(1, $userRole);

// Get all accessible reports for user
$accessibleReports = $repository->getReportsByRole($userRole);
```

### 5. Model Methods

```php
// ReportCenter model
$report = ReportCenter::find(1);
$report->isActive();              // Check if active
$report->isExportEnabled();       // Check if export enabled
$report->canAccessByRole('admin'); // Check role access
$report->getOrderedParameters();  // Get parameters sorted
$report->getRequiredParameters(); // Get only required params

// ReportParameter model
$parameter = ReportParameter::find(1);
$parameter->hasParent();          // Check if has parent
$parameter->isRequired();         // Check if required
$parameter->isDropdown();         // Check if dropdown type
$parameter->hasStaticValues();    // Check if static values
$parameter->hasDynamicQuery();    // Check if dynamic query
$parameter->getValidationRulesArray(); // Get Laravel validation rules
```

## Model Relationships

```
ReportCategory (1) ─┬─> (Many) ReportCenter
                    │
ReportCenter (1) ───┴─> (Many) ReportParameter
                    │
ReportParameter (parent) (1) ──> (Many) ReportParameter (children)
```

## Query Scopes

```php
// ReportCenter scopes
ReportCenter::active()->get();                    // Only active reports
ReportCenter::byModule('Finance')->get();         // Filter by module
ReportCenter::byCategory(1)->get();               // Filter by category
ReportCenter::byType('tabular')->get();           // Filter by type

// ReportParameter scopes
ReportParameter::ordered()->get();                // Order by display_order
ReportParameter::required()->get();               // Only required
ReportParameter::optional()->get();               // Only optional

// ReportCategory scopes
ReportCategory::ordered()->get();                 // Order by display_order
ReportCategory::byModule('Finance')->get();       // Filter by module
```

## Parameter Types

| Type | Description | UI Component |
|------|-------------|--------------|
| text | Text input | `<input type="text">` |
| number | Number input | `<input type="number">` |
| date | Date picker | `<input type="date">` |
| datetime | Date/time picker | `<input type="datetime-local">` |
| select | Dropdown (single) | `<select>` |
| multiselect | Dropdown (multiple) | `<select multiple>` |
| checkbox | Checkbox | `<input type="checkbox">` |
| radio | Radio buttons | `<input type="radio">` |

## Parameter Values Format

### Static Values (JSON Array)
```json
[
    {"value": 1, "label": "Active"},
    {"value": 0, "label": "Inactive"}
]
```

### Dynamic Query (Database)
```json
{
    "source": "query",
    "query": "SELECT id AS value, name AS label FROM classes WHERE status = 1 ORDER BY name"
}
```

### Cascading (Parent-Child)
```json
{
    "source": "query",
    "query": "SELECT id AS value, name AS label FROM sections WHERE class_id = :p_class_id"
}
```

## Security Features

- Query validation (only SELECT allowed)
- SQL injection prevention
- Dangerous keyword blocking (DROP, DELETE, UPDATE, etc.)
- Parameterized queries
- Query timeout enforcement (30 seconds)
- Tenant isolation (branch_id filtering)

## Caching Strategy

- Cache duration: 15 minutes
- Cache key pattern: `report_parameter_values:{param_id}:{parent_hash}:{branch_id}`
- Automatic cache invalidation on data changes
- Tenant-aware caching

## Validation Rules

Parameters automatically generate validation rules:

```php
$parameter->getValidationRulesArray();

// Required date parameter returns:
['required', 'date']

// Optional number parameter returns:
['nullable', 'numeric']

// Select parameter returns:
['nullable', 'string']
```

## Common Patterns

### Pattern 1: Display Reports by Category
```php
$categories = $repository->getAllReportsGroupedByCategory('Finance');

foreach ($categories as $category) {
    echo "<h2>{$category->name}</h2>";
    foreach ($category->reports as $report) {
        echo "<a href='/reports/{$report->id}'>{$report->name}</a>";
    }
}
```

### Pattern 2: Build Report Form
```php
$report = $repository->getReportWithParameters(1);

echo "<form action='/reports/{$report->id}/execute' method='POST'>";

foreach ($report->parameters as $param) {
    echo "<label>{$param->label}</label>";

    if ($param->type === 'date') {
        echo "<input type='date' name='{$param->name}' value='{$param->default_value}'>";
    } elseif ($param->type === 'select') {
        echo "<select name='{$param->name}'>";
        foreach ($param->dropdown_values as $option) {
            echo "<option value='{$option['value']}'>{$option['label']}</option>";
        }
        echo "</select>";
    }
}

echo "<button type='submit'>Generate Report</button>";
echo "</form>";
```

### Pattern 3: Execute and Export
```php
$result = $repository->executeReport($reportId, $request->all());

if ($result['success'] && $request->has('export')) {
    return Excel::download(
        new ReportExport($result['data']),
        $result['report']['name'] . '.xlsx'
    );
}

return view('reports.show', ['result' => $result]);
```

## Error Handling

All repository methods use try-catch with logging:

```php
try {
    $report = $repository->executeReport($id, $params);
} catch (\Exception $e) {
    // Error automatically logged with context
    return response()->json([
        'success' => false,
        'message' => $e->getMessage()
    ], 400);
}
```

## Performance Tips

1. **Use Eager Loading**: Always load relationships to avoid N+1
   ```php
   $reports = ReportCenter::with(['category', 'parameters'])->get();
   ```

2. **Cache Dropdown Values**: Automatically cached for 15 minutes
   ```php
   $values = $repository->resolveParameterDropdownValues($paramId);
   ```

3. **Clear Cache After Updates**: Clear parameter cache when values change
   ```php
   $resolver->clearCache($parameterId);
   ```

4. **Filter Early**: Use scopes to reduce query load
   ```php
   $reports = ReportCenter::active()->byModule('Finance')->get();
   ```

5. **Optimize Stored Procedures**: Ensure procedures are properly indexed

## Next Steps for Integration

1. Create API controller: `ReportController.php`
2. Create form requests: `ExecuteReportRequest.php`
3. Create API resources: `ReportResource.php`, `ReportParameterResource.php`
4. Create frontend components (Vue/React)
5. Implement export functionality (PDF/Excel/CSV)
6. Add report scheduling
7. Implement audit logging

## Testing Checklist

- [ ] Test model relationships
- [ ] Test scopes and filters
- [ ] Test parameter value resolution
- [ ] Test static vs dynamic values
- [ ] Test cascading dropdowns
- [ ] Test report execution
- [ ] Test validation rules
- [ ] Test role-based access
- [ ] Test multi-tenant isolation
- [ ] Test caching behavior
- [ ] Test query security validation
- [ ] Test error handling

## Support & Maintenance

For issues or questions:
1. Check `/claudedocs/DATA_LAYER_IMPLEMENTATION.md` for detailed documentation
2. Review model PHPDoc comments for method signatures
3. Check Laravel logs for execution errors
4. Monitor stored procedure performance
5. Review cache hit rates

---

**Implementation Status**: ✅ Complete
**Syntax Validation**: ✅ All files passed PHP lint
**Documentation**: ✅ Comprehensive guides created
**Ready for**: Controller layer and API implementation
