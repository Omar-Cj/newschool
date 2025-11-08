# School ID Migration - Quick Reference Guide

## Migration Files

### File 1: Add Columns
**File**: `database/migrations/tenant/2025_01_01_000001_add_school_id_to_all_tables.php`
- Adds `school_id` column to 130+ tables
- Adds nullable `school_id` to users table
- Runs first

### File 2: Populate Data (THIS FILE)
**File**: `database/migrations/tenant/2025_11_05_000002_populate_school_id_for_existing_data.php`
- Populates existing records with `school_id = 1`
- Special logic for users table based on role_id
- Runs second

---

## Quick Execution

### Single School Installation
```bash
php artisan migrate
```

### Multi-Tenant SaaS
```bash
php artisan tenants:migrate
# or for specific tenant
php artisan tenants:migrate --tenant=abc123
```

---

## What Gets Updated

### All 130+ Tables → school_id = 1
Including: students, staff, fees, exams, attendance, etc.

### Users Table (Special Logic)
- Regular users (role_id != 1): school_id = 1
- Admin users (role_id = 1): school_id = NULL

---

## Verification

```bash
# Check if migration ran
php artisan migrate:status

# Verify data population
php artisan tinker
> DB::table('students')->whereNull('school_id')->count()  // Should be 0
> DB::table('users')->where('role_id', 1)->where('school_id', '!=', null)->count()  // Should be 0
```

---

## Rollback

```bash
php artisan migrate:rollback --step=1
```

---

## Troubleshooting

| Issue | Cause | Solution |
|-------|-------|----------|
| Migration hangs | Large dataset | Run during off-peak hours |
| school_id still NULL | Migration didn't run | Check migrations table, re-run |
| Admin users have school_id = 1 | Incorrect role_id | Verify role_id = 1 in roles table |
| Table not found error | Optional module missing | Expected, migration continues |

---

## Key Points

✓ Uses batch updates (efficient)
✓ Handles 130+ tables
✓ Special logic for users/admins
✓ Detailed logging
✓ Rollback safe
✓ Multi-tenant ready

---

## Performance

- Small database (< 100k records): < 10 seconds
- Medium database (100k-1M): 10-60 seconds
- Large database (> 1M): 1-5 minutes

---

## Logs Location

Check: `storage/logs/laravel.log`

Sample output:
```
Starting population of school_id for existing data
Table 'students': Updated 150 records with school_id = 1
Users table: Updated 45 regular users with school_id = 1
Users table: 2 admin users kept with NULL school_id
Successfully completed population of school_id
```
