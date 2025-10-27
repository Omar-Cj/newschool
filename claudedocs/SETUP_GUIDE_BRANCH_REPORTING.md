# Branch-Based Reporting System - Setup Guide

**Version:** 1.0
**Date:** 2025-10-18
**Status:** Ready for Setup

---

## 📋 Table of Contents

1. [Prerequisites](#prerequisites)
2. [Setup Steps](#setup-steps)
3. [Stored Procedure Updates](#stored-procedure-updates)
4. [Testing the Implementation](#testing-the-implementation)
5. [Troubleshooting](#troubleshooting)
6. [Rollback Instructions](#rollback-instructions)

---

## ✅ Prerequisites

Before starting the setup, ensure you have:

- [x] Laravel project running (already in place)
- [x] MultiBranch module activated
- [x] All branches configured in `branches` table
- [x] Users assigned to branches (`users.branch_id`)
- [x] Database backup taken (IMPORTANT!)

---

## 🚀 Setup Steps

### Step 1: Verify Backend Files (Already Completed ✅)

The following files have been created/updated:

**New Files:**
- ✅ `app/Services/Report/BranchParameterService.php`
- ✅ `app/Http/Middleware/CheckBranchAccess.php`
- ✅ `database/migrations/2025_10_18_053259_add_branch_parameter_to_report_stored_procedures.php`

**Modified Files:**
- ✅ `app/Services/Report/ReportExecutionService.php`
- ✅ `app/Services/Report/DependentParameterService.php`
- ✅ `app/Http/Kernel.php`
- ✅ `routes/api.php`
- ✅ `routes/reports.php`

### Step 2: Clear Application Cache

Run these commands to clear cached configurations:

```bash
# Clear all caches
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

# Rebuild optimizations
php artisan config:cache
php artisan route:cache
```

### Step 3: Update Stored Procedures (REQUIRED)

This is the **most important step**. You need to update ALL your report stored procedures.

#### 3.1 List Your Report Procedures

First, find all stored procedures used in your reports:

```sql
-- Connect to your database and run:
SELECT routine_name
FROM information_schema.routines
WHERE routine_type = 'PROCEDURE'
  AND routine_schema = DATABASE()
  AND routine_name LIKE '%report%';
```

#### 3.2 Update the Migration File

Edit `database/migrations/2025_10_18_053259_add_branch_parameter_to_report_stored_procedures.php`:

1. **Add your procedure names** to the `$reportProcedures` array:
```php
private array $reportProcedures = [
    'sp_student_attendance_report',
    'sp_fee_collection_report',
    'sp_examination_results_report',
    'GetStudentGradebook',
    'GetPaidStudentsReport',
    'GetFeeGenerationReport',
    // ... add ALL your report procedures
];
```

2. **Create update methods** for each procedure following the pattern in the migration file.

#### 3.3 Stored Procedure Update Pattern

For **EACH** of your stored procedures, follow this pattern:

**BEFORE (Example):**
```sql
CREATE PROCEDURE sp_fee_collection_report(
    IN p_start_date DATE,
    IN p_end_date DATE,
    IN p_class_id INT
)
BEGIN
    SELECT
        s.id,
        s.name,
        SUM(f.amount) as total_fees
    FROM students s
    LEFT JOIN fee_payments f ON f.student_id = s.id
        AND f.payment_date BETWEEN p_start_date AND p_end_date
    WHERE s.class_id = p_class_id
    GROUP BY s.id;
END;
```

**AFTER (With Branch Parameter):**
```sql
CREATE PROCEDURE sp_fee_collection_report(
    IN p_start_date DATE,
    IN p_end_date DATE,
    IN p_class_id INT,
    IN p_branch_id INT  -- ← ADD THIS (always last)
)
BEGIN
    SELECT
        s.id,
        s.name,
        SUM(f.amount) as total_fees
    FROM students s
    LEFT JOIN fee_payments f ON f.student_id = s.id
        AND f.payment_date BETWEEN p_start_date AND p_end_date
    WHERE s.class_id = p_class_id
      AND (p_branch_id IS NULL OR s.branch_id = p_branch_id)  -- ← ADD THIS
    GROUP BY s.id;
END;
```

**Key Changes:**
1. Add `IN p_branch_id INT` as the **LAST parameter**
2. Add `AND (p_branch_id IS NULL OR table.branch_id = p_branch_id)` to WHERE clause
3. `NULL` value = "All Branches"

#### 3.4 Run the Migration

Once you've updated all procedures in the migration:

```bash
# Run the migration
php artisan migrate

# If you get errors, check:
# - Syntax of your SQL statements
# - All procedures are listed correctly
# - No missing commas or quotes
```

**IMPORTANT:** Test each procedure individually before running the full migration!

### Step 4: Verify Role Configuration

Check that the roles in `BranchParameterService` match your system:

Edit `app/Services/Report/BranchParameterService.php` if needed:

```php
const ALL_BRANCHES_ROLES = [
    'Super Admin',      // Adjust to match your role names
    'School Admin',
    // Add other roles that should see "All Branches"
];
```

To find your role names:
```sql
SELECT DISTINCT name FROM roles;
```

### Step 5: Test Basic Functionality

#### 5.1 Test Branch Parameter Display

1. Login to your application
2. Navigate to **Report Center**
3. Select any report
4. **Verify:** Branch dropdown appears as the FIRST parameter
5. **Verify:** Your assigned branch is pre-selected
6. **Verify:** (For Super Admin only) "-- All Branches --" option appears

#### 5.2 Test Report Execution

1. Select a report with simple parameters
2. Choose your branch (should be pre-selected)
3. Fill other parameters
4. Click "Generate Report"
5. **Verify:** Report executes successfully
6. **Verify:** Data is filtered to selected branch

#### 5.3 Test Permission Control

**As Regular User (Teacher/Staff):**
```
1. Try to select a different branch
2. Expected: Only see your assigned branch
3. Try to execute report
4. Expected: Success (filtered to your branch)
```

**As Super Admin:**
```
1. Select "All Branches"
2. Execute report
3. Expected: See data from ALL branches
4. Select specific branch
5. Execute report
6. Expected: See data only from that branch
```

### Step 6: Check Logs for Errors

Monitor the logs to ensure everything is working:

```bash
# Watch logs in real-time
tail -f storage/logs/laravel.log

# Look for these log entries:
# - "Branch parameter auto-injected"
# - "Branch access check"
# - Any errors or warnings
```

**Expected Log Output (Success):**
```
[INFO] Branch access check {
    user_id: 15,
    requested_branch: 2,
    user_branch: 2
}
[INFO] Branch parameter auto-injected {
    report_id: 5,
    branch_id: 2,
    is_all_branches: false
}
[INFO] Report executed successfully {
    report_id: 5,
    execution_time_ms: 245
}
```

---

## 🗄️ Stored Procedure Updates

### Finding All Your Report Procedures

```sql
-- Get list of all procedures
SHOW PROCEDURE STATUS WHERE Db = DATABASE();

-- Get specific procedure definition
SHOW CREATE PROCEDURE sp_your_procedure_name;
```

### Example: Complete Procedure Update

Here's a complete example for a complex procedure:

**Original Procedure:**
```sql
DROP PROCEDURE IF EXISTS sp_student_performance_report;

CREATE PROCEDURE sp_student_performance_report(
    IN p_academic_year INT,
    IN p_class_id INT,
    IN p_subject_id INT
)
BEGIN
    SELECT
        s.id as student_id,
        s.name as student_name,
        s.enrollment_number,
        c.name as class_name,
        sub.name as subject_name,
        AVG(e.marks_obtained) as average_marks,
        MAX(e.marks_obtained) as highest_marks,
        MIN(e.marks_obtained) as lowest_marks,
        COUNT(e.id) as total_exams
    FROM students s
    INNER JOIN classes c ON c.id = s.class_id
    INNER JOIN subjects sub ON sub.id = p_subject_id
    LEFT JOIN exam_results e ON e.student_id = s.id
        AND e.subject_id = p_subject_id
        AND e.academic_year = p_academic_year
    WHERE s.class_id = p_class_id
      AND s.status = 'active'
    GROUP BY s.id, s.name, s.enrollment_number, c.name, sub.name
    ORDER BY average_marks DESC;
END;
```

**Updated With Branch Parameter:**
```sql
DROP PROCEDURE IF EXISTS sp_student_performance_report;

CREATE PROCEDURE sp_student_performance_report(
    IN p_academic_year INT,
    IN p_class_id INT,
    IN p_subject_id INT,
    IN p_branch_id INT  -- NEW: Branch parameter
)
BEGIN
    SELECT
        s.id as student_id,
        s.name as student_name,
        s.enrollment_number,
        c.name as class_name,
        sub.name as subject_name,
        AVG(e.marks_obtained) as average_marks,
        MAX(e.marks_obtained) as highest_marks,
        MIN(e.marks_obtained) as lowest_marks,
        COUNT(e.id) as total_exams
    FROM students s
    INNER JOIN classes c ON c.id = s.class_id
    INNER JOIN subjects sub ON sub.id = p_subject_id
    LEFT JOIN exam_results e ON e.student_id = s.id
        AND e.subject_id = p_subject_id
        AND e.academic_year = p_academic_year
    WHERE s.class_id = p_class_id
      AND s.status = 'active'
      AND (p_branch_id IS NULL OR s.branch_id = p_branch_id)  -- NEW: Branch filter
    GROUP BY s.id, s.name, s.enrollment_number, c.name, sub.name
    ORDER BY average_marks DESC;
END;
```

### Testing Individual Procedures

Test each updated procedure manually:

```sql
-- Test with specific branch
CALL sp_student_performance_report(2024, 5, 3, 2);

-- Test with "All Branches" (NULL)
CALL sp_student_performance_report(2024, 5, 3, NULL);

-- Verify results are filtered correctly
```

---

## 🧪 Testing the Implementation

### Test Checklist

- [ ] **Branch Dropdown Appears:** First parameter in all reports
- [ ] **Default Selection:** User's assigned branch is pre-selected
- [ ] **All Branches Option:** Shows for Super Admin/School Admin only
- [ ] **Report Execution:** Works with branch parameter
- [ ] **Data Filtering:** Results match selected branch
- [ ] **Permission Control:** Regular users cannot access other branches
- [ ] **Super Admin Access:** Can view all branches and specific branches
- [ ] **Export Functionality:** Branch filter applies to exports
- [ ] **Logs:** No errors in `storage/logs/laravel.log`

### Manual Test Scenarios

#### Scenario 1: Regular User (Teacher)
```
User: teacher@school.com (branch_id: 2)
Action: Open any report
Expected:
  - Branch dropdown shows only "Downtown Campus"
  - Cannot change selection
  - Report shows only Downtown Campus data
```

#### Scenario 2: Super Admin - Specific Branch
```
User: admin@school.com (Super Admin, branch_id: 1)
Action: Select "Downtown Campus" (branch_id: 2)
Expected:
  - Can select any branch
  - Report shows only Downtown Campus data
  - Data matches branch filter
```

#### Scenario 3: Super Admin - All Branches
```
User: admin@school.com (Super Admin)
Action: Select "-- All Branches --"
Expected:
  - Report shows data from ALL branches
  - Results include students/data from all locations
  - Summary totals include all branches
```

#### Scenario 4: Unauthorized Access Attempt
```
User: teacher@school.com (branch_id: 2)
Action: Manually send API request with branch_id: 5
Expected:
  - Request blocked with 403 error
  - Error message: "You do not have permission to access branch #5"
  - Security event logged
```

### API Testing with Postman/Curl

```bash
# Test report execution with branch parameter
curl -X POST http://your-app.test/api/reports/1/execute \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "parameters": {
      "p_branch_id": 2,
      "start_date": "2025-01-01",
      "end_date": "2025-01-31"
    }
  }'

# Expected Response:
{
  "success": true,
  "data": { /* filtered results */ },
  "meta": {
    "total_records": 45,
    "execution_time_ms": 234
  }
}
```

---

## 🔧 Troubleshooting

### Issue 1: Branch Dropdown Not Showing

**Symptoms:**
- Report parameters load but no branch dropdown
- Branch parameter missing from UI

**Solutions:**
```bash
# 1. Clear cache
php artisan cache:clear
php artisan config:clear

# 2. Check DependentParameterService
# Verify BranchParameterService is injected

# 3. Check browser console for JS errors

# 4. Verify API response includes branch parameter
curl http://your-app.test/api/reports/1/parameters
```

### Issue 2: "All Branches" Not Showing for Admin

**Symptoms:**
- Super Admin doesn't see "All Branches" option

**Solutions:**
```php
// 1. Check role name matches
// In BranchParameterService.php:
const ALL_BRANCHES_ROLES = [
    'Super Admin',  // Must match EXACTLY
];

// 2. Verify user's role
SELECT u.id, u.name, r.name as role_name
FROM users u
JOIN roles r ON r.id = u.role_id
WHERE u.email = 'admin@school.com';

// 3. Check logs for permission check
tail -f storage/logs/laravel.log | grep "Branch permission check"
```

### Issue 3: Reports Return Empty Results

**Symptoms:**
- Report executes but returns no data
- Data exists in database

**Solutions:**
```sql
-- 1. Test stored procedure directly
CALL sp_your_report(param1, param2, branch_id);

-- 2. Check if data has branch_id
SELECT COUNT(*) FROM students WHERE branch_id IS NULL;

-- 3. Verify branch filter syntax
-- Should be: AND (p_branch_id IS NULL OR table.branch_id = p_branch_id)

-- 4. Check parameter order
-- p_branch_id must be LAST parameter
```

### Issue 4: 403 Forbidden Error

**Symptoms:**
- Request blocked with "Unauthorized branch access"

**Solutions:**
```bash
# 1. Check user's branch assignment
SELECT id, name, branch_id, role_id FROM users WHERE id = YOUR_USER_ID;

# 2. Verify requested branch matches user's branch
# Or user has "All Branches" permission

# 3. Check middleware is applied correctly
php artisan route:list | grep reports

# 4. Review security logs
tail -f storage/logs/laravel.log | grep "Unauthorized branch access"
```

### Issue 5: Stored Procedure Syntax Error

**Symptoms:**
- Migration fails with SQL syntax error

**Solutions:**
```bash
# 1. Test procedure syntax in MySQL directly first
mysql -u root -p your_database < test_procedure.sql

# 2. Check for common errors:
#    - Missing commas between parameters
#    - Unescaped quotes in strings
#    - Missing semicolons
#    - Wrong delimiter

# 3. Use heredoc syntax in PHP
DB::unprepared("
    CREATE PROCEDURE ...
");

# 4. Validate each procedure individually
```

### Issue 6: Performance Degradation

**Symptoms:**
- Reports are slower than before
- High database load

**Solutions:**
```sql
-- 1. Ensure branch_id column is indexed
SHOW INDEX FROM students;
ALTER TABLE students ADD INDEX idx_branch_id (branch_id);

-- 2. Check query execution plan
EXPLAIN CALL sp_your_report(params);

-- 3. Optimize procedure queries
-- Add indexes on frequently filtered columns

-- 4. Monitor slow queries
SET GLOBAL slow_query_log = 'ON';
SET GLOBAL long_query_time = 1;
```

---

## 🔄 Rollback Instructions

If you need to revert the changes:

### Option 1: Quick Rollback (Disable Without Removing Code)

```php
// 1. Comment out middleware in routes/api.php
Route::post('/{reportId}/execute', [ReportController::class, 'execute']);
// ->middleware('branch.access');  // COMMENTED OUT

// 2. Comment out branch injection in ReportExecutionService.php
// $branchId = $this->branchParameterService->getBranchIdForExecution($parameters);
// $parameters[BranchParameterService::BRANCH_PARAM_NAME] = $branchId;

// 3. Comment out branch parameter in DependentParameterService.php
// array_unshift($result, $branchParameter);
```

### Option 2: Full Rollback (Remove Changes)

```bash
# 1. Rollback migration (restores old procedures)
php artisan migrate:rollback --step=1

# 2. Remove middleware registration from Kernel.php
# Delete line: 'branch.access' => \App\Http\Middleware\CheckBranchAccess::class,

# 3. Clear caches
php artisan cache:clear
php artisan config:clear
php artisan route:clear

# 4. (Optional) Remove new files
rm app/Services/Report/BranchParameterService.php
rm app/Http/Middleware/CheckBranchAccess.php

# 5. Restore modified files from Git
git checkout app/Services/Report/ReportExecutionService.php
git checkout app/Services/Report/DependentParameterService.php
git checkout routes/api.php
git checkout routes/reports.php
```

### Option 3: Gradual Rollback (Per Report)

You can rollback specific stored procedures individually:

```sql
-- Restore original procedure (without branch parameter)
DROP PROCEDURE IF EXISTS sp_student_attendance_report;

CREATE PROCEDURE sp_student_attendance_report(
    IN p_start_date DATE,
    IN p_end_date DATE,
    IN p_class_id INT
    -- No p_branch_id parameter
)
BEGIN
    -- Original query without branch filter
END;
```

---

## 📊 Monitoring and Maintenance

### Daily Monitoring

```bash
# Check for errors
grep "ERROR" storage/logs/laravel.log | tail -20

# Monitor branch access attempts
grep "Branch access check" storage/logs/laravel.log | tail -50

# Check report execution times
grep "Report executed successfully" storage/logs/laravel.log | tail -20
```

### Weekly Maintenance

```bash
# Review unauthorized access attempts
grep "Unauthorized branch access" storage/logs/laravel-$(date +%Y-%m-%d).log

# Analyze report performance
# Look for slow execution times (> 2000ms)

# Verify data integrity
# Ensure all new records have branch_id
```

### Monthly Tasks

- Review and optimize slow-running stored procedures
- Check index usage on branch_id columns
- Audit user branch assignments
- Review "All Branches" access logs for super admins

---

## 🎯 Success Criteria

Your implementation is successful when:

- ✅ All reports display branch selector as first parameter
- ✅ Users see only their assigned branch (non-admins)
- ✅ Super Admins can select "All Branches" or specific branches
- ✅ Reports execute successfully with branch filtering
- ✅ Export/Print functions respect branch parameter
- ✅ Unauthorized branch access is blocked
- ✅ No errors in application logs
- ✅ Performance is acceptable (< 2 seconds for most reports)

---

## 📞 Support

If you encounter issues not covered in this guide:

1. Check `storage/logs/laravel.log` for detailed error messages
2. Review the implementation documentation: `BRANCH_REPORTING_IMPLEMENTATION.md`
3. Test stored procedures directly in MySQL/MariaDB
4. Verify middleware and route configuration
5. Check user roles and permissions

---

**End of Setup Guide**

Good luck with your implementation! 🚀
