# Migration Guide: Adding school_id to Buses Table

## Overview
This guide covers the steps to add the missing `school_id` column to the `buses` table, which is critical for multi-tenant isolation.

## Files Created/Modified

### 1. Migration File
- **Location**: `database/migrations/tenant/2025_11_16_000001_add_school_id_to_buses_table.php`
- **Purpose**: Adds school_id column, index, and updates unique constraint

### 2. Model Update
- **Location**: `app/Models/Transportation/Bus.php`
- **Change**: Added `'school_id'` to `$fillable` array (line 52)

## Step-by-Step Migration Process

### Step 1: Run the Migration

For **SaaS Multi-Tenant** setup:
```bash
php artisan migrate --path=database/migrations/tenant
```

For **Single School** installation:
```bash
php artisan migrate
```

**Expected Output**:
```
Migrating: 2025_11_16_000001_add_school_id_to_buses_table
Migrated:  2025_11_16_000001_add_school_id_to_buses_table (XX.XXms)
```

### Step 2: Populate school_id for Existing Buses

You currently have **6 buses** in the database that need school_id values assigned.

#### Option A: All Buses Belong to One School (Most Common)

If all existing buses belong to school_id = 1:

```sql
-- Update all existing buses to belong to school_id = 1
UPDATE buses SET school_id = 1 WHERE school_id IS NULL OR school_id = 0;
```

Run via Artisan Tinker:
```bash
php artisan tinker
>>> DB::table('buses')->whereNull('school_id')->orWhere('school_id', 0)->update(['school_id' => 1]);
>>> exit
```

#### Option B: Map Buses by Branch ID

If you need to assign buses based on which branch they belong to:

```sql
-- First, check which schools your branches belong to
SELECT id, name, school_id FROM branches;

-- Then update buses based on branch->school_id mapping
-- Example: If branch 1 belongs to school 1
UPDATE buses b
INNER JOIN branches br ON b.branch_id = br.id
SET b.school_id = br.school_id
WHERE b.school_id IS NULL OR b.school_id = 0;
```

Run via Artisan Tinker:
```bash
php artisan tinker
>>> DB::table('buses as b')
    ->join('branches as br', 'b.branch_id', '=', 'br.id')
    ->whereNull('b.school_id')
    ->orWhere('b.school_id', 0)
    ->update(['b.school_id' => DB::raw('br.school_id')]);
>>> exit
```

#### Option C: Manual Assignment Per Bus

If you need to manually assign each bus:

```bash
php artisan tinker
```

```php
// List all buses
$buses = DB::table('buses')->get(['id', 'area_name', 'branch_id', 'school_id']);
foreach($buses as $bus) {
    echo "Bus ID: {$bus->id} | Area: {$bus->area_name} | Branch: {$bus->branch_id} | School: {$bus->school_id}\n";
}

// Update individual buses
DB::table('buses')->where('id', 1)->update(['school_id' => 1]);
DB::table('buses')->where('id', 2)->update(['school_id' => 1]);
// ... repeat for each bus

exit
```

### Step 3: Verify the Migration

#### Check Database Structure
```bash
php artisan tinker
>>> Schema::hasColumn('buses', 'school_id');  // Should return: true
>>> DB::select('SHOW CREATE TABLE buses');
>>> exit
```

**Expected Columns**:
- `id`, `school_id`, `area_name`, `bus_number`, `capacity`, `driver_name`, `driver_phone`, `license_plate`, `status`, `branch_id`, `created_at`, `updated_at`

**Expected Indexes**:
- PRIMARY KEY: `id`
- INDEX: `buses_branch_id_index`
- INDEX: `buses_school_id_index` ✅ NEW
- UNIQUE: `buses_area_name_branch_school_unique` ✅ UPDATED

#### Check Existing Data
```bash
php artisan tinker
>>> DB::table('buses')->select('id', 'area_name', 'school_id')->get();
```

**Expected**: All 6 buses should have valid `school_id` values (NOT NULL, NOT 0)

### Step 4: Test Multi-Tenant Isolation

#### Test 1: Verify Auto-Scoping Works
```bash
php artisan tinker
```

```php
// Login as user from school 1
$user = App\Models\User::where('school_id', 1)->first();
auth()->login($user);

// This should ONLY return buses from school 1
$buses = App\Models\Transportation\Bus::all();
echo "Count: " . $buses->count() . "\n";
foreach($buses as $bus) {
    echo "Bus: {$bus->area_name} | School: {$bus->school_id}\n";
}

// Login as user from school 2 (if exists)
$user2 = App\Models\User::where('school_id', 2)->first();
if($user2) {
    auth()->login($user2);
    $buses2 = App\Models\Transportation\Bus::all();
    echo "School 2 buses count: " . $buses2->count() . "\n";
}

exit
```

**Expected Behavior**:
- School 1 user sees ONLY school 1 buses
- School 2 user sees ONLY school 2 buses
- System admin (school_id = NULL) sees ALL buses

#### Test 2: Verify Auto-Assignment on Create
```bash
php artisan tinker
```

```php
// Login as user from school 1
$user = App\Models\User::where('school_id', 1)->first();
auth()->login($user);

// Create new bus (should auto-get school_id = 1)
$bus = new App\Models\Transportation\Bus();
$bus->area_name = 'Test Route ' . time();
$bus->branch_id = 1;
$bus->save();

// Check if school_id was auto-assigned
echo "New bus school_id: " . $bus->school_id . "\n";  // Should be 1

// Clean up test data
$bus->delete();

exit
```

**Expected**: New bus automatically gets `school_id = 1` from authenticated user

#### Test 3: Verify UI Filtering
1. Login to the school dashboard as a school admin
2. Navigate to: **Transportation → Buses**
3. Verify you ONLY see buses belonging to your school
4. Try to manually access another school's bus by URL:
   ```
   https://your-domain/bus/edit/999
   ```
   Where 999 is a bus ID from another school
5. **Expected**: 404 error or "Access Denied" (BaseModel auto-scoping blocks it)

### Step 5: Clear Caches
```bash
php artisan optimize:clear
```

Or individually:
```bash
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear
```

## Verification Checklist

After completing all steps, verify:

- [ ] Migration ran successfully
- [ ] `school_id` column exists in `buses` table
- [ ] `buses_school_id_index` index exists
- [ ] Unique constraint updated to `buses_area_name_branch_school_unique`
- [ ] All 6 existing buses have valid `school_id` values (check via SQL)
- [ ] `school_id` added to Bus model's `$fillable` array
- [ ] BaseModel auto-scoping works (Test 1 passed)
- [ ] New buses auto-get `school_id` (Test 2 passed)
- [ ] UI shows only current school's buses (Test 3 passed)
- [ ] Cannot access other schools' buses via direct URL
- [ ] Caches cleared

## Rollback (If Needed)

If you need to rollback this migration:

```bash
php artisan migrate:rollback --step=1
```

This will:
- Remove `school_id` column
- Restore old unique constraint (without school_id)
- Remove index

**⚠️ WARNING**: Rollback will LOSE the school_id data you populated!

## Troubleshooting

### Issue: Migration fails with "Duplicate key name 'buses_school_id_index'"
**Cause**: Index already exists
**Fix**:
```sql
ALTER TABLE buses DROP INDEX buses_school_id_index;
```
Then re-run migration.

### Issue: Migration fails with "Unknown column 'buses_area_name_branch_unique'"
**Cause**: Unique constraint has different name
**Fix**: Check actual constraint name:
```sql
SHOW CREATE TABLE buses;
```
Update migration file with correct constraint name.

### Issue: Buses still visible across schools
**Cause**: Caches not cleared or school_id values incorrect
**Fix**:
1. Clear all caches: `php artisan optimize:clear`
2. Verify school_id values: `SELECT id, area_name, school_id FROM buses;`
3. Check user's school_id: `SELECT id, name, school_id FROM users WHERE id = YOUR_USER_ID;`

### Issue: New buses get school_id = NULL
**Cause**: User not authenticated or user.school_id is NULL
**Fix**:
1. Ensure user is logged in: `auth()->check()`
2. Verify user has school_id: `auth()->user()->school_id`
3. Manually set if needed: `$bus->school_id = auth()->user()->school_id ?? 1;`

## Database Schema Reference

### Before Migration
```sql
CREATE TABLE `buses` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `area_name` varchar(255) NOT NULL,
  `bus_number` varchar(100) DEFAULT NULL,
  `capacity` int DEFAULT NULL,
  `driver_name` varchar(255) DEFAULT NULL,
  `driver_phone` varchar(50) DEFAULT NULL,
  `license_plate` varchar(100) DEFAULT NULL,
  `status` tinyint NOT NULL DEFAULT '1',
  `branch_id` bigint unsigned NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `buses_area_name_branch_unique` (`area_name`,`branch_id`),
  KEY `buses_branch_id_index` (`branch_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
```

### After Migration
```sql
CREATE TABLE `buses` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `school_id` bigint unsigned NOT NULL DEFAULT '1' COMMENT 'School ID for multi-tenant isolation',
  `area_name` varchar(255) NOT NULL,
  `bus_number` varchar(100) DEFAULT NULL,
  `capacity` int DEFAULT NULL,
  `driver_name` varchar(255) DEFAULT NULL,
  `driver_phone` varchar(50) DEFAULT NULL,
  `license_plate` varchar(100) DEFAULT NULL,
  `status` tinyint NOT NULL DEFAULT '1',
  `branch_id` bigint unsigned NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `buses_area_name_branch_school_unique` (`area_name`,`branch_id`,`school_id`),
  KEY `buses_branch_id_index` (`branch_id`),
  KEY `buses_school_id_index` (`school_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
```

## Security Impact

### Before Fix (CRITICAL VULNERABILITY)
- ❌ School A can view School B's buses
- ❌ School A can edit/delete School B's buses
- ❌ Students can be assigned to buses from other schools
- ❌ Complete multi-tenant isolation failure

### After Fix (SECURE)
- ✅ School A can ONLY view their own buses
- ✅ School A CANNOT access School B's buses
- ✅ Student bus assignments respect school boundaries
- ✅ Full multi-tenant isolation restored

## Support

If you encounter issues:
1. Check Laravel logs: `storage/logs/laravel.log`
2. Check migration status: `php artisan migrate:status`
3. Verify database structure: `SHOW CREATE TABLE buses;`
4. Test with Tinker: `php artisan tinker`

---

**Migration Date**: 2025-11-16
**Priority**: CRITICAL
**Estimated Time**: 30-45 minutes
**Risk Level**: High (Multi-tenant security)
