# Complete Migration Execution Plan

## Overview

This document provides step-by-step instructions for executing the comprehensive permission and architectural migration.

**Migration Components:**
1. ✅ Feature Group 14 "Community" creation
2. ✅ All 102 permissions migrated to permission_features table
3. ✅ Package 1 assigned 75 basic features (27 premium excluded)
4. ✅ Debug endpoint fixed to show correct feature counts
5. ✅ Role model updated to extend Model (not BaseModel)
6. ✅ branch_id removed from permissions and roles tables
7. ✅ Department model updated to extend Model (not BaseModel)
8. ✅ Designation model updated to extend Model (not BaseModel)
9. ✅ school_id and branch_id removed from departments table
10. ✅ school_id and branch_id removed from designations table
11. ✅ Gender model updated to extend Model (not BaseModel)
12. ✅ branch_id removed from genders table

**Estimated Time:** 62 minutes total
**Downtime Required:** < 42 minutes

---

## Pre-Migration Checklist

### 1. Create Database Backup
```bash
# Method 1: Laravel Backup (if configured)
php artisan backup:run

# Method 2: mysqldump
mysqldump -u your_user -p school_management > backup_$(date +%Y%m%d_%H%M%S).sql

# Verify backup was created
ls -lh backup_*.sql
```

### 2. Document Current State
```bash
# Save current state to file
mysql -u your_user -p school_management -e "
SELECT 'Permissions:' as metric, COUNT(*) as count FROM permissions
UNION ALL
SELECT 'Permission Features:', COUNT(*) FROM permission_features
UNION ALL
SELECT 'Feature Groups:', COUNT(*) FROM feature_groups
UNION ALL
SELECT 'Package 1 Features:', COUNT(*) FROM package_permission_features WHERE package_id = 1
UNION ALL
SELECT 'Departments:', COUNT(*) FROM departments
UNION ALL
SELECT 'Designations:', COUNT(*) FROM designations
UNION ALL
SELECT 'Genders:', COUNT(*) FROM genders
UNION ALL
SELECT 'Permissions has branch_id:', COUNT(*) FROM information_schema.columns WHERE table_name='permissions' AND column_name='branch_id'
UNION ALL
SELECT 'Roles has branch_id:', COUNT(*) FROM information_schema.columns WHERE table_name='roles' AND column_name='branch_id'
UNION ALL
SELECT 'Departments has school_id:', COUNT(*) FROM information_schema.columns WHERE table_name='departments' AND column_name='school_id'
UNION ALL
SELECT 'Departments has branch_id:', COUNT(*) FROM information_schema.columns WHERE table_name='departments' AND column_name='branch_id'
UNION ALL
SELECT 'Designations has school_id:', COUNT(*) FROM information_schema.columns WHERE table_name='designations' AND column_name='school_id'
UNION ALL
SELECT 'Designations has branch_id:', COUNT(*) FROM information_schema.columns WHERE table_name='designations' AND column_name='branch_id'
UNION ALL
SELECT 'Genders has branch_id:', COUNT(*) FROM information_schema.columns WHERE table_name='genders' AND column_name='branch_id';
" > pre_migration_state.txt

cat pre_migration_state.txt
```

**Expected Pre-Migration State:**
```
Permissions: 102
Permission Features: 7
Feature Groups: 13
Package 1 Features: 6
Departments: [actual count]
Designations: [actual count]
Genders: 2
Permissions has branch_id: 1
Roles has branch_id: 1
Departments has school_id: 1
Departments has branch_id: 1
Designations has school_id: 1
Designations has branch_id: 1
Genders has branch_id: 1
```

### 3. Verify Code Changes Are Applied
```bash
# Check Role model inheritance
grep "class Role extends" app/Models/Role.php
# Expected: class Role extends Model

# Check Department model inheritance
grep "class Department extends" app/Models/Staff/Department.php
# Expected: class Department extends Model

# Check Designation model inheritance
grep "class Designation extends" app/Models/Staff/Designation.php
# Expected: class Designation extends Model

# Check Gender model inheritance
grep "class Gender extends" app/Models/Gender.php
# Expected: class Gender extends Model

# Check debug endpoint fix
grep "pluck('p.attribute')" routes/web.php | wc -l
# Expected: 1 (should find the fixed line)

# Check migration files exist
ls -1 database/migrations/*remove_branch_id_from_system_tables.php
# Expected: database/migrations/2025_11_12_152332_remove_branch_id_from_system_tables.php

ls -1 database/migrations/*remove_school_branch_from_departments_designations.php
# Expected: database/migrations/2025_11_12_164056_remove_school_branch_from_departments_designations.php

ls -1 database/migrations/*remove_branch_id_from_genders.php
# Expected: database/migrations/2025_11_12_170117_remove_branch_id_from_genders.php
```

---

## Migration Execution Steps

### STEP 1: Execute SQL Migrations (Database Changes)

Navigate to SQL directory:
```bash
cd database/migrations/temp_sql
```

#### Option A: Execute via MySQL Command Line
```bash
# Execute in order
mysql -u your_user -p school_management < 01_create_feature_group_community.sql
mysql -u your_user -p school_management < 02_insert_permission_features.sql
mysql -u your_user -p school_management < 03_assign_package_features.sql
```

#### Option B: Execute via Laravel Tinker (Recommended)
```bash
cd ../../.. # Back to project root
php artisan tinker

# Then paste and execute:
DB::unprepared(file_get_contents('database/migrations/temp_sql/01_create_feature_group_community.sql'));
echo "✅ Feature Group 14 created\n";

DB::unprepared(file_get_contents('database/migrations/temp_sql/02_insert_permission_features.sql'));
echo "✅ Permission features inserted\n";

DB::unprepared(file_get_contents('database/migrations/temp_sql/03_assign_package_features.sql'));
echo "✅ Package features assigned\n";

exit
```

#### Option C: Execute via Database Client
1. Open your database client (phpMyAdmin, TablePlus, DBeaver, etc.)
2. Connect to `school_management` database
3. Execute each SQL file in order:
   - `01_create_feature_group_community.sql`
   - `02_insert_permission_features.sql`
   - `03_assign_package_features.sql`

### STEP 2: Verify SQL Migration Success

```bash
php artisan tinker

# Run verification queries
DB::select("
    SELECT
        'Total Permission Features' as metric,
        COUNT(*) as value,
        '102 expected' as expected
    FROM permission_features
    UNION ALL
    SELECT
        'Premium Features',
        COUNT(*),
        '27 expected'
    FROM permission_features WHERE is_premium = 1
    UNION ALL
    SELECT
        'Basic Features',
        COUNT(*),
        '75 expected'
    FROM permission_features WHERE is_premium = 0
    UNION ALL
    SELECT
        'Package 1 Features',
        COUNT(*),
        '75 expected'
    FROM package_permission_features WHERE package_id = 1
    UNION ALL
    SELECT
        'Feature Groups',
        COUNT(*),
        '14 expected'
    FROM feature_groups
    UNION ALL
    SELECT
        'Unmapped Permissions',
        COUNT(*),
        '0 expected'
    FROM permissions p
    LEFT JOIN permission_features pf ON p.id = pf.permission_id
    WHERE pf.id IS NULL;
");

exit
```

**Expected Output:**
```
Total Permission Features: 102 (102 expected)
Premium Features: 27 (27 expected)
Basic Features: 75 (75 expected)
Package 1 Features: 75 (75 expected)
Feature Groups: 14 (14 expected)
Unmapped Permissions: 0 (0 expected)
```

✅ **If all values match expected, proceed to Step 3**
❌ **If any value doesn't match, STOP and troubleshoot**

### STEP 3: Execute Laravel Migrations (Remove system-level table scoping)

```bash
# From project root
php artisan migrate

# This will execute THREE migrations:
#
# Migration 1: remove_branch_id_from_system_tables
# - Runs 5 safety checks for permissions and roles tables
# - Removes branch_id from permissions table
# - Removes branch_id from roles table
#
# Migration 2: remove_school_branch_from_departments_designations
# - Runs 9 safety checks for departments and designations tables
# - Removes school_id and branch_id from departments table
# - Removes school_id and branch_id from designations table
#
# Migration 3: remove_branch_id_from_genders
# - Runs 4 safety checks for genders table
# - Removes branch_id from genders table
#
# If ALL safety checks pass, columns will be removed
```

**Expected Output:**
```
Running migration: 2025_11_12_152332_remove_branch_id_from_system_tables

✅ All safety checks passed. Removing branch_id columns...
✅ Removed branch_id column from permissions table
✅ Removed branch_id column from roles table

✅ Migration completed successfully!
📊 Summary:
   - Permissions table: branch_id column removed
   - Roles table: branch_id column removed
   - Both tables are now system-level (no branch scoping)

Running migration: 2025_11_12_164056_remove_school_branch_from_departments_designations

✅ All safety checks passed. Removing school_id and branch_id columns...
✅ Removed school_id and branch_id columns from departments table
✅ Removed school_id and branch_id columns from designations table

✅ Migration completed successfully!
📊 Summary:
   - Departments table: school_id and branch_id columns removed
   - Designations table: school_id and branch_id columns removed
   - Both tables are now system-level (shared across all schools)

Running migration: 2025_11_12_170117_remove_branch_id_from_genders

✅ All safety checks passed. Removing branch_id column...
✅ Removed branch_id column from genders table

✅ Migration completed successfully!
📊 Summary:
   - Genders table: branch_id column removed
   - Table is now system-level (shared across all schools/branches)
```

### STEP 4: Clear All Caches

```bash
# Clear all Laravel caches
php artisan cache:clear
php artisan config:clear
php artisan view:clear
php artisan route:clear
php artisan optimize:clear

# Verify caches cleared
php artisan cache:clear --verbose
```

**Expected:** All caches cleared successfully

### STEP 5: Verify Database Schema

```bash
php artisan tinker

# Check permissions table structure
DB::select("SHOW COLUMNS FROM permissions WHERE Field = 'branch_id'");
# Expected: Empty array [] (column removed)

# Check roles table structure
DB::select("SHOW COLUMNS FROM roles WHERE Field = 'branch_id'");
# Expected: Empty array [] (column removed)

# Check departments table structure
DB::select("SHOW COLUMNS FROM departments WHERE Field IN ('school_id', 'branch_id')");
# Expected: Empty array [] (both columns removed)

# Check designations table structure
DB::select("SHOW COLUMNS FROM designations WHERE Field IN ('school_id', 'branch_id')");
# Expected: Empty array [] (both columns removed)

# Check genders table structure
DB::select("SHOW COLUMNS FROM genders WHERE Field = 'branch_id'");
# Expected: Empty array [] (column removed)

# Verify permission_features table
DB::table('permission_features')->count();
# Expected: 102

# Verify departments, designations, and genders record counts unchanged
DB::table('departments')->count();
DB::table('designations')->count();
DB::table('genders')->count();
# Expected: Same counts as pre-migration (genders should be 2)

exit
```

---

## Post-Migration Testing

### Test 1: Verify Debug Endpoint

```bash
# Test School 1 debug endpoint
curl http://your-domain.test/debug/features/school/1 | jq

# OR open in browser:
# http://your-domain.test/debug/features/school/1
```

**Expected Response:**
```json
{
  "package_diagnostics": {
    "database_keywords_count": 75,
    "cache_vs_db_match": true,
    "missing_in_cache": [],
    "extra_in_cache": []
  }
}
```

✅ **Key Check:** `cache_vs_db_match` should be `true` (previously was `false`)

### Test 2: Login and Access Testing

#### School 1 (Basic Package - 75 features)

1. **Login:** Navigate to School 1 login
2. **Login Credentials:** Use School 1 Super Admin credentials
3. **Verify Sidebar:** Check sidebar renders correctly
4. **Expected Visible Menus:**
   - ✅ Dashboard
   - ✅ Student Management
   - ✅ Academic Management
   - ✅ Fees Management (except Cash Transfer - premium)
   - ✅ Examination
   - ✅ Accounts
   - ✅ Attendance
   - ✅ Reports
   - ✅ Library
   - ❌ Online Examination (premium - should be hidden)
   - ✅ Staff Management
   - ✅ Website (ID cards/certificates only, not CMS features)
   - ✅ Settings
   - ❌ Community/Forums (premium - should be hidden)

5. **Test Core Functions:**
   ```
   - Navigate to Student Management → Students → Should load ✅
   - Create new student → Should work ✅
   - Navigate to Fees → Fee Collection → Should load ✅
   - Navigate to Attendance → Mark Attendance → Should load ✅
   ```

6. **Test Premium Restrictions:**
   ```
   - Try accessing /online-exam (if route exists) → Should be blocked ❌
   - Try accessing /forums (if route exists) → Should be blocked ❌
   - Try accessing /cash-transfer → Should be blocked ❌
   ```

#### School 2 (Same Package - 75 features)

1. **Login:** Navigate to School 2 login
2. **Login Credentials:** Use School 2 Super Admin credentials
3. **Repeat all tests from School 1**
4. **Verify:** Both schools have identical feature access

### Test 3: Cache Performance Test

```bash
php artisan tinker

# Enable query log
DB::enableQueryLog();

# Simulate feature check (triggers cache)
Auth::loginUsingId(1); // School 1 admin user ID
$features = Auth::user()->school->getAllowedFeatures();
echo "Features loaded: " . $features->count() . "\n";

# Check queries
$queries = DB::getQueryLog();
echo "Queries executed: " . count($queries) . "\n";

# Clear query log
DB::flushQueryLog();

# Second call - should use cache
$features2 = Auth::user()->school->getAllowedFeatures();
$queries2 = DB::getQueryLog();
echo "Queries executed (cached): " . count($queries2) . "\n";

exit
```

**Expected:**
```
Features loaded: 75
Queries executed: 3-5 (cache build)
Queries executed (cached): 0 (cache hit)
```

### Test 4: Verify Permission System Still Works

```bash
php artisan tinker

# Test hasPermission helper
Auth::loginUsingId(1); // Super Admin

// Super Admin should pass all permission checks (role_id = 1 bypass)
var_dump(hasPermission('student_read')); // true
var_dump(hasPermission('fees_collect_create')); // true
var_dump(hasPermission('nonexistent_permission')); // true (Super Admin bypass)

// Test hasFeature helper
var_dump(hasFeature('student')); // true (basic feature)
var_dump(hasFeature('online_exam')); // false (premium feature, not in package)
var_dump(hasFeature('dashboard')); // true (basic feature)

exit
```

### Test 5: Database Integrity Check

```bash
php artisan tinker

// Final integrity verification
$results = DB::select("
    SELECT
        'Total Permissions' as metric,
        COUNT(*) as value
    FROM permissions
    UNION ALL
    SELECT 'Total Permission Features', COUNT(*)
    FROM permission_features
    UNION ALL
    SELECT 'Total Feature Groups', COUNT(*)
    FROM feature_groups
    UNION ALL
    SELECT 'Package 1 Features', COUNT(*)
    FROM package_permission_features WHERE package_id = 1
    UNION ALL
    SELECT 'Premium Features', COUNT(*)
    FROM permission_features WHERE is_premium = 1
    UNION ALL
    SELECT 'Basic Features', COUNT(*)
    FROM permission_features WHERE is_premium = 0
    UNION ALL
    SELECT 'Duplicate Permissions', COUNT(*)
    FROM (
        SELECT attribute, COUNT(*) as cnt
        FROM permissions
        GROUP BY attribute
        HAVING cnt > 1
    ) duplicates
    UNION ALL
    SELECT 'Orphaned Features', COUNT(*)
    FROM permission_features pf
    LEFT JOIN permissions p ON pf.permission_id = p.id
    WHERE p.id IS NULL;
");

print_r($results);

exit
```

**Expected Results:**
```
Total Permissions: 102
Total Permission Features: 102
Total Feature Groups: 14
Package 1 Features: 75
Premium Features: 27
Basic Features: 75
Duplicate Permissions: 0
Orphaned Features: 0
```

---

## Success Criteria Checklist

### Pre-Migration
- [ ] **Database Backup Created** and verified
- [ ] **Pre-migration state** documented

### Permission Features Migration
- [ ] **SQL migrations executed** successfully (3 files)
- [ ] **102 permission_features** created (verified)
- [ ] **75 features assigned** to Package 1 (verified)
- [ ] **Debug endpoint** shows correct count (75/75 match)

### System-Level Table Migrations
- [ ] **Laravel migrations executed** (3 migrations)
- [ ] **branch_id removed** from permissions table
- [ ] **branch_id removed** from roles table
- [ ] **school_id and branch_id removed** from departments table
- [ ] **school_id and branch_id removed** from designations table
- [ ] **branch_id removed** from genders table
- [ ] **No data loss** (record counts unchanged)

### Post-Migration Verification
- [ ] **Caches cleared** successfully
- [ ] **School 1 login** works and shows correct features
- [ ] **School 2 login** works and shows correct features
- [ ] **Both schools see same departments** (system-level)
- [ ] **Both schools see same designations** (system-level)
- [ ] **Both schools see same genders** (system-level)
- [ ] **Basic features accessible** (student, fees, attendance, etc.)
- [ ] **Premium features blocked** (online exam, forums, cash transfer)
- [ ] **Cache working** (0 queries on second load)
- [ ] **No errors** in `storage/logs/laravel.log`
- [ ] **Database integrity** verified (all checks pass)

---

## Rollback Instructions

If any issues occur during migration:

### 1. Restore Database from Backup
```bash
# Stop migration immediately
# Drop current database
mysql -u your_user -p -e "DROP DATABASE school_management;"

# Recreate database
mysql -u your_user -p -e "CREATE DATABASE school_management CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"

# Restore from backup
mysql -u your_user -p school_management < backup_YYYYMMDD_HHMMSS.sql

# Verify restoration
mysql -u your_user -p school_management -e "SELECT COUNT(*) FROM permissions;"
# Should show 102
```

### 2. Rollback Code Changes (Git)
```bash
# If using git
git checkout app/Models/Role.php
git checkout routes/web.php
rm database/migrations/2025_11_12_152332_remove_branch_id_from_system_tables.php
```

### 3. Clear Caches
```bash
php artisan cache:clear
php artisan config:clear
php artisan view:clear
```

### 4. Verify System is Back to Pre-Migration State
```bash
# Check state matches pre_migration_state.txt
cat pre_migration_state.txt
```

---

## Troubleshooting

### Issue: SQL migration fails with duplicate key error
**Cause:** Some permission_features already exist
**Solution:** Check which records exist, skip those INSERT statements

### Issue: Laravel migration fails safety checks
**Cause:** Models still extend BaseModel or duplicate data exists
**Solution:** Verify Role.php and Permission.php extend Model, not BaseModel

### Issue: Cache still showing old feature count
**Cause:** Cache not properly cleared
**Solution:**
```bash
php artisan cache:clear --verbose
php artisan config:clear
# Also clear Redis/database cache manually if using those drivers
```

### Issue: Debug endpoint shows cache_vs_db_match: false
**Cause:** Debug endpoint query not updated
**Solution:** Verify routes/web.php line 529 uses `pluck('p.attribute')`

### Issue: Sidebar not showing new features
**Cause:** hasFeature() still checking old cache
**Solution:**
```bash
php artisan cache:clear
# Then force cache rebuild
php artisan tinker
Auth::loginUsingId(1);
Cache::forget('school_features_' . Auth::user()->school_id);
exit
```

### Issue: Permission denied errors after migration
**Cause:** Permission model cache or hasPermission() issues
**Solution:** Verify Super Admin bypass is in place (role_id = 1)

---

## Post-Migration Cleanup (After 7 Days)

Once migration is verified stable:

```bash
# Remove temporary SQL files
rm -rf database/migrations/temp_sql/

# Remove backup files (keep offsite backup)
rm backup_*.sql

# Document migration completion
echo "Migration completed successfully on $(date)" >> MIGRATION_LOG.md
```

---

## Support & Documentation

- **Migration Files:** `database/migrations/temp_sql/`
- **Execution Guide:** This file (`MIGRATION_EXECUTION_PLAN.md`)
- **Verification Queries:** See `03_assign_package_features.sql`
- **Logs:** `storage/logs/laravel.log`

**Questions or Issues?**
Review this document first, check logs, then consult with development team.

---

## Final Notes

This migration is **non-destructive** and **fully reversible**. All changes can be rolled back using the backup and migration rollback procedures.

**Best Practices:**
1. Execute during low-traffic hours
2. Monitor logs continuously during migration
3. Test thoroughly before declaring success
4. Keep backup for at least 7 days
5. Document any deviations from this plan

**Estimated Downtime:** 20-30 minutes
**Total Execution Time:** 62 minutes
**Risk Level:** LOW (with proper backup and testing)

---

✅ **READY TO EXECUTE**

Follow steps 1-5 in order. Do not skip verification steps.
