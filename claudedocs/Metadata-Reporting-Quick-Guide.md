# Metadata-Driven Reporting System - Developer Guide

## Overview

This system allows you to create dynamic reports by inserting metadata into database tables. The UI auto-generates forms, validates inputs, executes stored procedures, and displays results - **no frontend coding required**.

---

## Quick Start: Adding a New Report

### Step 1: Create Your Stored Procedure

```sql
-- Example: Student Attendance Summary Report
DELIMITER $$
CREATE PROCEDURE GetStudentAttendanceSummary(
    IN p_start_date DATE,
    IN p_end_date DATE,
    IN p_class_id BIGINT,
    IN p_section_id BIGINT
)
BEGIN
    SELECT
        s.id,
        s.enrollment_number,
        CONCAT(s.first_name, ' ', s.last_name) AS student_name,
        c.name AS class_name,
        sec.name AS section_name,
        COUNT(CASE WHEN a.status = 'present' THEN 1 END) AS present_days,
        COUNT(CASE WHEN a.status = 'absent' THEN 1 END) AS absent_days,
        ROUND((COUNT(CASE WHEN a.status = 'present' THEN 1 END) / COUNT(*)) * 100, 2) AS attendance_percentage
    FROM students s
    JOIN classes c ON s.class_id = c.id
    JOIN sections sec ON s.section_id = sec.id
    LEFT JOIN attendance_records a ON a.student_id = s.id
        AND a.attendance_date BETWEEN p_start_date AND p_end_date
    WHERE (p_class_id IS NULL OR s.class_id = p_class_id)
      AND (p_section_id IS NULL OR s.section_id = p_section_id)
    GROUP BY s.id, c.name, sec.name
    ORDER BY s.first_name, s.last_name;
END$$
DELIMITER ;
```

### Step 2: Register the Report

```sql
-- Get category ID (or create one)
SET @category_id = (SELECT id FROM report_category WHERE name = 'Attendance Reports');

-- If category doesn't exist, create it
INSERT IGNORE INTO report_category (name, module, display_order)
VALUES ('Attendance Reports', 'Academic', 3);
SET @category_id = LAST_INSERT_ID();

-- Register the report
INSERT INTO report_center (
    name,
    description,
    module,
    category_id,
    procedure_name,
    report_type,
    export_enabled,
    export_formats,
    status,
    display_order
) VALUES (
    'Student Attendance Summary',
    'Summary of student attendance with present/absent days and percentages',
    'Academic',
    @category_id,
    'GetStudentAttendanceSummary',  -- Must match stored procedure name exactly
    'tabular',                       -- Type: tabular, summary, chart, or custom
    1,                               -- Export enabled
    '["excel", "pdf", "csv"]',       -- Available export formats
    1,                               -- Active status
    1                                -- Display order
);

SET @report_id = LAST_INSERT_ID();
```

### Step 3: Define Parameters

```sql
-- Add parameters (must match stored procedure parameters exactly)
INSERT INTO report_parameters (
    report_id,
    name,           -- Must match procedure parameter name
    label,          -- Display label in UI
    type,           -- UI component type
    value_type,     -- Data type for validation
    default_value,  -- Pre-filled value
    is_required,    -- Validation
    display_order,  -- Order in form
    placeholder,    -- Input placeholder
    help_text,      -- Tooltip/help text
    width           -- UI width: full, half, third
) VALUES
-- Start Date Parameter
(@report_id, 'p_start_date', 'Start Date', 'date', 'date',
 DATE_SUB(CURDATE(), INTERVAL 30 DAY), 1, 1,
 'Select start date', 'Beginning of attendance period', 'half'),

-- End Date Parameter
(@report_id, 'p_end_date', 'End Date', 'date', 'date',
 CURDATE(), 1, 2,
 'Select end date', 'End of attendance period', 'half'),

-- Class Dropdown (dynamic values from database)
(@report_id, 'p_class_id', 'Class', 'select', 'int',
 NULL, 0, 3,
 'All classes', 'Filter by specific class (optional)', 'half'),

-- Section Dropdown (depends on Class selection)
(@report_id, 'p_section_id', 'Section', 'select', 'int',
 NULL, 0, 4,
 'All sections', 'Filter by section (optional)', 'half');
```

### Step 4: Configure Dropdown Data Sources

```sql
-- Class Dropdown: Load from database
UPDATE report_parameters
SET `values` = JSON_OBJECT(
    'source', 'query',
    'query', 'SELECT id AS value, name AS label FROM classes WHERE status = 1 ORDER BY name'
)
WHERE report_id = @report_id AND name = 'p_class_id';

-- Section Dropdown: Dependent on Class (cascading)
-- First get the class parameter ID for parent relationship
SELECT id INTO @class_param_id
FROM report_parameters
WHERE report_id = @report_id AND name = 'p_class_id';

UPDATE report_parameters
SET `values` = JSON_OBJECT(
        'source', 'query',
        'query', 'SELECT id AS value, name AS label FROM sections WHERE (:p_class_id IS NULL OR class_id = :p_class_id) AND status = 1 ORDER BY name',
        'depends_on', 'p_class_id'  -- Cascading dependency
    ),
    parent_id = @class_param_id
WHERE report_id = @report_id AND name = 'p_section_id';
```

**Done!** The report is now available in Report Center with auto-generated UI.

---

## Database Schema

### 1. `report_center` - Report Registry

| Column | Type | Description |
|--------|------|-------------|
| `id` | INT | Primary key |
| `name` | VARCHAR(255) | Report display name |
| `description` | TEXT | Report description |
| `module` | VARCHAR(100) | Module: Academic, Finance, HR, etc. |
| `category_id` | INT | FK to report_category |
| `procedure_name` | VARCHAR(255) | **Stored procedure name** |
| `report_type` | VARCHAR(50) | `tabular`, `summary`, `chart`, `custom` |
| `export_enabled` | TINYINT | Enable/disable exports |
| `export_formats` | JSON | `["excel", "pdf", "csv"]` |
| `status` | TINYINT | 1=Active, 0=Inactive |
| `roles` | JSON | Allowed roles (optional) |
| `display_order` | INT | Sort order |

### 2. `report_category` - Report Grouping

| Column | Type | Description |
|--------|------|-------------|
| `id` | INT | Primary key |
| `name` | VARCHAR(255) | Category name |
| `module` | VARCHAR(100) | Module grouping |
| `icon` | VARCHAR(100) | Icon class (optional) |
| `display_order` | INT | Sort order |

### 3. `report_parameters` - Dynamic Parameters

| Column | Type | Description |
|--------|------|-------------|
| `id` | INT | Primary key |
| `report_id` | INT | FK to report_center |
| `name` | VARCHAR(100) | **Must match stored proc param** |
| `label` | VARCHAR(255) | UI display label |
| `type` | ENUM | `text`, `number`, `date`, `datetime`, `select`, `multiselect`, `checkbox`, `radio` |
| `value_type` | VARCHAR(50) | Data type: `int`, `string`, `date`, `float`, etc. |
| `values` | TEXT/JSON | Static values or dynamic query |
| `default_value` | VARCHAR(255) | Pre-filled value |
| `parent_id` | INT | For cascading dropdowns |
| `is_required` | TINYINT | Validation |
| `display_order` | INT | Form field order |
| `placeholder` | VARCHAR(255) | Input placeholder |
| `help_text` | TEXT | Tooltip text |
| `width` | VARCHAR(20) | `full`, `half`, `third` |
| `validation_rules` | JSON | Laravel validation rules |

---

## Parameter Types & Examples

### 1. Text Input
```sql
INSERT INTO report_parameters (report_id, name, label, type, value_type, is_required, display_order)
VALUES (@report_id, 'p_student_name', 'Student Name', 'text', 'string', 0, 1);
```

### 2. Number Input
```sql
INSERT INTO report_parameters (report_id, name, label, type, value_type, default_value, is_required, display_order)
VALUES (@report_id, 'p_min_score', 'Minimum Score', 'number', 'int', '50', 0, 2);
```

### 3. Date Picker
```sql
INSERT INTO report_parameters (report_id, name, label, type, value_type, default_value, is_required, display_order)
VALUES (@report_id, 'p_exam_date', 'Exam Date', 'date', 'date', CURDATE(), 1, 3);
```

### 4. Static Dropdown
```sql
-- Insert parameter
INSERT INTO report_parameters (report_id, name, label, type, value_type, is_required, display_order)
VALUES (@report_id, 'p_gender', 'Gender', 'select', 'string', 0, 4);

-- Set static values
UPDATE report_parameters
SET `values` = JSON_ARRAY(
    JSON_OBJECT('value', 'male', 'label', 'Male'),
    JSON_OBJECT('value', 'female', 'label', 'Female'),
    JSON_OBJECT('value', 'other', 'label', 'Other')
)
WHERE report_id = @report_id AND name = 'p_gender';
```

### 5. Dynamic Dropdown (Database Query)
```sql
-- Insert parameter
INSERT INTO report_parameters (report_id, name, label, type, value_type, is_required, display_order)
VALUES (@report_id, 'p_teacher_id', 'Teacher', 'select', 'int', 0, 5);

-- Set dynamic query
UPDATE report_parameters
SET `values` = JSON_OBJECT(
    'source', 'query',
    'query', 'SELECT id AS value, CONCAT(first_name, " ", last_name) AS label FROM teachers WHERE status = 1 ORDER BY first_name'
)
WHERE report_id = @report_id AND name = 'p_teacher_id';
```

### 6. Cascading Dropdown (Parent-Child Dependency)
```sql
-- Parent: Class
INSERT INTO report_parameters (report_id, name, label, type, value_type, is_required, display_order)
VALUES (@report_id, 'p_class_id', 'Class', 'select', 'int', 1, 6);

UPDATE report_parameters
SET `values` = JSON_OBJECT(
    'source', 'query',
    'query', 'SELECT id AS value, name AS label FROM classes WHERE status = 1 ORDER BY name'
)
WHERE report_id = @report_id AND name = 'p_class_id';

-- Child: Section (depends on Class)
INSERT INTO report_parameters (report_id, name, label, type, value_type, is_required, display_order)
VALUES (@report_id, 'p_section_id', 'Section', 'select', 'int', 0, 7);

-- Get parent parameter ID
SELECT id INTO @class_param_id FROM report_parameters WHERE report_id = @report_id AND name = 'p_class_id';

-- Set dependent query with :p_class_id placeholder
UPDATE report_parameters
SET `values` = JSON_OBJECT(
        'source', 'query',
        'query', 'SELECT id AS value, name AS label FROM sections WHERE (:p_class_id IS NULL OR class_id = :p_class_id) AND status = 1 ORDER BY name',
        'depends_on', 'p_class_id'
    ),
    parent_id = @class_param_id
WHERE report_id = @report_id AND name = 'p_section_id';
```

### 7. Multi-Select
```sql
INSERT INTO report_parameters (report_id, name, label, type, value_type, is_required, display_order)
VALUES (@report_id, 'p_subjects', 'Subjects', 'multiselect', 'array', 0, 8);

UPDATE report_parameters
SET `values` = JSON_OBJECT(
    'source', 'query',
    'query', 'SELECT id AS value, name AS label FROM subjects WHERE status = 1 ORDER BY name'
)
WHERE report_id = @report_id AND name = 'p_subjects';
```

### 8. Checkbox
```sql
INSERT INTO report_parameters (report_id, name, label, type, value_type, default_value, is_required, display_order)
VALUES (@report_id, 'p_include_inactive', 'Include Inactive Students', 'checkbox', 'boolean', '0', 0, 9);
```

---

## How It Works (Architecture Overview)

### 1. Frontend Flow (Auto-Generated UI)

```
User Opens Report Center
    ↓
JavaScript fetches reports grouped by category
    ↓
User selects report → Fetch parameters from report_parameters table
    ↓
DynamicReportForm.js auto-generates form based on parameter metadata
    ↓
User fills form → Cascading dropdowns fetch dependent values
    ↓
User clicks "Generate Report" → Validation → Execute
    ↓
Results displayed in table with export buttons
```

**Key File**: `resources/js/components/DynamicReportForm.js` (1733 lines)
- Renders all parameter types dynamically
- Handles cascading dropdowns
- Executes reports via API
- Displays results (tabular/summary/chart)
- Exports to Excel/PDF/CSV

### 2. Backend Flow (Report Execution)

```
POST /api/reports/{reportId}/execute
    ↓
ReportController@execute → Validate parameters
    ↓
ReportExecutionService@executeReport
    ↓
Fetch report metadata from report_center
    ↓
Prepare parameters → Match with stored procedure signature
    ↓
Execute: CALL StoredProcedureName(param1, param2, ...)
    ↓
Transform results based on report_type (tabular/summary)
    ↓
Calculate summaries if needed
    ↓
Return JSON: { columns: [], rows: [], summary: {}, meta: {} }
```

**Key Files**:
- `app/Http/Controllers/ReportController.php` - API endpoints
- `app/Services/Report/ReportExecutionService.php` - Core execution engine
- `app/Repositories/Report/ReportRepository.php` - Database access

### 3. Parameter Resolution (Cascading Dropdowns)

```
User selects "Class A" from Class dropdown
    ↓
Frontend detects change → Finds dependent parameters (Section)
    ↓
GET /api/reports/parameters/{sectionParamId}/dependent-values?parent_value=1
    ↓
Backend executes query:
  SELECT id AS value, name AS label
  FROM sections
  WHERE class_id = 1
  ORDER BY name
    ↓
Returns options → Frontend populates Section dropdown
```

---

## Report Types

### 1. `tabular` - Standard Table Results
- Returns rows and columns
- Auto-generates table UI
- Supports sorting/filtering
- Best for: Student lists, attendance records, transaction logs

**Stored Procedure Output**: Any SELECT query returning rows

### 2. `summary` - Financial/Statistical Summary
- Returns summary data with sections
- Auto-calculates totals
- Displays hierarchical data
- Best for: Fee collection, exam results, financial reports

**Stored Procedure Output**:
- Rows with `section_name` column for grouping
- Numeric columns for auto-totaling

### 3. `chart` - Visual Charts
- Returns data for charts (pie, bar, line)
- Auto-renders charts
- Best for: Trends, distributions, comparisons

**Stored Procedure Output**: `label` and `value` columns

### 4. `custom` - Fully Custom
- Returns raw data
- Requires custom frontend rendering
- Best for: Complex layouts, specialized reports

---

## Export Functionality

All reports with `export_enabled = 1` automatically support:

### Excel Export
```javascript
// Frontend automatically provides button
// Backend uses Maatwebsite/Laravel-Excel
```

### PDF Export
```javascript
// Uses DomPDF with custom templates
// Template: resources/views/reports/pdf/template.blade.php
```

### CSV Export
```javascript
// Simple comma-separated values
// Direct download, no template
```

### Print
```javascript
// Browser print with identical PDF layout
// Uses print.css for optimal formatting
```

**Configuration**:
```sql
UPDATE report_center
SET export_enabled = 1,
    export_formats = '["excel", "pdf", "csv"]'
WHERE id = @report_id;
```

---

## Common Patterns

### Pattern 1: Date Range Report
```sql
-- Parameters
INSERT INTO report_parameters (report_id, name, label, type, value_type, default_value, is_required, display_order, width)
VALUES
(@report_id, 'p_start_date', 'From Date', 'date', 'date', DATE_SUB(CURDATE(), INTERVAL 30 DAY), 1, 1, 'half'),
(@report_id, 'p_end_date', 'To Date', 'date', 'date', CURDATE(), 1, 2, 'half');

-- Stored Procedure
CREATE PROCEDURE MyReport(IN p_start_date DATE, IN p_end_date DATE)
BEGIN
    SELECT * FROM transactions
    WHERE transaction_date BETWEEN p_start_date AND p_end_date;
END;
```

### Pattern 2: Class → Section → Student Cascade
```sql
-- 1. Class
INSERT INTO report_parameters (report_id, name, label, type, value_type, is_required, display_order)
VALUES (@report_id, 'p_class_id', 'Class', 'select', 'int', 1, 1);

UPDATE report_parameters
SET `values` = JSON_OBJECT('source', 'query', 'query', 'SELECT id AS value, name AS label FROM classes WHERE status = 1')
WHERE report_id = @report_id AND name = 'p_class_id';

-- 2. Section (depends on Class)
INSERT INTO report_parameters (report_id, name, label, type, value_type, is_required, display_order)
VALUES (@report_id, 'p_section_id', 'Section', 'select', 'int', 0, 2);

SELECT id INTO @class_param_id FROM report_parameters WHERE report_id = @report_id AND name = 'p_class_id';

UPDATE report_parameters
SET `values` = JSON_OBJECT(
        'source', 'query',
        'query', 'SELECT id AS value, name AS label FROM sections WHERE (:p_class_id IS NULL OR class_id = :p_class_id)',
        'depends_on', 'p_class_id'
    ),
    parent_id = @class_param_id
WHERE report_id = @report_id AND name = 'p_section_id';

-- 3. Student (depends on Section)
INSERT INTO report_parameters (report_id, name, label, type, value_type, is_required, display_order)
VALUES (@report_id, 'p_student_id', 'Student', 'select', 'int', 0, 3);

SELECT id INTO @section_param_id FROM report_parameters WHERE report_id = @report_id AND name = 'p_section_id';

UPDATE report_parameters
SET `values` = JSON_OBJECT(
        'source', 'query',
        'query', 'SELECT id AS value, CONCAT(first_name, " ", last_name) AS label FROM students WHERE (:p_section_id IS NULL OR section_id = :p_section_id)',
        'depends_on', 'p_section_id'
    ),
    parent_id = @section_param_id
WHERE report_id = @report_id AND name = 'p_student_id';
```

### Pattern 3: Optional Filters
```sql
-- Make parameter optional (is_required = 0) and handle NULL in stored procedure
CREATE PROCEDURE MyReport(IN p_optional_filter INT)
BEGIN
    SELECT * FROM students
    WHERE (p_optional_filter IS NULL OR category_id = p_optional_filter);
END;
```

---

## Best Practices

### ✅ DO:
1. **Match parameter names exactly**: `report_parameters.name` must match stored procedure parameter
2. **Use descriptive labels**: Users see `label`, not `name`
3. **Set sensible defaults**: Pre-fill common values (e.g., current date)
4. **Make optional parameters nullable**: Handle NULL in stored procedures
5. **Order parameters logically**: Use `display_order` for intuitive forms
6. **Add help text**: Guide users with `help_text` and `placeholder`
7. **Test queries**: Verify dropdown queries return `value` and `label` columns
8. **Use `display_order`**: Control parameter sequence in UI
9. **Set appropriate widths**: `full`, `half`, `third` for clean layouts

### ❌ DON'T:
1. **Don't hardcode values**: Use dynamic queries for changing data
2. **Don't skip validation**: Set `is_required` appropriately
3. **Don't use complex queries in dropdowns**: Keep them simple and fast
4. **Don't forget indexes**: Index columns used in dropdown queries
5. **Don't expose sensitive data**: Check role permissions
6. **Don't return too many rows**: Paginate or filter large datasets
7. **Don't skip default values**: Provide sensible defaults where possible

---

## Troubleshooting

### Issue: Report doesn't appear in Report Center
**Solution**:
- Check `report_center.status = 1`
- Verify `category_id` exists in `report_category`
- Clear cache: `php artisan cache:clear`

### Issue: Parameter dropdown is empty
**Solution**:
- Test the query directly in MySQL
- Ensure query returns `value` and `label` columns
- Check for SQL syntax errors
- Verify table/column names are correct

### Issue: Cascading dropdown not working
**Solution**:
- Verify `parent_id` is set correctly
- Check `depends_on` matches parent parameter `name`
- Ensure query uses `:parent_param_name` placeholder syntax
- Test parent-child relationship manually

### Issue: Stored procedure error
**Solution**:
- Verify `procedure_name` matches exactly (case-sensitive)
- Check parameter count and types match
- Test procedure manually: `CALL ProcedureName(param1, param2);`
- Review Laravel logs: `storage/logs/laravel.log`

### Issue: Export fails
**Solution**:
- Check `export_enabled = 1`
- Verify format is in `export_formats` JSON array
- Ensure sufficient disk space
- Check file permissions on `storage/app/exports/`

---

## API Endpoints Reference

| Method | Endpoint | Description |
|--------|----------|-------------|
| `GET` | `/api/reports` | List all reports grouped by category |
| `GET` | `/api/reports/{id}` | Get report details with parameters |
| `GET` | `/api/reports/{id}/parameters` | Get report parameters only |
| `GET` | `/api/reports/parameters/{paramId}/dependent-values` | Get cascading values |
| `POST` | `/api/reports/{id}/execute` | Execute report with parameters |
| `POST` | `/api/reports/{id}/export` | Export report (Excel/PDF/CSV) |
| `GET` | `/api/reports/{id}/print` | Get printable HTML |
| `GET` | `/api/reports/categories` | Get all categories |

---

## Complete Example: Unpaid Students Report

```sql
-- 1. Create Stored Procedure
DELIMITER $$
CREATE PROCEDURE GetUnpaidStudentsReport(
    IN p_start_date DATE,
    IN p_end_date DATE,
    IN p_class_id BIGINT,
    IN p_section_id BIGINT
)
BEGIN
    SELECT
        s.id,
        s.enrollment_number,
        CONCAT(s.first_name, ' ', s.last_name) AS student_name,
        c.name AS class_name,
        sec.name AS section_name,
        s.guardian_phone,
        COALESCE(SUM(fi.amount), 0) AS total_fees,
        COALESCE(SUM(p.amount), 0) AS paid_amount,
        COALESCE(SUM(fi.amount), 0) - COALESCE(SUM(p.amount), 0) AS balance_due
    FROM students s
    JOIN classes c ON s.class_id = c.id
    JOIN sections sec ON s.section_id = sec.id
    LEFT JOIN fee_invoices fi ON fi.student_id = s.id
        AND fi.invoice_date BETWEEN p_start_date AND p_end_date
    LEFT JOIN payments p ON p.student_id = s.id
        AND p.payment_date BETWEEN p_start_date AND p_end_date
    WHERE (p_class_id IS NULL OR s.class_id = p_class_id)
      AND (p_section_id IS NULL OR s.section_id = p_section_id)
    GROUP BY s.id, c.name, sec.name, s.guardian_phone
    HAVING balance_due > 0
    ORDER BY balance_due DESC;
END$$
DELIMITER ;

-- 2. Create/Get Category
INSERT IGNORE INTO report_category (name, module, display_order)
VALUES ('Financial Reports', 'Finance', 2);
SET @category_id = (SELECT id FROM report_category WHERE name = 'Financial Reports');

-- 3. Register Report
INSERT INTO report_center (
    name, description, module, category_id, procedure_name,
    report_type, export_enabled, export_formats, status, display_order
) VALUES (
    'Unpaid Students Report',
    'Students with outstanding fee balances for selected period',
    'Finance', @category_id, 'GetUnpaidStudentsReport',
    'tabular', 1, '["excel", "pdf", "csv"]', 1, 1
);
SET @report_id = LAST_INSERT_ID();

-- 4. Add Parameters
INSERT INTO report_parameters (
    report_id, name, label, type, value_type, default_value,
    is_required, display_order, placeholder, width
) VALUES
(@report_id, 'p_start_date', 'From Date', 'date', 'date', DATE_SUB(CURDATE(), INTERVAL 30 DAY), 1, 1, 'Start date', 'half'),
(@report_id, 'p_end_date', 'To Date', 'date', 'date', CURDATE(), 1, 2, 'End date', 'half'),
(@report_id, 'p_class_id', 'Class', 'select', 'int', NULL, 0, 3, 'All classes', 'half'),
(@report_id, 'p_section_id', 'Section', 'select', 'int', NULL, 0, 4, 'All sections', 'half');

-- 5. Configure Dropdowns
UPDATE report_parameters
SET `values` = JSON_OBJECT(
    'source', 'query',
    'query', 'SELECT id AS value, name AS label FROM classes WHERE status = 1 ORDER BY name'
)
WHERE report_id = @report_id AND name = 'p_class_id';

SELECT id INTO @class_param_id FROM report_parameters WHERE report_id = @report_id AND name = 'p_class_id';

UPDATE report_parameters
SET `values` = JSON_OBJECT(
        'source', 'query',
        'query', 'SELECT id AS value, name AS label FROM sections WHERE (:p_class_id IS NULL OR class_id = :p_class_id) AND status = 1 ORDER BY name',
        'depends_on', 'p_class_id'
    ),
    parent_id = @class_param_id
WHERE report_id = @report_id AND name = 'p_section_id';

-- ✅ DONE! Report is now live in Report Center
```

---

## Summary

This metadata-driven system eliminates the need to write:
- ❌ Frontend forms and validation
- ❌ API controllers for each report
- ❌ Export logic (Excel/PDF/CSV)
- ❌ Print functionality
- ❌ Parameter dependency handling

**You only need**:
- ✅ Write stored procedure (business logic)
- ✅ Insert metadata (3 SQL statements)
- ✅ Configure parameter sources (dropdowns)

**The system auto-generates**:
- ✅ Dynamic form UI
- ✅ Validation and error handling
- ✅ Cascading dropdowns
- ✅ Report execution
- ✅ Export buttons (Excel/PDF/CSV)
- ✅ Print functionality
- ✅ Results display

---

## Need Help?

**Common Files**:
- Backend: `app/Http/Controllers/ReportController.php`
- Service: `app/Services/Report/ReportExecutionService.php`
- Frontend: `resources/js/components/DynamicReportForm.js`
- Views: `resources/views/reports/index.blade.php`

**Reference Implementation**: See `refactor-plan.txt` for examples of existing reports.
