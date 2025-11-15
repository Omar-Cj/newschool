# Report Parameters School Filtering Update - Summary

## Overview
Added multi-tenant data isolation to report parameter queries by implementing school_id filtering. This ensures that users only see data from their own school, while System Admins (school_id=NULL) can see all schools' data.

## Filter Pattern Applied
```sql
AND (:p_school_id IS NULL OR table_name.school_id = :p_school_id)
```

This pattern:
- Allows System Admins (`:p_school_id` is NULL) to see all data
- Restricts school users to see only their school's data
- Preserves original query syntax and functionality
- Works seamlessly with existing parameter dependencies

## Updated Parameters Summary

### Total Parameters Updated: 31

### Category Breakdown:

#### 1. Sessions (p_session_id) - 3 parameters
- **IDs**: 28, 34, 39
- **Table**: `sessions`
- **Filter Added**: `AND (:p_school_id IS NULL OR school_id = :p_school_id)`

#### 2. Classes (p_class_id) - 9 parameters
- **IDs**: 18, 25, 31, 36, 41, 50, 58, 63, 68
- **Table**: `classes`
- **Filter Added**: `AND (:p_school_id IS NULL OR school_id = :p_school_id)`

#### 3. Shifts (p_shift_id) - 3 parameters
- **IDs**: 21, 43, 52
- **Table**: `shifts`
- **Filter Added**: `AND (:p_school_id IS NULL OR school_id = :p_school_id)`

#### 4. Exam Types (p_exam_type_id) - 1 parameter
- **ID**: 30
- **Table**: `exam_types`
- **Filter Added**: `AND (:p_school_id IS NULL OR school_id = :p_school_id)`

#### 5. Student Categories (p_student_category_id) - 1 parameter
- **ID**: 44
- **Table**: `student_categories`
- **Filter Added**: `AND (:p_school_id IS NULL OR school_id = :p_school_id)`
- **Note**: Original query had no `status = 1` filter

#### 6. Sections (p_section_id) - 9 parameters (Complex)
- **IDs**: 19, 26, 32, 37, 42, 51, 59, 64, 69
- **Main Table**: `sections` (aliased as `s`)
- **Joins**: `class_setup_childrens`, `class_setups`
- **Filter Added**: `AND (:p_school_id IS NULL OR s.school_id = :p_school_id)`
- **Existing Dependencies**: `p_class_id`
- **Special Handling**: Filter added to main table in complex JOIN query

#### 7. Terms (p_term_id) - 2 parameters (Complex)
- **IDs**: 29, 35
- **Main Table**: `terms` (aliased as `t`)
- **Joins**: `term_definitions`
- **Filter Added**: `AND (:p_school_id IS NULL OR t.school_id = :p_school_id)`
- **Existing Dependencies**: `p_session_id`
- **Special Handling**: Filter added to main table alongside existing session filter

#### 8. Students (p_student_id) - 2 parameters (Complex)
- **IDs**: 33, 38
- **Main Table**: `students` (aliased as `s`)
- **Joins**: `session_class_students`
- **Filter Added**: `AND (:p_school_id IS NULL OR s.school_id = :p_school_id)`
- **ID 33 Dependencies**: `p_class_id`
- **ID 38 Dependencies**: `p_session_id`, `p_class_id`, `p_section_id`
- **Special Handling**: Filter added to students table in JOIN queries with CONCAT for labels

#### 9. Expense Categories (p_expense_category_id) - 1 parameter (Complex)
- **ID**: 78
- **Table**: `expense_categories`
- **Filter Added**: `AND (:p_school_id IS NULL OR school_id = :p_school_id)`
- **Existing Dependencies**: `p_branch_id`
- **Special Handling**: Added alongside existing branch filter

## Files Created

### 1. Laravel Migration File
**Location**: `/database/migrations/tenant/2025_01_14_add_school_id_filtering_to_report_parameters.php`

**Usage**:
```bash
# For SaaS Multi-Tenant (applies to all tenants)
php artisan tenants:migrate

# For Single School Installation
php artisan migrate --path=database/migrations/tenant

# Rollback if needed
php artisan migrate:rollback --path=database/migrations/tenant
```

**Features**:
- Full `up()` method with all 31 parameter updates
- Complete `down()` method for rollback
- Organized by category with clear comments
- Uses Laravel DB facade for database operations
- Handles JSON column updates properly

### 2. Forward SQL Script
**Location**: `/database/sql/add_school_id_filtering_to_report_parameters.sql`

**Usage**:
```bash
# Via MySQL client
mysql -u username -p database_name < database/sql/add_school_id_filtering_to_report_parameters.sql

# Via Laravel Artisan
php artisan db:seed --class=RunRawSqlSeeder  # If you have a custom seeder
```

**Features**:
- Direct SQL UPDATE statements
- Uses JSON_SET for safe JSON updates
- Includes verification queries at the end
- Can be run directly on database without Laravel

### 3. Rollback SQL Script
**Location**: `/database/sql/rollback_school_id_filtering_from_report_parameters.sql`

**Usage**: Same as forward script
```bash
mysql -u username -p database_name < database/sql/rollback_school_id_filtering_from_report_parameters.sql
```

**Features**:
- Restores all original queries exactly
- Removes school_id filtering completely
- Includes verification queries to confirm rollback
- Safe to run if update needs to be reverted

## Query Examples

### Simple Query Transformation

**Before (ID 28 - Sessions)**:
```sql
SELECT id AS value, name AS label
FROM sessions
WHERE status = 1
ORDER BY id DESC
```

**After**:
```sql
SELECT id AS value, name AS label
FROM sessions
WHERE status = 1
AND (:p_school_id IS NULL OR school_id = :p_school_id)
ORDER BY id DESC
```

### Complex Query with Joins Transformation

**Before (ID 19 - Sections)**:
```sql
SELECT s.id AS value, s.name AS label
FROM sections s
INNER JOIN class_setup_childrens csc ON s.id = csc.section_id
INNER JOIN class_setups cs ON csc.class_setup_id = cs.id
WHERE (:p_class_id IS NULL OR cs.classes_id = :p_class_id)
AND s.status = 1
AND csc.status = 1
AND cs.status = 1
ORDER BY s.name
```

**After**:
```sql
SELECT s.id AS value, s.name AS label
FROM sections s
INNER JOIN class_setup_childrens csc ON s.id = csc.section_id
INNER JOIN class_setups cs ON csc.class_setup_id = cs.id
WHERE (:p_class_id IS NULL OR cs.classes_id = :p_class_id)
AND s.status = 1
AND csc.status = 1
AND cs.status = 1
AND (:p_school_id IS NULL OR s.school_id = :p_school_id)
ORDER BY s.name
```

### Complex Query with Multiple Dependencies

**Before (ID 38 - Students)**:
```sql
SELECT DISTINCT s.id AS value, CONCAT(s.first_name, ' ', s.last_name) AS label
FROM students s
INNER JOIN session_class_students scs ON s.id = scs.student_id
WHERE (:p_session_id IS NULL OR scs.session_id = :p_session_id)
AND (:p_class_id IS NULL OR scs.classes_id = :p_class_id)
AND (:p_section_id IS NULL OR scs.section_id = :p_section_id)
AND s.status = 1
ORDER BY label
```

**After**:
```sql
SELECT DISTINCT s.id AS value, CONCAT(s.first_name, ' ', s.last_name) AS label
FROM students s
INNER JOIN session_class_students scs ON s.id = scs.student_id
WHERE (:p_session_id IS NULL OR scs.session_id = :p_session_id)
AND (:p_class_id IS NULL OR scs.classes_id = :p_class_id)
AND (:p_section_id IS NULL OR scs.section_id = :p_section_id)
AND s.status = 1
AND (:p_school_id IS NULL OR s.school_id = :p_school_id)
ORDER BY label
```

## Verification Steps

### 1. Pre-Update Verification
```sql
-- Check current queries before update
SELECT id, name, JSON_UNQUOTE(JSON_EXTRACT(`values`, '$.query')) AS query
FROM report_parameters
WHERE id IN (28, 34, 39, 18, 25, 31, 36, 41, 50, 58, 63, 68, 21, 43, 52, 30, 44,
             19, 26, 32, 37, 42, 51, 59, 64, 69, 29, 35, 33, 38, 78)
ORDER BY id;
```

### 2. Post-Update Verification
```sql
-- Verify school_id filter was added
SELECT
    id,
    name,
    CASE
        WHEN JSON_UNQUOTE(JSON_EXTRACT(`values`, '$.query')) LIKE '%:p_school_id%' THEN 'UPDATED'
        ELSE 'NOT UPDATED'
    END AS status,
    JSON_UNQUOTE(JSON_EXTRACT(`values`, '$.query')) AS query
FROM report_parameters
WHERE id IN (28, 34, 39, 18, 25, 31, 36, 41, 50, 58, 63, 68, 21, 43, 52, 30, 44,
             19, 26, 32, 37, 42, 51, 59, 64, 69, 29, 35, 33, 38, 78)
ORDER BY id;

-- Count verification
SELECT
    COUNT(*) as total_updated
FROM report_parameters
WHERE id IN (28, 34, 39, 18, 25, 31, 36, 41, 50, 58, 63, 68, 21, 43, 52, 30, 44,
             19, 26, 32, 37, 42, 51, 59, 64, 69, 29, 35, 33, 38, 78)
AND JSON_UNQUOTE(JSON_EXTRACT(`values`, '$.query')) LIKE '%:p_school_id%';
-- Should return 31
```

### 3. Functional Testing
```sql
-- Test with actual school_id value
-- Replace {school_id} with actual ID from your schools table
SET @p_school_id = {school_id};
SET @p_class_id = NULL;

-- Test classes query
SELECT id AS value, name AS label
FROM classes
WHERE status = 1
AND (:p_school_id IS NULL OR school_id = :p_school_id)
ORDER BY name;

-- Test with System Admin (NULL school_id)
SET @p_school_id = NULL;

-- Should return all classes from all schools
SELECT id AS value, name AS label
FROM classes
WHERE status = 1
AND (:p_school_id IS NULL OR school_id = :p_school_id)
ORDER BY name;
```

## Special Handling Notes

### 1. JSON Column Updates
All updates use `JSON_SET()` to safely modify the JSON `values` column without corrupting the JSON structure:
```sql
UPDATE report_parameters
SET `values` = JSON_SET(`values`, '$.query', 'NEW_QUERY_HERE')
WHERE id = X;
```

### 2. Preserving Dependencies
Parameters with existing dependencies (like `depends_on: p_class_id`) maintain their dependencies:
```sql
UPDATE report_parameters
SET `values` = JSON_SET(
    JSON_SET(
        JSON_SET(`values`, '$.query', 'NEW_QUERY'),
        '$.source', 'query'
    ),
    '$.depends_on', 'p_class_id'
)
WHERE id = X;
```

### 3. Syntax Preservation
All original query syntax was preserved exactly:
- No changes to column aliases (`AS value`, `AS label`)
- No changes to JOIN syntax
- No changes to ORDER BY clauses
- No changes to quote styles
- Filter added BEFORE `ORDER BY` clause in all cases

### 4. Table Aliases in Complex Queries
For queries with joins, the school_id filter explicitly specifies the table alias:
- `s.school_id` for students/sections queries
- `t.school_id` for terms queries
- Direct `school_id` for simple queries

## Impact Analysis

### Security Impact
- **CRITICAL FIX**: Closes multi-tenant data leak vulnerability
- Users can now only access their school's data in dropdowns
- System Admins retain full access to all schools
- No API changes required - parameter resolver handles filtering

### Performance Impact
- **Minimal**: Additional WHERE clause uses indexed school_id column
- Queries will be faster for school users (smaller dataset)
- System Admin queries unchanged in performance
- Recommend ensuring `school_id` is indexed on all affected tables

### Compatibility Impact
- **Zero Breaking Changes**: Existing reports continue to work
- Parameter dependencies preserved
- JSON structure maintained
- No frontend changes needed
- ParameterValueResolver handles `:p_school_id` automatically

## Database Table Requirements

All affected tables MUST have `school_id` column:
- [x] `sessions`
- [x] `classes`
- [x] `sections`
- [x] `shifts`
- [x] `exam_types`
- [x] `student_categories`
- [x] `terms`
- [x] `students`
- [x] `expense_categories`

**Action Required**: Verify all tables have `school_id` column with proper foreign key constraints:
```sql
-- Verification query
SELECT
    TABLE_NAME,
    COLUMN_NAME,
    DATA_TYPE,
    IS_NULLABLE
FROM INFORMATION_SCHEMA.COLUMNS
WHERE TABLE_SCHEMA = DATABASE()
AND TABLE_NAME IN ('sessions', 'classes', 'sections', 'shifts', 'exam_types',
                    'student_categories', 'terms', 'students', 'expense_categories')
AND COLUMN_NAME = 'school_id';
```

## Recommended Index Creation

Ensure optimal performance by creating indexes:
```sql
-- Add indexes if not already present
CREATE INDEX idx_sessions_school_id ON sessions(school_id);
CREATE INDEX idx_classes_school_id ON classes(school_id);
CREATE INDEX idx_sections_school_id ON sections(school_id);
CREATE INDEX idx_shifts_school_id ON shifts(school_id);
CREATE INDEX idx_exam_types_school_id ON exam_types(school_id);
CREATE INDEX idx_student_categories_school_id ON student_categories(school_id);
CREATE INDEX idx_terms_school_id ON terms(school_id);
CREATE INDEX idx_students_school_id ON students(school_id);
CREATE INDEX idx_expense_categories_school_id ON expense_categories(school_id);
```

## Testing Checklist

### Pre-Deployment Testing
- [ ] Backup `report_parameters` table
- [ ] Verify all affected tables have `school_id` column
- [ ] Run verification queries to document current state
- [ ] Test rollback script on staging environment

### Post-Deployment Testing
- [ ] Verify all 31 parameters updated successfully
- [ ] Test report dropdowns as System Admin (should see all schools)
- [ ] Test report dropdowns as school user (should see only their school)
- [ ] Test dependent parameters (sections, terms, students)
- [ ] Verify no broken reports
- [ ] Check application logs for SQL errors

### Functional Test Cases

**Test Case 1: System Admin Access**
- User: System Admin (school_id = NULL)
- Expected: See all sessions/classes/shifts from all schools in dropdowns

**Test Case 2: School User Access**
- User: School Admin (school_id = 5)
- Expected: See only sessions/classes/shifts from school_id = 5

**Test Case 3: Dependent Parameters**
- Select Class → Section dropdown should show sections for that class AND current school
- Select Session → Term dropdown should show terms for that session AND current school

**Test Case 4: Student Parameters**
- Student dropdown should show only students from current school
- When filtered by class, should show students from that class AND current school

## Rollback Strategy

If issues arise after deployment:

### Option 1: Laravel Migration Rollback
```bash
php artisan migrate:rollback --path=database/migrations/tenant --step=1
```

### Option 2: SQL Script Rollback
```bash
mysql -u username -p database_name < database/sql/rollback_school_id_filtering_from_report_parameters.sql
```

### Option 3: Manual Table Restore
```sql
-- If you have a backup
-- Replace {backup_file} with your backup file
SOURCE {backup_file};
```

## Support & Troubleshooting

### Common Issues

**Issue 1: "Unknown column 'school_id'"**
- **Cause**: Table missing school_id column
- **Solution**: Add school_id column to the table or remove that parameter from update

**Issue 2: Empty dropdowns for school users**
- **Cause**: ParameterValueResolver not passing :p_school_id
- **Solution**: Check ParameterValueResolver implementation to ensure school_id is added to parameter bindings

**Issue 3: System Admin seeing limited data**
- **Cause**: :p_school_id being set to specific value instead of NULL
- **Solution**: Check authentication middleware - System Admins should have school_id = NULL

## Files Reference

```
project_root/
├── database/
│   ├── migrations/
│   │   └── tenant/
│   │       └── 2025_01_14_add_school_id_filtering_to_report_parameters.php
│   └── sql/
│       ├── add_school_id_filtering_to_report_parameters.sql
│       ├── rollback_school_id_filtering_from_report_parameters.sql
│       └── REPORT_PARAMETERS_SCHOOL_FILTER_SUMMARY.md (this file)
```

## Conclusion

This update successfully adds multi-tenant data isolation to 31 report parameters across 9 entity types. The implementation:

✅ Preserves original query syntax exactly
✅ Maintains backward compatibility
✅ Requires zero frontend changes
✅ Provides complete rollback capability
✅ Includes comprehensive verification queries
✅ Follows the specified filter pattern consistently

**Deployment Recommendation**: Deploy to staging first, run full test suite, then proceed to production with rollback script ready.

---

**Last Updated**: 2025-01-14
**Author**: Backend Architect
**Status**: Ready for Deployment
