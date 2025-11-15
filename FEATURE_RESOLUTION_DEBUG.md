# Feature Resolution Debugging Guide

## Problem Statement

**Issue**: School 2 (school_id: 2, package_id: 1) has **ZERO** `allowed_features` while School 1 (school_id: 1, package_id: 1) has **40+ features** despite both schools having the **SAME** package_id.

**Expected Behavior**: Both schools should have identical feature sets since they share the same package.

**Root Cause Investigation**: This guide provides comprehensive tools to identify exactly where the feature resolution chain breaks for School 2.

---

## Feature Loading Chain Overview

Understanding the complete feature resolution path:

```
User Login
    ↓
Sidebar Rendering → hasAnyFeature(['feature1', 'feature2'])
    ↓
hasFeatureAccess() helper
    ↓
School::getAllowedFeatures() [LOG POINTS A-E]
    ↓
[Cache Lookup: school_features_{school_id}]
    ↓
Package::getAllowedPermissions() [LOG POINTS F-J]
    ↓
[Cache Lookup: package_allowed_permissions_{package_id}]
    ↓
Database Query Chain:
    package_permission_features (package_id = 1)
        ↓
    JOIN permission_features (status = 1) ← ACTIVE FILTER
        ↓
    JOIN permissions (permission_id)
        ↓
    PLUCK permission.attribute
        ↓
    FILTER (remove nulls)
        ↓
    UNIQUE + VALUES
        ↓
    RETURN Collection
```

---

## Diagnostic Tools

### 1. Database Seeder: DiagnoseSchool2FeaturesSeeder

**Purpose**: Comprehensive data integrity verification

**Usage**:
```bash
php artisan db:seed --class=DiagnoseSchool2FeaturesSeeder
```

**What It Checks**:
- ✅ Schools and package existence
- ✅ package_permission_features count for package_id = 1
- ✅ Active vs inactive permission_features distribution
- ✅ Broken relationships (permission_features → permissions)
- ✅ Cache status for both schools
- ✅ Simulated feature loading for comparison
- ✅ NULL permission attributes

**Interactive Features**:
- Offers to clear School 2 cache during diagnosis
- Displays side-by-side comparison tables
- Provides actionable fix recommendations

**Expected Output**:
```
🔍 [1/7] Verifying school and package data...
✅ Both schools have the SAME package_id: 1

🔍 [2/7] Checking package_permission_features for package_id = 1...
✅ Found 45 permission_feature records for package_id = 1

🔍 [3/7] Checking permission_features status distribution...
│ Feature Status │ Count │ Active? │
│ 1              │ 40    │ ✅ Yes  │
│ 0              │ 5     │ ❌ No   │

🔍 [4/7] Checking for broken feature→permission relationships...
✅ No broken relationships found

🔍 [5/7] Checking cache status for both schools...
│ School 1 features │ school_features_1 │ ✅ Cached │ 40 │
│ School 2 features │ school_features_2 │ ❌ Not cached │ 0 │

🔍 [6/7] Simulating feature loading for both schools...
School 1: ✅ Query returned: 40 features
School 2: ❌ ZERO features returned from database query!

🔍 [7/7] Verifying permission attributes...
✅ Found 40 unique permission attributes for package 1
```

---

### 2. Artisan Command: diagnose:school-features

**Purpose**: Interactive diagnostic tool for specific schools

**Usage**:
```bash
# Basic diagnosis
php artisan diagnose:school-features 2

# Compare with another school
php artisan diagnose:school-features 2 --compare-with=1

# Clear cache before diagnosis
php artisan diagnose:school-features 2 --clear-cache

# Show SQL queries
php artisan diagnose:school-features 2 --show-sql --compare-with=1
```

**Features**:
- 📊 Step-by-step feature loading trace
- 🧹 Optional cache clearing
- 🔍 SQL query display
- 📋 Side-by-side school comparison
- ✅ Eloquent model testing

**Output Example**:
```bash
$ php artisan diagnose:school-features 2 --compare-with=1 --clear-cache

╔═══════════════════════════════════════════════════════════════╗
║  School Feature Diagnostic Tool                               ║
╚═══════════════════════════════════════════════════════════════╝

🏫 Diagnosing School: School 2 (ID: 2)
   Package ID: 1

🧹 Clearing cache...
   ✅ Cleared: school_features_2
   ✅ Cleared: package_allowed_permissions_1

📊 School: School 2 (ID: 2)

🔍 Step 1: Checking package_permission_features...
   ✅ Found 45 permission_feature records

🔍 Step 2: Checking active permission_features...
   ✅ Active features: 40
   ⚠️  Inactive features: 5 (will be filtered out)

🔍 Step 3: Checking permission relationships...
   ✅ All active features have valid permissions

🔍 Step 4: Checking final permission attributes...
   ✅ Final permissions: 40
   Sample: dashboard, fees_type, fees_collect, student, parent...

🔍 Step 5: Checking cache...
   ⚠️  School cache empty (will build on next access)
   ⚠️  Package cache empty (will build on next access)

🔍 Step 6: Testing with Eloquent model...
   ✅ Eloquent getAllowedFeatures() returned: 40 features
   Sample: dashboard, fees_type, fees_collect, student, parent...

═══════════════════════════════════════════════════════════════
  COMPARISON MODE
═══════════════════════════════════════════════════════════════

📊 Comparison Summary:
│ Attribute      │ School 1        │ School 2        │ Match? │
│ Package ID     │ 1               │ 1               │ ✅     │
│ Feature Count  │ 40              │ 40              │ ✅     │
```

---

### 3. Enhanced Debug Endpoint

**URL**: `/debug/feature-access`

**Authentication**: Required (middleware: auth)

**New Features Added**:
- 💾 Cache diagnostics (existence, counts)
- 📦 Package diagnostics (total, active, inactive features)
- 🗄️ Database integrity checks
- 📝 Automatic logging trigger

**Usage**:
```bash
# Login as School 2 user
curl -X GET https://your-domain.com/debug/feature-access \
  -H "Authorization: Bearer YOUR_TOKEN" \
  | jq
```

**Enhanced Response**:
```json
{
  "user": {
    "id": 197,
    "email": "dahir@gacanlibax.com",
    "role_id": 1,
    "school_id": 2
  },
  "school": {
    "id": 2,
    "name": null,
    "package_id": 1,
    "has_package_relation": true,
    "package_name": "Basic Package"
  },
  "allowed_features": [],
  "feature_checks": {
    "online_admission": false,
    "student_info": false
  },
  "cache": {
    "school_cache_key": "school_features_2",
    "school_cache_exists": true,
    "school_cache_count": 0,
    "package_cache_key": "package_allowed_permissions_1",
    "package_cache_exists": true,
    "package_cache_count": 0
  },
  "package_diagnostics": {
    "total_permission_features": 45,
    "active_permission_features": 40,
    "inactive_permission_features": 5,
    "valid_final_permissions": 0
  },
  "database_check": {
    "school_exists": true,
    "package_exists": true,
    "has_package_features": true
  }
}
```

**Key Indicators**:
- `allowed_features: []` → School has zero features
- `school_cache_count: 0` → Cache is poisoned with empty array
- `package_cache_count: 0` → Package cache is also empty
- `valid_final_permissions: 0` → **ROOT CAUSE: Database query returns 0!**

---

## Logging System

### Log Points Overview

#### School.php - getAllowedFeatures()

| Log Point | Event | Location |
|-----------|-------|----------|
| **LOG POINT A** | Cache lookup | Before Cache::remember |
| **LOG POINT B** | Cache miss | Inside Cache::remember callback |
| **LOG POINT C** | No package found | When $this->package is null |
| **LOG POINT D** | Package found | Before calling getAllowedPermissions() |
| **LOG POINT E** | Results received | After getAllowedPermissions() returns |

#### Package.php - getAllowedPermissions()

| Log Point | Event | Location |
|-----------|-------|----------|
| **LOG POINT F** | Cache lookup | Before Cache::remember |
| **LOG POINT G** | Cache miss | Inside Cache::remember callback |
| **LOG POINT H** | Features loaded | After permissionFeatures()->active()->get() |
| **LOG POINT I** | Attributes plucked | After pluck('permission.attribute') |
| **LOG POINT J** | Final results | After filter()->unique()->values() |

### Viewing Logs

**Location**: `storage/logs/laravel.log`

**Filter by prefix**:
```bash
# School model logs
tail -f storage/logs/laravel.log | grep "\[SCHOOL\]"

# Package model logs
tail -f storage/logs/laravel.log | grep "\[PACKAGE\]"

# Debug endpoint logs
tail -f storage/logs/laravel.log | grep "\[DEBUG ENDPOINT\]"

# All feature-related logs
tail -f storage/logs/laravel.log | grep -E "\[SCHOOL\]|\[PACKAGE\]|\[DEBUG\]"
```

**Expected Log Sequence for Working School** (School 1):
```
[SCHOOL] getAllowedFeatures() - Cache lookup
    school_id: 1, package_id: 1, cache_hit: true

[PACKAGE] getAllowedPermissions() - Cache lookup
    package_id: 1, cache_hit: true
```

**Expected Log Sequence for Broken School** (School 2):
```
[SCHOOL] getAllowedFeatures() - Cache lookup
    school_id: 2, package_id: 1, cache_hit: false

[SCHOOL] getAllowedFeatures() - Cache miss, building features
    school_id: 2, has_package_relationship: true

[SCHOOL] getAllowedFeatures() - Package found, fetching permissions
    package_id: 1, package_name: "Basic Package"

[PACKAGE] getAllowedPermissions() - Cache lookup
    package_id: 1, cache_hit: false

[PACKAGE] getAllowedPermissions() - Cache miss, executing query
    package_id: 1

[PACKAGE] getAllowedPermissions() - Permission features loaded
    total_features: 0 ← ❌ PROBLEM: Should be 40!

[PACKAGE] getAllowedPermissions() - Attributes plucked
    total_attributes: 0, null_count: 0

[PACKAGE] getAllowedPermissions() - Final permissions
    final_count: 0 ← ❌ ZERO PERMISSIONS!

[SCHOOL] getAllowedFeatures() - Permissions received from package
    permission_count: 0 ← ❌ ZERO FEATURES CACHED!
```

---

## Diagnostic Workflow

### Quick Diagnosis (5 minutes)

1. **Clear cache for School 2**:
```bash
php artisan cache:forget school_features_2
php artisan cache:forget package_allowed_permissions_1
```

2. **Run diagnostic seeder**:
```bash
php artisan db:seed --class=DiagnoseSchool2FeaturesSeeder
```

3. **Review output** for red flags:
   - ❌ No package_permission_features
   - ❌ All features inactive
   - ❌ Broken relationships
   - ❌ Simulated query returns 0

4. **Access debug endpoint as School 2 user**:
```bash
# Login as School 2 and visit
https://your-domain.com/debug/feature-access
```

5. **Check logs**:
```bash
tail -100 storage/logs/laravel.log | grep -E "\[SCHOOL\]|\[PACKAGE\]"
```

### Deep Diagnosis (15 minutes)

1. **Run artisan command with comparison**:
```bash
php artisan diagnose:school-features 2 --compare-with=1 --clear-cache --show-sql
```

2. **Analyze each step**:
   - Step 1: package_permission_features count
   - Step 2: Active vs inactive features
   - Step 3: Permission relationships
   - Step 4: Final permission attributes
   - Step 5: Cache status
   - Step 6: Eloquent model test

3. **Review SQL queries** (with --show-sql flag):
```sql
SELECT DISTINCT p.attribute
FROM package_permission_features ppf
JOIN permission_features pf ON ppf.permission_feature_id = pf.id
JOIN permissions p ON pf.permission_id = p.id
WHERE ppf.package_id = 1
  AND pf.status = 1
  AND p.attribute IS NOT NULL
```

4. **Compare results** between School 1 and School 2

5. **Check logs for complete trace**:
```bash
# Follow logs in real-time
tail -f storage/logs/laravel.log | grep -E "\[SCHOOL\]|\[PACKAGE\]" --color=always
```

6. **Access debug endpoint** and examine:
   - `cache` section (poisoned cache?)
   - `package_diagnostics` (data integrity?)
   - `database_check` (missing records?)

---

## Common Issues & Solutions

### Issue 1: Cache Poisoned with Empty Array

**Symptoms**:
- `allowed_features: []`
- `school_cache_count: 0`
- `package_cache_count: 0`

**Diagnosis**:
```bash
php artisan diagnose:school-features 2 --clear-cache
```

**Solution**:
```bash
# Clear specific caches
php artisan cache:forget school_features_2
php artisan cache:forget package_allowed_permissions_1

# Or clear all caches
php artisan cache:clear
```

**Verification**:
```bash
# Access debug endpoint - should rebuild cache
curl https://your-domain.com/debug/feature-access

# Check new cache values
php artisan diagnose:school-features 2
```

---

### Issue 2: All Features Inactive (status = 0)

**Symptoms**:
- LOG POINT H shows `total_features: 0`
- Diagnostic seeder shows all features with status = 0

**Diagnosis**:
```sql
SELECT pf.status, COUNT(*) as count
FROM package_permission_features ppf
JOIN permission_features pf ON ppf.permission_feature_id = pf.id
WHERE ppf.package_id = 1
GROUP BY pf.status;
```

**Solution**:
```sql
-- Activate all permission_features
UPDATE permission_features
SET status = 1
WHERE status != 1;
```

**Verification**:
```bash
php artisan db:seed --class=DiagnoseSchool2FeaturesSeeder
```

---

### Issue 3: Missing package_permission_features

**Symptoms**:
- `total_permission_features: 0`
- Diagnostic seeder shows "NO package_permission_features"

**Diagnosis**:
```sql
SELECT COUNT(*) FROM package_permission_features WHERE package_id = 1;
-- Result: 0 (should be 40+)
```

**Solution**:
```bash
# Run the seeder to add missing features
php artisan db:seed --class=AddStaffManagementFeaturesToPackageSeeder

# Verify with integrity check
php artisan db:seed --class=VerifyPackageFeatureIntegritySeeder
```

**Verification**:
```bash
php artisan diagnose:school-features 2 --clear-cache
```

---

### Issue 4: Broken Permission Relationships

**Symptoms**:
- LOG POINT H shows features loaded
- LOG POINT I shows high null_count
- `valid_final_permissions: 0`

**Diagnosis**:
```sql
SELECT ppf.id, pf.name, pf.permission_id, p.id as perm_id
FROM package_permission_features ppf
LEFT JOIN permission_features pf ON ppf.permission_feature_id = pf.id
LEFT JOIN permissions p ON pf.permission_id = p.id
WHERE ppf.package_id = 1 AND pf.status = 1 AND p.id IS NULL;
```

**Solution**:
```sql
-- Find permission_features with invalid permission_id
SELECT id, name, permission_id
FROM permission_features
WHERE permission_id NOT IN (SELECT id FROM permissions);

-- Fix by updating to valid permission_id or deleting broken records
```

**Verification**:
```bash
php artisan diagnose:school-features 2 --show-sql
```

---

### Issue 5: Permission Attributes are NULL

**Symptoms**:
- Features loaded correctly
- Attributes plucked but all NULL
- LOG POINT I: `null_count: 40`

**Diagnosis**:
```sql
SELECT p.id, p.attribute
FROM permissions p
WHERE p.attribute IS NULL
  AND p.id IN (
    SELECT pf.permission_id
    FROM permission_features pf
    WHERE pf.id IN (
      SELECT ppf.permission_feature_id
      FROM package_permission_features ppf
      WHERE ppf.package_id = 1
    )
  );
```

**Solution**:
```sql
-- Update NULL attributes with proper keywords
UPDATE permissions
SET attribute = LOWER(REPLACE(name, ' ', '_'))
WHERE attribute IS NULL;
```

**Verification**:
```bash
php artisan cache:clear
php artisan diagnose:school-features 2
```

---

## Manual Verification Queries

### Query 1: Full Feature Chain for Package 1
```sql
SELECT
    ppf.id as pivot_id,
    ppf.package_id,
    pf.id as feature_id,
    pf.name as feature_name,
    pf.status as feature_status,
    pf.permission_id,
    p.id as perm_id,
    p.attribute as perm_attribute
FROM package_permission_features ppf
LEFT JOIN permission_features pf ON ppf.permission_feature_id = pf.id
LEFT JOIN permissions p ON pf.permission_id = p.id
WHERE ppf.package_id = 1
ORDER BY pf.status DESC, pf.id;
```

**Expected Result**: 40-45 rows with valid perm_attribute values

**Red Flags**:
- feature_id IS NULL (broken ppf → pf relationship)
- perm_id IS NULL (broken pf → p relationship)
- feature_status = 0 (inactive, will be filtered)
- perm_attribute IS NULL (will be filtered by ->filter())

---

### Query 2: Compare Package Features Between Schools
```sql
-- This should return same results for both schools
SELECT
    s.id as school_id,
    s.school_name,
    s.package_id,
    pkg.name as package_name,
    COUNT(DISTINCT p.attribute) as feature_count
FROM schools s
JOIN packages pkg ON s.package_id = pkg.id
JOIN package_permission_features ppf ON pkg.id = ppf.package_id
JOIN permission_features pf ON ppf.permission_feature_id = pf.id
JOIN permissions p ON pf.permission_id = p.id
WHERE s.id IN (1, 2)
  AND pf.status = 1
  AND p.attribute IS NOT NULL
GROUP BY s.id, s.school_name, s.package_id, pkg.name;
```

**Expected Result**:
```
| school_id | school_name | package_id | package_name  | feature_count |
|-----------|-------------|------------|---------------|---------------|
| 1         | School 1    | 1          | Basic Package | 40            |
| 2         | School 2    | 1          | Basic Package | 40            |
```

---

### Query 3: Identify Inactive Features
```sql
SELECT
    pf.id,
    pf.name,
    pf.status,
    COUNT(ppf.id) as used_in_packages
FROM permission_features pf
LEFT JOIN package_permission_features ppf ON pf.id = ppf.permission_feature_id
WHERE pf.status != 1
GROUP BY pf.id, pf.name, pf.status
ORDER BY used_in_packages DESC;
```

---

### Query 4: Find Permission Features with NULL Attributes
```sql
SELECT
    pf.id as feature_id,
    pf.name as feature_name,
    pf.permission_id,
    p.id as perm_id,
    p.name as perm_name,
    p.attribute
FROM permission_features pf
JOIN permissions p ON pf.permission_id = p.id
WHERE p.attribute IS NULL
  AND pf.status = 1;
```

---

## Testing After Fixes

### 1. Clear All Caches
```bash
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear
```

### 2. Run Diagnostic Seeder
```bash
php artisan db:seed --class=DiagnoseSchool2FeaturesSeeder
```

**Expected**: All checks should pass with ✅

### 3. Run Artisan Diagnostic Command
```bash
php artisan diagnose:school-features 2 --compare-with=1 --clear-cache
```

**Expected**: Both schools should have identical feature counts

### 4. Test Debug Endpoint
```bash
# Login as School 2 user and access
curl https://your-domain.com/debug/feature-access | jq '.allowed_features | length'
```

**Expected**: Should return 40 (same as School 1)

### 5. Test Sidebar Rendering
```bash
# Login as School 2 user and check sidebar
# All features should be visible
# No 403 errors when accessing feature pages
```

### 6. Monitor Logs
```bash
tail -f storage/logs/laravel.log | grep -E "\[SCHOOL\]|\[PACKAGE\]"
```

**Expected Log Sequence**:
```
[SCHOOL] getAllowedFeatures() - Cache lookup: cache_hit: true
[SCHOOL] getAllowedFeatures() - Permissions received: permission_count: 40
```

---

## Maintenance

### Regular Health Checks

**Weekly**:
```bash
# Run diagnostic seeder
php artisan db:seed --class=DiagnoseSchool2FeaturesSeeder

# Check for inactive features
php artisan db:seed --class=VerifyPackageFeatureIntegritySeeder
```

**After Package Changes**:
```bash
# Clear package cache
php artisan cache:forget package_allowed_permissions_{package_id}

# Clear all school caches
php artisan db:seed --class=ClearFeatureCacheSeeder
```

**After Feature Changes**:
```bash
# Clear all feature-related caches
php artisan cache:flush

# Re-verify integrity
php artisan db:seed --class=VerifyPackageFeatureIntegritySeeder
```

### Performance Monitoring

**Check Cache Hit Rate**:
```bash
# Enable query log in config/database.php
DB::enableQueryLog();

# Access features
# Check query count
$queries = DB::getQueryLog();
count($queries); // Should be minimal with cache hits
```

**Monitor Feature Loading Time**:
```php
// In debug endpoint or controller
$start = microtime(true);
$features = $school->getAllowedFeatures();
$duration = microtime(true) - $start;

Log::info('Feature loading performance', [
    'duration_ms' => $duration * 1000,
    'feature_count' => $features->count(),
    'cache_hit' => Cache::has("school_features_{$school->id}"),
]);
```

---

## Summary

### Quick Reference Card

**Problem**: School 2 has zero features
1. `php artisan db:seed --class=DiagnoseSchool2FeaturesSeeder`
2. Check output for ❌ indicators
3. Apply recommended fixes
4. `php artisan cache:clear`
5. Test with `php artisan diagnose:school-features 2 --compare-with=1`

**Logging**:
- School logs: `grep "\[SCHOOL\]" storage/logs/laravel.log`
- Package logs: `grep "\[PACKAGE\]" storage/logs/laravel.log`

**Debug Endpoint**: `/debug/feature-access` (requires auth)

**Tools**:
- Seeder: `DiagnoseSchool2FeaturesSeeder` (comprehensive check)
- Command: `diagnose:school-features {id}` (interactive)
- Endpoint: Enhanced `/debug/feature-access` (real-time)

**Common Fixes**:
1. Inactive features: `UPDATE permission_features SET status = 1`
2. Missing features: `php artisan db:seed --class=AddStaffManagementFeaturesToPackageSeeder`
3. Poisoned cache: `php artisan cache:forget school_features_2`
4. Broken relationships: Check permission_features.permission_id validity

---

## Contact & Support

For persistent issues after following this guide:
1. Collect all diagnostic output
2. Export recent logs: `tail -500 storage/logs/laravel.log > debug_logs.txt`
3. Run all diagnostic tools and save output
4. Document exact steps taken and results observed

This comprehensive logging system will pinpoint the exact breaking point in School 2's feature resolution chain.
