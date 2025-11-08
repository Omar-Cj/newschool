# FixMigrationStatusSeeder - Integration Guide

## Quick Start

The seeder is ready to use immediately with no additional configuration needed.

## Run Standalone (Recommended for First-Time Setup)

```bash
php artisan db:seed --class=FixMigrationStatusSeeder
```

This is the simplest approach and should be your first step after migrations.

## Integration with DatabaseSeeder

If you want to include this seeder in your main database seeding process, follow these steps:

### Step 1: Edit DatabaseSeeder.php

Open `database/seeders/DatabaseSeeder.php` and add the import at the top:

```php
<?php

namespace Database\Seeders;

use App\Models\Currency;
use Illuminate\Database\Seeder;
use Database\Seeders\RoleSeeder;
use Database\Seeders\UserSeeder;
use Database\Seeders\SearchSeeder;
use Database\Seeders\UploadSeeder;
use Database\Seeders\SettingSeeder;
use Illuminate\Support\Facades\Log;
use Database\Seeders\FlagIconSeeder;
use Database\Seeders\LanguageSeeder;
use Database\Seeders\FixMigrationStatusSeeder;  // ADD THIS LINE
// ... rest of imports
```

### Step 2: Add to Seeders Array

In the `run()` method, add the seeder to the beginning of the `$seeders` array (for both APP_DEMO=true and APP_DEMO=false sections):

#### For APP_DEMO mode (production-like):
```php
public function run()
{
    $seeders = [];

    if (env('APP_DEMO')) {
        $seeders = [
            FixMigrationStatusSeeder::class,  // ADD THIS - run FIRST
            UploadSeeder::class,
            RoleSeeder::class,
            // ... rest of seeders
        ];
    } else {
        $seeders = [
            FixMigrationStatusSeeder::class,  // ADD THIS - run FIRST
            UploadSeeder::class,
            RoleSeeder::class,
            // ... rest of seeders
        ];

        Log::info('Seeders:', $seeders);
        // ... rest of code
    }

    $this->call($seeders);
}
```

### Step 3: Run the Combined Seeder

After integration, you can run all seeders together:

```bash
php artisan db:seed
```

Or with specific environment:

```bash
php artisan db:seed --env=local
php artisan db:seed --env=production
```

## Execution Order

**Important**: The `FixMigrationStatusSeeder` should run **FIRST** before other seeders because:

1. It ensures migration status is correct
2. It creates the foundational `Package` record (ID=1)
3. It creates the foundational `School` record (ID=1)
4. Other seeders may depend on these default records

Recommended order:
```
1. FixMigrationStatusSeeder    (fixes migrations, creates package & school)
2. UploadSeeder               (creates upload base)
3. RoleSeeder                 (creates roles)
4. DesignationSeeder          (creates designations)
5. PermissionSeeder           (creates permissions)
6. UserSeeder                 (creates users - depends on roles)
7. ... (rest of seeders)
```

## Configuration Options

### Environment-Specific Behavior

The seeder respects Laravel's environment configuration:

```bash
# For local development
APP_ENV=local php artisan db:seed --class=FixMigrationStatusSeeder

# For testing
APP_ENV=testing php artisan db:seed --class=FixMigrationStatusSeeder

# For production
APP_ENV=production php artisan db:seed --class=FixMigrationStatusSeeder
```

### Conditional Integration

If you want the seeder to run only in certain environments, modify DatabaseSeeder.php:

```php
public function run()
{
    $seeders = [];

    // Always include migration fixer in development and testing
    if (in_array(env('APP_ENV'), ['local', 'testing'])) {
        $seeders[] = FixMigrationStatusSeeder::class;
    }

    // ... rest of seeders
    $this->call($seeders);
}
```

## Database Preparation Workflow

### Fresh Setup
```bash
# 1. Run migrations to create tables
php artisan migrate --fresh

# 2. Seed with FixMigrationStatusSeeder
php artisan db:seed --class=FixMigrationStatusSeeder

# 3. Verify migration status
php artisan migrate:status
```

### Existing Database
```bash
# 1. Check current migration status
php artisan migrate:status

# 2. Run pending migrations if any
php artisan migrate

# 3. Seed to fix status and create defaults
php artisan db:seed --class=FixMigrationStatusSeeder
```

### Reset and Seed
```bash
# Complete reset (careful in production!)
php artisan migrate:fresh --seed

# If integrated into DatabaseSeeder, will automatically run FixMigrationStatusSeeder
```

## Verification Steps

After running the seeder, verify setup with these commands:

```bash
# Check migrations are marked complete
php artisan tinker
DB::table('migrations')->count();  // Should see 18+ migrations
DB::table('migrations')->where('batch', 100)->count();  // Should see 18

# Verify package exists
Modules\MainApp\Entities\Package::find(1);

# Verify school exists
Modules\MainApp\Entities\School::find(1);

# Verify subscription exists
Modules\MainApp\Entities\Subscription::first();

# Exit tinker
exit
```

## Troubleshooting Integration

### Issue: Seeder Not Found
**Error**: `Class 'Database\Seeders\FixMigrationStatusSeeder' not found`

**Solution**: Ensure the file is in the correct location:
```bash
ls database/seeders/FixMigrationStatusSeeder.php
```

### Issue: Foreign Key Constraint
**Error**: `Integrity constraint violation: 1452`

**Solution**: The seeder must run AFTER migrations. Ensure:
```bash
php artisan migrate --refresh
php artisan db:seed --class=FixMigrationStatusSeeder
```

### Issue: Duplicate Key Error
**Error**: `Duplicate entry '1' for key 'schools.PRIMARY'`

**Solution**: The seeder is safe to run multiple times. If you get this error, it means records already exist. Check:
```bash
php artisan tinker
Modules\MainApp\Entities\School::find(1);
exit
```

### Issue: Wrong Column Types
**Error**: `SQLSTATE[HY000]: General error: 1366`

**Solution**: Ensure all migrations ran successfully:
```bash
php artisan migrate:status
# Look for pending migrations
php artisan migrate
```

## Advanced Configuration

### Custom Batch Number

To use a different batch number instead of 100, modify the seeder:

```php
// In FixMigrationStatusSeeder.php
DB::table('migrations')->insert([
    'migration' => $migration,
    'batch' => 200,  // Change from 100 to any number
]);
```

### Custom Package Details

To use different default package values:

```php
// In FixMigrationStatusSeeder.php
$defaultPackage = Package::firstOrCreate(
    ['id' => 1],
    [
        'name' => 'My Custom Package',  // Change name
        'price' => 99.99,                // Change price
        'student_limit' => 500,          // Change limit
        // ... rest of fields
    ]
);
```

### Custom School Details

To use different default school values:

```php
// In FixMigrationStatusSeeder.php
$mainSchool = School::firstOrCreate(
    ['id' => 1],
    [
        'name' => 'My School Name',
        'email' => 'admin@myschool.com',
        'phone' => '+1-555-1234-5678',
        'address' => 'Your School Address',
        // ... rest of fields
    ]
);
```

## Monitoring Seeder Execution

### Enable Logging

Add detailed logging to track execution:

```bash
# Check logs after seeding
tail -f storage/logs/laravel.log | grep -i "seeder\|migration"
```

### Verbose Output

```bash
# Run with verbose flag
php artisan db:seed --class=FixMigrationStatusSeeder -vvv
```

## Related Commands

```bash
# View all seeders
find database/seeders -name "*.php" -type f | grep -v __MACOSX

# List specific seeder
php artisan tinker
require 'database/seeders/FixMigrationStatusSeeder.php';
exit

# Check migration table
php artisan migrate:status

# Reset all and reseed
php artisan migrate:fresh --seed
```

## Performance Considerations

- Seeder execution time: < 1 second
- Records created: 4 (1 package, 1 school, 1 subscription, 18 migration records)
- Database impact: Minimal
- Safe for production: Yes (idempotent)

## Security Checklist

Before production deployment:

- [ ] Update placeholder email from `admin@mainschool.com`
- [ ] Update placeholder phone number
- [ ] Update placeholder address
- [ ] Set appropriate package pricing if not free
- [ ] Configure subscription expiry date appropriately
- [ ] Review all created records
- [ ] Run migrations to verify no errors
- [ ] Test authentication and authorization

## Support & Debugging

For issues:

1. Check logs: `storage/logs/laravel.log`
2. Review this guide: Look for your error message in Troubleshooting
3. Check database state: Use `php artisan tinker`
4. Verify file location: Confirm seeder file exists
5. Review configuration: Check `.env` file settings

---

**Last Updated**: November 5, 2025
**Integration Version**: 1.0
