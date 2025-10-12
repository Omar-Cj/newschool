# Report Center API Documentation

## Overview

The Report Center is a metadata-driven reporting system that allows dynamic report generation through stored procedures with configurable parameters. This API layer provides endpoints for listing reports, fetching parameters, executing reports, and exporting results.

## Architecture

### Components

1. **Models**
   - `ReportCenter` - Main report definition model
   - `ReportParameter` - Dynamic parameter metadata
   - `ReportCategory` - Report categorization

2. **Repository**
   - `ReportRepository` - Data access layer for report operations

3. **Services**
   - `ReportExecutionService` - Handles report execution and validation
   - `DependentParameterService` - Manages cascading dropdown dependencies

4. **Controller**
   - `ReportController` - RESTful API endpoints

## Database Schema

### report_center
- `id` - Primary key
- `name` - Report name
- `description` - Report description
- `module` - Module name (e.g., 'student', 'fees')
- `category_id` - Foreign key to report_category
- `procedure_name` - Stored procedure name to execute
- `report_type` - Type: 'tabular', 'summary', 'chart', 'custom'
- `export_enabled` - Boolean for export capability
- `status` - Report status (1=active, 0=inactive)
- `roles` - JSON array of allowed roles

### report_parameters
- `id` - Primary key
- `report_id` - Foreign key to report_center
- `name` - Parameter name (used in API)
- `label` - Display label
- `type` - Input type: 'text', 'number', 'date', 'select', 'multiselect'
- `placeholder` - Input placeholder
- `value_type` - 'static' or 'dynamic'
- `values` - JSON: static array or query definition
- `default_value` - Default value
- `parent_id` - Foreign key for dependent parameters
- `is_required` - Boolean
- `display_order` - Sort order
- `validation_rules` - JSON array of validation rules

### report_category
- `id` - Primary key
- `name` - Category name
- `module` - Module association
- `icon` - Icon class/name
- `display_order` - Sort order

## API Endpoints

### Base URL
```
/api/teacher/reports
```

All endpoints require authentication via `auth:sanctum` middleware.

---

### 1. List All Reports

**Endpoint:** `GET /api/teacher/reports`

**Description:** Retrieve all active reports grouped by category

**Query Parameters:**
- `module` (optional) - Filter by module name

**Response:**
```json
{
  "success": true,
  "message": "Reports retrieved successfully",
  "data": {
    "categories": [
      {
        "category": {
          "id": 1,
          "name": "Student Reports",
          "icon": "users",
          "module": "student"
        },
        "reports": [
          {
            "id": 1,
            "name": "Student List Report",
            "description": "List of all students with filters",
            "report_type": "tabular",
            "export_enabled": true,
            "parameter_count": 8
          }
        ]
      }
    ]
  }
}
```

---

### 2. Get Report Details

**Endpoint:** `GET /api/teacher/reports/{reportId}`

**Description:** Get detailed information about a specific report including parameter structure

**Response:**
```json
{
  "success": true,
  "message": "Report details retrieved successfully",
  "data": {
    "report": {
      "id": 1,
      "name": "Student List Report",
      "description": "Comprehensive student list with filters",
      "module": "student",
      "category": "Student Reports",
      "report_type": "tabular",
      "export_enabled": true
    },
    "parameter_tree": [
      {
        "id": 1,
        "name": "session",
        "label": "Academic Session",
        "type": "select",
        "is_required": true,
        "has_children": false,
        "children": []
      }
    ]
  }
}
```

---

### 3. Get Report Parameters

**Endpoint:** `GET /api/teacher/reports/{reportId}/parameters`

**Description:** Get all parameters with initial dropdown values

**Query Parameters:**
- `initial_values` (optional) - JSON object with initial parameter values

**Response:**
```json
{
  "success": true,
  "message": "Parameters retrieved successfully",
  "data": {
    "parameters": [
      {
        "id": 1,
        "name": "session",
        "label": "Academic Session",
        "type": "select",
        "placeholder": "Select session",
        "is_required": true,
        "default_value": null,
        "parent_id": null,
        "display_order": 1,
        "values": [
          {
            "value": "1",
            "label": "2024-2025"
          },
          {
            "value": "2",
            "label": "2023-2024"
          }
        ]
      },
      {
        "id": 2,
        "name": "class",
        "label": "Class",
        "type": "select",
        "placeholder": "Select class",
        "is_required": false,
        "default_value": null,
        "parent_id": null,
        "display_order": 2,
        "values": []
      }
    ]
  }
}
```

---

### 4. Get Dependent Parameter Values

**Endpoint:** `POST /api/teacher/reports/parameters/{parameterId}/dependent-values`

**Description:** Fetch filtered values for a dependent dropdown based on parent selection

**Request Body:**
```json
{
  "parent_value": "1"
}
```

**Response:**
```json
{
  "success": true,
  "message": "Dependent values retrieved successfully",
  "data": {
    "parameter_id": 3,
    "values": [
      {
        "value": "101",
        "label": "Section A"
      },
      {
        "value": "102",
        "label": "Section B"
      }
    ]
  }
}
```

---

### 5. Execute Report

**Endpoint:** `POST /api/teacher/reports/{reportId}/execute`

**Description:** Execute the report with provided parameters

**Request Body:**
```json
{
  "parameters": {
    "session": "1",
    "grade": "10",
    "class": "101",
    "section": null,
    "shift": null,
    "category": null,
    "status": "active",
    "gender": null
  }
}
```

**Response (Tabular):**
```json
{
  "success": true,
  "message": "Report executed successfully",
  "data": {
    "report": {
      "id": 1,
      "name": "Student List Report",
      "description": "List of students",
      "type": "tabular"
    },
    "data": {
      "columns": [
        {
          "field": "full_name",
          "label": "Full Name",
          "sortable": true
        },
        {
          "field": "mobile",
          "label": "Mobile",
          "sortable": true
        }
      ],
      "rows": [
        {
          "full_name": "John Doe",
          "mobile": "1234567890",
          "grade": "10",
          "class_name": "Class A",
          "section_name": "Section 1",
          "guardian_name": "Jane Doe"
        }
      ]
    },
    "meta": {
      "total_records": 1,
      "execution_time_ms": 45.23,
      "executed_at": "2025-10-11T19:30:00.000000Z",
      "executed_by": 1,
      "parameters_used": {
        "session": "1",
        "grade": "10",
        "class": "101"
      }
    }
  }
}
```

**Response (Summary):**
```json
{
  "success": true,
  "message": "Report executed successfully",
  "data": {
    "report": {
      "id": 5,
      "name": "Payment Summary",
      "type": "summary"
    },
    "data": [
      {
        "metric": "Total Paid",
        "value": 25000,
        "formatted": "25,000"
      },
      {
        "metric": "Total Unpaid",
        "value": 5000,
        "formatted": "5,000"
      }
    ],
    "meta": {
      "total_records": 2,
      "execution_time_ms": 32.10
    }
  }
}
```

---

### 6. Export Report

**Endpoint:** `POST /api/teacher/reports/{reportId}/export/{format}`

**Description:** Export report results in specified format

**Path Parameters:**
- `format` - Export format: 'excel', 'pdf', or 'csv'

**Request Body:**
```json
{
  "parameters": {
    "session": "1",
    "grade": "10"
  }
}
```

**Response:** File download (Excel/PDF/CSV)

**Error Response (Export Disabled):**
```json
{
  "success": false,
  "message": "Export is not enabled for this report"
}
```

---

### 7. Get Report Statistics

**Endpoint:** `GET /api/teacher/reports/{reportId}/statistics`

**Description:** Get execution statistics for a report

**Query Parameters:**
- `days` (optional, default: 30) - Number of days to look back

**Response:**
```json
{
  "success": true,
  "message": "Statistics retrieved successfully",
  "data": {
    "report_id": 1,
    "period_days": 30,
    "total_executions": 150,
    "avg_execution_time_ms": 45.2,
    "last_executed_at": "2025-10-11T18:00:00.000000Z",
    "most_used_parameters": {
      "session": "1",
      "grade": "10"
    }
  }
}
```

---

### 8. Get All Categories

**Endpoint:** `GET /api/teacher/reports/categories`

**Description:** Get all report categories

**Query Parameters:**
- `module` (optional) - Filter by module

**Response:**
```json
{
  "success": true,
  "message": "Categories retrieved successfully",
  "data": {
    "categories": [
      {
        "id": 1,
        "name": "Student Reports",
        "module": "student",
        "icon": "users",
        "display_order": 1
      }
    ]
  }
}
```

---

## Security Features

### 1. SQL Injection Prevention
- All stored procedure calls use parameterized queries
- Dynamic query bindings are properly escaped
- No direct SQL string concatenation

### 2. Authorization
- Reports can be restricted by roles via `roles` JSON field
- User permissions checked before execution
- Multi-tenant context enforced via middleware

### 3. Input Validation
- Parameter types validated against metadata
- Required parameters enforced
- Type casting applied (number, date, etc.)

### 4. Audit Logging
- All executions logged with user ID, timestamp, and parameters
- Failed executions logged with error details
- Sensitive parameters redacted in logs

## Parameter Types

### Static Values
Parameters with predefined options stored as JSON:
```json
{
  "values": [
    {"value": "1", "label": "Option 1"},
    {"value": "2", "label": "Option 2"}
  ]
}
```

### Dynamic Query
Parameters with values from database queries:
```json
{
  "query": "SELECT id, name FROM classes WHERE session_id = :session ORDER BY name"
}
```

### Dependent Parameters
Child parameters that depend on parent selection:
- Set `parent_id` to reference parent parameter
- Use `:parent_parameter_name` in query
- Values loaded dynamically when parent changes

## Example Workflow

### 1. Frontend Loads Report
```
GET /api/teacher/reports/1/parameters
```
Receives all parameters with initial dropdown values

### 2. User Selects Parent Parameter
```
POST /api/teacher/reports/parameters/3/dependent-values
Body: {"parent_value": "1"}
```
Receives filtered values for dependent dropdown

### 3. User Executes Report
```
POST /api/teacher/reports/1/execute
Body: {"parameters": {"session": "1", "grade": "10", ...}}
```
Receives formatted report results

### 4. User Exports Results
```
POST /api/teacher/reports/1/export/excel
Body: {"parameters": {"session": "1", "grade": "10", ...}}
```
Downloads Excel file

## Error Handling

### Common Error Responses

**Report Not Found (404):**
```json
{
  "success": false,
  "message": "Report not found"
}
```

**Permission Denied (403):**
```json
{
  "success": false,
  "message": "You do not have permission to access this report"
}
```

**Validation Error (422):**
```json
{
  "success": false,
  "message": "Parameter validation failed: {...}",
  "errors": {
    "session": ["Session is required"],
    "grade": ["Grade must be a number"]
  }
}
```

**Execution Error (500):**
```json
{
  "success": false,
  "message": "Failed to execute report",
  "error": "Stored procedure GetStudentListReport does not exist"
}
```

## Performance Considerations

### Caching
- Independent parameter values cached for 1 hour
- Dependent parameter values not cached
- Cache cleared when metadata changes

### Query Optimization
- Stored procedures should have proper indexing
- Limit result sets where possible
- Use pagination for large datasets

### Timeouts
- Database query timeout: 30 seconds
- Export generation timeout: 60 seconds
- API request timeout: 120 seconds

## Testing

### Example cURL Commands

**List Reports:**
```bash
curl -X GET "http://localhost/api/teacher/reports" \
  -H "Authorization: Bearer YOUR_TOKEN"
```

**Execute Report:**
```bash
curl -X POST "http://localhost/api/teacher/reports/1/execute" \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{"parameters": {"session": "1", "grade": "10"}}'
```

**Export to Excel:**
```bash
curl -X POST "http://localhost/api/teacher/reports/1/export/excel" \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{"parameters": {"session": "1"}}' \
  --output report.xlsx
```

## Implementation Files

### Created Files
1. `/app/Models/ReportCenter.php` - Report model (already existed)
2. `/app/Models/ReportParameter.php` - Parameter model (already existed)
3. `/app/Models/ReportCategory.php` - Category model (already existed)
4. `/app/Repositories/Report/ReportRepository.php` - Data access layer
5. `/app/Services/Report/ReportExecutionService.php` - Execution logic
6. `/app/Services/Report/DependentParameterService.php` - Dependency handling
7. `/app/Http/Controllers/ReportController.php` - API controller
8. `/app/Http/Requests/Report/ExecuteReportRequest.php` - Validation
9. `/app/Http/Requests/Report/ExportReportRequest.php` - Validation
10. `/resources/views/reports/pdf-export.blade.php` - PDF template
11. `/routes/api.php` - API routes (updated)

### Key Features Implemented
- Metadata-driven parameter generation
- Cascading dependent dropdowns
- SQL injection prevention via parameterized queries
- Role-based access control
- Multi-format export (Excel, PDF, CSV)
- Comprehensive error handling
- Audit logging
- Parameter value caching
- Type validation and casting

## Future Enhancements

1. **Report Scheduling** - Automated report execution
2. **Report Subscriptions** - Email delivery
3. **Custom Visualizations** - Chart rendering for 'chart' type
4. **Report Builder UI** - Visual report configuration
5. **Advanced Filtering** - Client-side filtering on results
6. **Pagination** - For large result sets
7. **Report Versioning** - Track metadata changes
8. **Performance Monitoring** - Execution time tracking
9. **Report Sharing** - Share reports with other users
10. **Saved Filters** - Save common parameter combinations

## Support

For issues or questions:
- Check error logs in `storage/logs/laravel.log`
- Review database query logs for stored procedure errors
- Verify parameter metadata in `report_parameters` table
- Ensure stored procedures exist and have correct parameter order
