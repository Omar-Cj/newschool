# FixMigrationStatusSeeder - Usage Guide

## Overview

The `FixMigrationStatusSeeder` is a Laravel database seeder that repairs migration status and bootstraps the school management system with essential default data.

### File Location
```
database/seeders/FixMigrationStatusSeeder.php
```

## What This Seeder Does

### 1. **Fixes Migration Status**
Marks all existing database table migrations as completed by recording them in the `migrations` table with batch 100:

- `2023_08_10_083847_create_packages_table`
- `2023_08_10_083848_create_schools_table`
- `2023_08_10_095949_create_package_children_table`
- `2023_08_14_100130_create_testimonials_table`
- `2023_08_16_052151_create_contacts_table`
- `2023_08_16_052418_create_subscribes_table`
- `2023_08_16_084459_create_sections_table`
- `2023_08_18_051726_create_frequently_asked_questions_table`
- `2023_08_18_093828_create_settings_table`
- `2023_08_18_102920_create_currencies_table`
- `2023_08_18_103633_create_languages_table`
- `2023_08_18_111510_create_flag_icons_table`
- `2023_08_21_070509_create_subscriptions_table`
- `2023_08_21_102229_create_users_table`
- `2025_05_09_092214_create_jobs_table`
- `2025_05_12_090223_create_failed_jobs_table`
- `2025_09_14_074026_update_batch_id_format_to_sequential`
- `2024_09_13_000001_add_fee_frequency_to_fees_types_table`

### 2. **Creates Default Package**
Creates a basic package with ID 1 if it doesn't exist:
- **Name**: "Basic Package"
- **Student Limit**: 1000
- **Staff Limit**: 100
- **Duration**: 12 months
- **Status**: Active
- **Price**: 0 (Free default package)

### 3. **Creates Main School**
Creates the primary school record with ID 1 if it doesn't exist:
- **ID**: 1
- **Name**: "Main School"
- **Email**: admin@mainschool.com
- **Package**: Links to the default package (ID 1)
- **Status**: Active
- **Phone**: +1-000-0000-0000 (placeholder)
- **Address**: School Address (placeholder)

### 4. **Creates Initial Subscription**
Creates a subscription for the main school:
- **School ID**: 1
- **Package ID**: 1
- **Status**: Approved (1)
- **Payment Status**: Paid (1)
- **Expiry Date**: 1 year from now
- **Features Enabled**:
  - Academic Management
  - Student Information System
  - Attendance Tracking
  - Fee Management
  - Examination System

## How to Run

### Option 1: Run Seeder Standalone
```bash
php artisan db:seed --class=FixMigrationStatusSeeder
```

### Option 2: Add to DatabaseSeeder
Edit `database/seeders/DatabaseSeeder.php`:

```php
public function run()
{
    $this->call([
        FixMigrationStatusSeeder::class,
        // ... other seeders
    ]);
}
```

Then run:
```bash
php artisan db:seed
```

### Option 3: Run with Specific Environment
```bash
php artisan db:seed --class=FixMigrationStatusSeeder --env=local
```

## Prerequisites

Ensure the following tables exist:
- `migrations` - For storing migration status
- `packages` - For package records
- `schools` - For school records
- `subscriptions` - For subscription records

If tables don't exist, run migrations first:
```bash
php artisan migrate
```

## Expected Output

When run successfully, you'll see:

```
Starting migration status fix...
Marked migration as completed: 2023_08_10_083847_create_packages_table
Marked migration as completed: 2023_08_10_083848_create_schools_table
[... more migrations ...]

Creating default Package...
Default package created successfully with ID: 1

Creating Main School...
Main school created successfully with ID: 1
School Email: admin@mainschool.com

Creating Subscription for Main School...
Subscription created successfully with ID: 1
Expiry Date: 2026-11-05

Migration status fix completed successfully!
Main School Details:
  - ID: 1
  - Name: Main School
  - Email: admin@mainschool.com
  - Status: Active
```

## Idempotency

The seeder is **idempotent** - it can be run multiple times safely:

- Migrations are only added if they don't already exist
- Package, School, and Subscription use `firstOrCreate()` to prevent duplicates
- Re-running reports what already exists without creating duplicates

## Customization

To customize the default values, edit the seeder:

```php
// Edit default package details
'name' => 'Your Package Name',
'price' => 99.99, // Change to non-free
'student_limit' => 500,
'staff_limit' => 50,

// Edit default school details
'name' => 'Your School Name',
'email' => 'contact@yourschool.com',
'phone' => '+1-XXX-XXXX-XXXX',
'address' => 'Your School Address',
```

## Database Tables Affected

| Table | Operation | Records |
|-------|-----------|---------|
| `migrations` | Insert | 18 migration records |
| `packages` | Insert/Update | 1 package record |
| `schools` | Insert/Update | 1 school record |
| `subscriptions` | Insert/Update | 1 subscription record |

## Rollback

To revert the seeder's changes:

```bash
# Delete the seeder-created records manually
DELETE FROM subscriptions WHERE school_id = 1;
DELETE FROM schools WHERE id = 1;
DELETE FROM packages WHERE id = 1;

# Remove migration records (optional - only if starting fresh)
DELETE FROM migrations WHERE batch = 100;
```

## Troubleshooting

### Foreign Key Constraint Error
**Issue**: `Integrity constraint violation: 1452 Cannot add or update a child row`

**Solution**: Ensure the package is created before the school:
```bash
# The seeder already handles this, but check table constraints
php artisan migrate --refresh
```

### Migration Not Found
**Issue**: Laravel reports missing migrations after seeding

**Solution**: This is expected behavior - the migrations table marks them as complete but the migration classes must exist:
```bash
# Verify migration files exist in:
ls Modules/MainApp/Database/Migrations/
ls database/migrations/
```

### Duplicate Entry Error
**Issue**: Seeder runs twice and creates duplicates

**Solution**: The seeder uses `firstOrCreate()` which prevents duplicates. If you get this error, check if the table constraints are properly set up:
```bash
# Verify unique constraints
php artisan tinker
DB::select('DESCRIBE schools')
```

## Security Notes

- Default credentials and placeholders should be updated in production
- Change the admin email from `admin@mainschool.com` to your actual school email
- Update placeholder phone and address fields with actual information
- The free default package (price: 0) is suitable for testing only

## API Access After Seeding

After running this seeder, you can immediately:

1. Create users associated with the main school
2. Create student and teacher accounts
3. Configure academic settings
4. Set up fee structures
5. Enable examination modules

Example:
```php
// In your application code
$school = School::find(1);
$package = $school->package; // Access the basic package
$subscription = $school->subscriptions()->first(); // Access subscription
```

## Related Seeder Files

- `database/seeders/GenderSeeder.php` - Creates gender types
- `database/seeders/DatabaseSeeder.php` - Main seeder orchestrator

## Additional Resources

- **Models**: `Modules/MainApp/Entities/{School,Package,Subscription}.php`
- **Migrations**: `Modules/MainApp/Database/Migrations/`
- **Enums**: `app/Enums/Status.php`

## Support

For issues or questions:
1. Check the troubleshooting section above
2. Review the Laravel Seeding documentation
3. Examine the migration files for table structure details
4. Check database logs for constraint violations

---

**Last Updated**: November 5, 2025
**Seeder Version**: 1.0
**Compatibility**: Laravel 8+ with nwidart/laravel-modules
