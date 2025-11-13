# Database Migration Execution Guide

## Overview
This directory contains SQL scripts for migrating all 102 permissions to the feature-based system.

## Execution Order (MUST BE SEQUENTIAL)

### Prerequisites
1. **Backup Database First!**
   ```bash
   # From project root
   php artisan db:backup
   # OR via mysqldump
   mysqldump -u your_user -p school_management > backup_$(date +%Y%m%d_%H%M%S).sql
   ```

2. **Verify Current State**
   ```sql
   SELECT COUNT(*) FROM permissions; -- Should be 102
   SELECT COUNT(*) FROM permission_features; -- Should be 7
   SELECT COUNT(*) FROM feature_groups; -- Should be 13
   SELECT COUNT(*) FROM package_permission_features WHERE package_id = 1; -- Should be 6
   ```

### Step-by-Step Execution

#### Method 1: MySQL Command Line
```bash
# Navigate to SQL directory
cd database/migrations/temp_sql

# Execute in order
mysql -u your_user -p school_management < 01_create_feature_group_community.sql
mysql -u your_user -p school_management < 02_insert_permission_features.sql
mysql -u your_user -p school_management < 03_assign_package_features.sql
```

#### Method 2: Laravel Tinker (Recommended)
```bash
php artisan tinker

# Then paste and execute each SQL file content
DB::statement(file_get_contents('database/migrations/temp_sql/01_create_feature_group_community.sql'));
DB::statement(file_get_contents('database/migrations/temp_sql/02_insert_permission_features.sql'));
DB::statement(file_get_contents('database/migrations/temp_sql/03_assign_package_features.sql'));
```

#### Method 3: Database Client (phpMyAdmin, TablePlus, etc.)
1. Open your database client
2. Connect to `school_management` database
3. Execute each SQL file in order:
   - `01_create_feature_group_community.sql`
   - `02_insert_permission_features.sql`
   - `03_assign_package_features.sql`

### Post-Execution Verification

Run these queries to verify successful migration:

```sql
-- 1. Total permission_features count
SELECT COUNT(*) as total FROM permission_features;
-- Expected: 102

-- 2. Premium vs Basic breakdown
SELECT
    is_premium,
    COUNT(*) as count,
    CASE
        WHEN is_premium = 1 THEN 'Premium'
        ELSE 'Basic'
    END as type
FROM permission_features
GROUP BY is_premium;
-- Expected: Basic=75, Premium=27

-- 3. Package 1 feature count
SELECT COUNT(*) as package1_features
FROM package_permission_features
WHERE package_id = 1;
-- Expected: 75

-- 4. Check for unmapped permissions
SELECT p.id, p.attribute
FROM permissions p
LEFT JOIN permission_features pf ON p.id = pf.permission_id
WHERE pf.id IS NULL;
-- Expected: 0 rows (all permissions mapped)

-- 5. Feature distribution by group
SELECT
    fg.name,
    COUNT(pf.id) as total,
    SUM(CASE WHEN pf.is_premium = 1 THEN 1 ELSE 0 END) as premium,
    SUM(CASE WHEN pf.is_premium = 0 THEN 1 ELSE 0 END) as basic
FROM feature_groups fg
LEFT JOIN permission_features pf ON fg.id = pf.feature_group_id
GROUP BY fg.id, fg.name
ORDER BY fg.position;
```

### Expected Results

#### Feature Groups Distribution
| Group | Total Features | Premium | Basic |
|-------|----------------|---------|-------|
| Dashboard | 1 | 0 | 1 |
| Student Information | 7 | 0 | 7 |
| Academic Management | 9 | 0 | 9 |
| Fees Management | 9 | 1 | 8 |
| Examination | 12 | 0 | 12 |
| Accounts | 4 | 0 | 4 |
| Attendance | 2 | 0 | 2 |
| Reports | 7 | 0 | 7 |
| Library | 5 | 0 | 5 |
| Online Examination | 4 | 4 | 0 |
| Staff Management | 5 | 0 | 5 |
| Website | 16 | 12 | 4 |
| Settings | 16 | 0 | 16 |
| Community | 3 | 3 | 0 |
| **TOTAL** | **102** | **27** | **75** |

#### Premium Features List (27 total)
1. **Fees**: cash_transfer
2. **Online Exam** (4): online_exam_type, question_group, question_bank, online_exam
3. **Website CMS** (12): sections, slider, about, counter, contact_info, dep_contact, news, event, gallery_category, gallery, subscribe, contact_message
4. **Community** (3): forums, forum_comment, memories

## Troubleshooting

### If Error Occurs During Execution

1. **Check error message** - Most errors will indicate which INSERT failed
2. **Verify prerequisites** - Ensure Feature Group 14 was created first
3. **Check for duplicates** - Ensure permission_features doesn't already have the record
4. **Rollback if needed**:
   ```sql
   -- Delete newly inserted records
   DELETE FROM package_permission_features
   WHERE package_id = 1
     AND created_at >= 'YYYY-MM-DD HH:MM:SS'; -- timestamp when you started

   DELETE FROM permission_features
   WHERE created_at >= 'YYYY-MM-DD HH:MM:SS';

   DELETE FROM feature_groups WHERE id = 14;
   ```

### Common Issues

**Issue**: Duplicate key error on permission_id
**Solution**: That permission is already mapped. Check `SELECT * FROM permission_features WHERE permission_id = X`

**Issue**: Foreign key constraint fails
**Solution**: Verify Feature Group 14 was created first

**Issue**: Package assignment adds 0 rows
**Solution**: Features might already be assigned. Verify with `SELECT COUNT(*) FROM package_permission_features WHERE package_id = 1`

## Next Steps

After successful execution:
1. ✅ Proceed with code fixes (debug endpoint, Role model)
2. ✅ Create migration for branch_id removal
3. ✅ Clear caches: `php artisan cache:clear`
4. ✅ Test feature access for both schools

## Rollback Instructions

If you need to completely rollback:

```sql
-- 1. Remove Package 1 feature assignments (keep only original 6)
DELETE FROM package_permission_features
WHERE package_id = 1
  AND permission_feature_id NOT IN (24, 25, 26, 34, 39, 49);

-- 2. Remove newly created permission_features (keep only original 7)
DELETE FROM permission_features
WHERE id NOT IN (24, 25, 26, 34, 39, 48, 49);

-- 3. Remove Community feature group
DELETE FROM feature_groups WHERE id = 14;

-- 4. Verify rollback
SELECT COUNT(*) FROM permission_features; -- Should be 7
SELECT COUNT(*) FROM feature_groups; -- Should be 13
SELECT COUNT(*) FROM package_permission_features WHERE package_id = 1; -- Should be 6
```

## Contact

If you encounter any issues during migration:
1. **Stop execution immediately**
2. **Restore from backup**
3. **Document the error message**
4. **Review the SQL file that failed**
