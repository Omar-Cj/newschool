# SchoolContext Middleware - Setup & Configuration Guide

Complete guide for setting up and configuring the SchoolContext middleware in your Laravel application.

---

## Table of Contents
1. [Prerequisites](#prerequisites)
2. [Installation Steps](#installation-steps)
3. [Configuration](#configuration)
4. [Database Setup](#database-setup)
5. [Kernel Registration](#kernel-registration)
6. [Route Configuration](#route-configuration)
7. [Model Updates](#model-updates)
8. [Testing Configuration](#testing-configuration)
9. [Troubleshooting](#troubleshooting)

---

## Prerequisites

### Required
- Laravel 8.0+
- PHP 7.4+
- MySQL/PostgreSQL with multi-tenant schema
- Existing User and Branch/School models
- Branch/School table with `id` and `name` columns

### Project Structure
```
app/
├── Http/
│   ├── Middleware/
│   │   ├── SchoolContext.php (to be created)
│   │   └── ...
│   ├── Controllers/
│   └── Kernel.php
├── Models/
│   ├── User.php
│   └── ...
├── Enums/
│   └── RoleEnum.php
└── ...
```

---

## Installation Steps

### Step 1: Copy Middleware File

The middleware is already created at `/app/Http/Middleware/SchoolContext.php`

Verify it exists:
```bash
ls -la app/Http/Middleware/SchoolContext.php
```

### Step 2: Check Dependencies

Ensure you have the required enums:

```bash
# Check RoleEnum exists
ls -la app/Enums/RoleEnum.php
```

### Step 3: Verify User Model

Update User model if needed:

```php
// app/Models/User.php
class User extends Authenticatable
{
    // ... existing code

    protected $fillable = [
        'name',
        'email',
        'password',
        'branch_id',  // REQUIRED
        'role_id',    // REQUIRED
        // ... other fields
    ];

    // Relationship to Role (if using)
    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    // Relationship to Branch/School
    public function school()
    {
        return $this->belongsTo(Branch::class, 'branch_id', 'id');
    }
}
```

---

## Configuration

### Application Configuration

#### Enable Multi-Tenancy Mode

```php
// config/app.php

return [
    'name' => env('APP_NAME', 'School Management'),

    // Enable multi-tenant mode
    'saas_enabled' => env('APP_SAAS', true),

    // ... other config
];
```

Add to `.env`:
```env
APP_SAAS=true
APP_SCHOOL_MODEL=App\\Models\\Branch
```

#### Cache Configuration

Update cache configuration for school data:

```php
// config/cache.php

return [
    'default' => env('CACHE_DRIVER', 'database'),

    'stores' => [
        'database' => [
            'driver' => 'database',
            'table' => 'cache',
            'prefix' => env('CACHE_PREFIX', 'school_'),
        ],
        // ... other drivers
    ],

    // Cache school data for 1 hour
    'school_ttl' => 3600,
];
```

#### Session Configuration

Ensure session driver supports multi-tenancy:

```php
// config/session.php

return [
    'driver' => env('SESSION_DRIVER', 'database'),
    'lifetime' => env('SESSION_LIFETIME', 120),
    'expire_on_close' => false,

    'table' => 'sessions',
    'store' => null,
];
```

Add to `.env`:
```env
SESSION_DRIVER=database
SESSION_LIFETIME=120
```

---

## Database Setup

### Create Branches Table

If you don't have a branches/schools table:

```bash
php artisan make:migration create_branches_table
```

```php
// database/migrations/xxxx_xx_xx_xxxxxx_create_branches_table.php

return new class extends Migration
{
    public function up()
    {
        Schema::create('branches', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->string('code')->unique();
            $table->string('location')->nullable();
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->text('address')->nullable();
            $table->string('principal_name')->nullable();
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->timestamps();

            $table->index('status');
            $table->index('code');
        });
    }

    public function down()
    {
        Schema::dropIfExists('branches');
    }
};
```

Run migration:
```bash
php artisan migrate
```

### Add branch_id to Users Table

If not already present:

```bash
php artisan make:migration add_branch_id_to_users_table
```

```php
// database/migrations/xxxx_xx_xx_xxxxxx_add_branch_id_to_users_table.php

return new class extends Migration
{
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'branch_id')) {
                $table->unsignedBigInteger('branch_id')
                    ->default(1)
                    ->after('id');

                $table->foreign('branch_id')
                    ->references('id')
                    ->on('branches')
                    ->onDelete('cascade');

                $table->index('branch_id');
            }
        });
    }

    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['branch_id']);
            $table->dropColumn('branch_id');
        });
    }
};
```

Run migration:
```bash
php artisan migrate
```

### Add branch_id to Related Tables

Add branch_id to all school-related tables:

```bash
php artisan make:migration add_branch_id_to_all_school_tables
```

```php
// database/migrations/xxxx_xx_xx_xxxxxx_add_branch_id_to_all_school_tables.php

return new class extends Migration
{
    protected $tables = [
        'students',
        'staff',
        'attendance',
        'fees_collect',
        'class_rooms',
        'subjects',
        'exams',
        // Add all school-related tables
    ];

    public function up()
    {
        foreach ($this->tables as $table) {
            if (Schema::hasTable($table) && !Schema::hasColumn($table, 'branch_id')) {
                Schema::table($table, function (Blueprint $table) {
                    $table->unsignedBigInteger('branch_id')
                        ->default(1)
                        ->after('id');

                    $table->foreign('branch_id')
                        ->references('id')
                        ->on('branches')
                        ->onDelete('cascade');

                    $table->index('branch_id');
                });
            }
        }
    }

    public function down()
    {
        foreach ($this->tables as $table) {
            if (Schema::hasTable($table)) {
                Schema::table($table, function (Blueprint $table) {
                    if (Schema::hasColumn($table->getTable(), 'branch_id')) {
                        $table->dropForeign(['branch_id']);
                        $table->dropColumn('branch_id');
                    }
                });
            }
        }
    }
};
```

### Create Sessions Table

If not already present:

```bash
php artisan session:table
php artisan migrate
```

---

## Kernel Registration

### Add to HTTP Middleware

```php
// app/Http/Kernel.php

protected $middleware = [
    // ... other middleware
    \App\Http\Middleware\SchoolContext::class,
];
```

### Or Add to Route Middleware

```php
// app/Http/Kernel.php

protected $routeMiddleware = [
    // ... other middleware
    'school-context' => \App\Http\Middleware\SchoolContext::class,
];
```

### Create Alias

Add to kernel for easy reference:

```php
protected $routeMiddleware = [
    'auth' => \App\Http\Middleware\Authenticate::class,
    'school-context' => \App\Http\Middleware\SchoolContext::class,
    'school-admin' => \App\Http\Middleware\SchoolAdminCheck::class, // Optional
];
```

---

## Route Configuration

### Apply to Route Groups

```php
// routes/web.php

Route::middleware(['auth', 'verified', 'school-context'])
    ->group(function () {
        Route::resource('dashboard', DashboardController::class);
        Route::resource('students', StudentController::class);
        Route::resource('staff', StaffController::class);
    });
```

### Apply to Specific Routes

```php
Route::get('students', [StudentController::class, 'index'])
    ->middleware(['auth', 'school-context']);
```

### Create Admin Routes Group

```php
Route::middleware(['auth', 'admin', 'school-context'])
    ->prefix('admin')
    ->group(function () {
        Route::get('dashboard', [AdminDashboardController::class, 'index']);
        Route::post('switch-school/{school}', [AdminDashboardController::class, 'switchSchool']);
    });
```

---

## Model Updates

### Create Base Model with Scopes

```bash
php artisan make:model BaseModel
```

```php
// app/Models/BaseModel.php

namespace App\Models;

use App\Http\Middleware\SchoolContext;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

abstract class BaseModel extends Model
{
    /**
     * Scope queries to current school
     */
    public function scopeCurrentSchool(Builder $query): Builder
    {
        $schoolId = SchoolContext::getSessionSchoolId();

        if ($schoolId) {
            return $query->where('branch_id', $schoolId);
        }

        return $query;
    }

    /**
     * Scope queries to specific school
     */
    public function scopeForSchool(Builder $query, int $schoolId): Builder
    {
        return $query->where('branch_id', $schoolId);
    }

    /**
     * Auto-set school on creation
     */
    public static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (!$model->branch_id && !request()?->input('branch_id')) {
                $model->branch_id = SchoolContext::getSessionSchoolId();
            }
        });
    }
}
```

### Update Models to Use BaseModel

```php
// app/Models/Student.php

namespace App\Models;

class Student extends BaseModel // Changed from Model
{
    use HasFactory;

    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'branch_id',
        // ... other fields
    ];
}
```

---

## Testing Configuration

### Set Up Test Traits

```php
// tests/Traits/WithSchoolContext.php

namespace Tests\Traits;

use App\Models\Branch;
use App\Models\User;
use App\Enums\RoleEnum;

trait WithSchoolContext
{
    protected ?Branch $school = null;
    protected ?User $schoolUser = null;
    protected ?User $adminUser = null;

    protected function setUpSchoolContext(): void
    {
        $this->school = Branch::factory()->create();

        $this->schoolUser = User::factory()->create([
            'branch_id' => $this->school->id,
            'role_id' => RoleEnum::STAFF,
        ]);

        $this->adminUser = User::factory()->create([
            'role_id' => RoleEnum::ADMIN,
        ]);
    }

    protected function actingAsSchoolUser()
    {
        return $this->actingAs($this->schoolUser);
    }

    protected function actingAsAdmin()
    {
        return $this->actingAs($this->adminUser);
    }
}
```

### Use in Tests

```php
// tests/Feature/StudentControllerTest.php

namespace Tests\Feature;

use Tests\TestCase;
use Tests\Traits\WithSchoolContext;

class StudentControllerTest extends TestCase
{
    use RefreshDatabase, WithSchoolContext;

    public function setUp(): void
    {
        parent::setUp();
        $this->setUpSchoolContext();
    }

    public function test_school_user_can_view_students()
    {
        $response = $this->actingAsSchoolUser()
            ->get(route('students.index'));

        $response->assertOk();
    }
}
```

---

## Troubleshooting

### Issue: Middleware Not Registering

**Symptom**: School context variables not available in views

**Solution**:
1. Check `app/Http/Kernel.php` for registration
2. Verify middleware is in correct array (global or route)
3. Restart development server: `php artisan serve`

### Issue: branch_id Always Null

**Symptom**: School context returns null

**Solution**:
```bash
# Check user has branch_id set
php artisan tinker
User::first()->branch_id
# Should not be null

# If null, update users
User::where('branch_id', null)->update(['branch_id' => 1]);
```

### Issue: Session Loses Data Between Requests

**Symptom**: session('school_id') is null

**Solution**:
```php
// Check session is started
route()->middleware('web')->group(function() {
    // Your routes
});

// Or verify session driver is correct
php artisan config:clear
php artisan cache:clear
```

### Issue: Admin Context Not Switching

**Symptom**: setAdminSchoolContext doesn't work

**Solution**:
```php
// Verify user is admin
if (Auth::user()->role_id != RoleEnum::ADMIN) {
    dd('User is not admin');
}

// Check role_id values
dd(RoleEnum::ADMIN); // Should be 2
```

### Issue: Cross-School Access Not Blocked

**Symptom**: Users can access other schools' data

**Solution**:
```php
// Add authorization check
if (!SchoolContext::userBelongsToSchool($request, $item->branch_id)) {
    abort(403);
}

// Or use policy
$this->authorize('view', $item);
```

---

## Verification Checklist

Before going to production:

- [ ] Middleware file created at `/app/Http/Middleware/SchoolContext.php`
- [ ] Middleware registered in `app/Http/Kernel.php`
- [ ] Users table has `branch_id` column
- [ ] Branches/schools table exists with required columns
- [ ] All school-related tables have `branch_id` column
- [ ] RoleEnum class exists with admin role constants
- [ ] Models use BaseModel or include scopes
- [ ] Routes use middleware: `['auth', 'school-context']`
- [ ] Controllers verify school access
- [ ] Tests pass with school context
- [ ] Admin context switching tested
- [ ] Cross-school access properly denied
- [ ] Session driver configured
- [ ] Cache driver configured
- [ ] All migrations run successfully

---

## Quick Start Script

Run this to verify setup:

```bash
#!/bin/bash

echo "Checking SchoolContext setup..."

# Check middleware file
if [ -f "app/Http/Middleware/SchoolContext.php" ]; then
    echo "✓ Middleware file exists"
else
    echo "✗ Middleware file missing"
fi

# Check User model for branch_id
if grep -q "branch_id" "app/Models/User.php"; then
    echo "✓ User model has branch_id"
else
    echo "✗ User model missing branch_id"
fi

# Check RoleEnum
if [ -f "app/Enums/RoleEnum.php" ]; then
    echo "✓ RoleEnum exists"
else
    echo "✗ RoleEnum missing"
fi

# Check migrations
php artisan migrate:status | grep -q "branches"
if [ $? -eq 0 ]; then
    echo "✓ Branches table exists"
else
    echo "✗ Branches table not found"
fi

echo "Setup verification complete"
```

---

## Next Steps

1. **Register middleware** in Kernel
2. **Create/update database** with branch tables
3. **Update models** to use BaseModel
4. **Test in development** with multiple schools
5. **Set up tests** with school context
6. **Review security** of authorization
7. **Configure logging** for audit trail
8. **Deploy to staging** and test thoroughly
9. **Document API** changes for integration
10. **Train team** on school context patterns

---

## Support & Documentation

- **Full Documentation**: `/claudedocs/SCHOOLCONTEXT_MIDDLEWARE.md`
- **Code Examples**: `/claudedocs/SCHOOLCONTEXT_EXAMPLES.md`
- **Quick Reference**: `/claudedocs/SCHOOLCONTEXT_QUICK_REFERENCE.md`
- **Middleware**: `/app/Http/Middleware/SchoolContext.php`

---

## Common Next Decisions

### Should I implement global scopes?

**Yes** - Add global scopes to automatically filter by school:
```php
protected static function booted()
{
    static::addGlobalScope(new SchoolScope());
}
```

### Should I cache school data?

**Yes** - Cache branches to reduce queries:
```php
$school = Cache::remember("school_{$id}", 3600, fn() => Branch::find($id));
```

### Should I audit context switches?

**Yes** - Log admin context switches:
```php
Log::info('Admin switched school context', [
    'admin_id' => Auth::id(),
    'from_school' => old_context,
    'to_school' => new_context,
]);
```

### Should I use policies?

**Yes** - Implement authorization policies:
```php
public function view(User $user, Student $student): bool
{
    return $user->branch_id === $student->branch_id || $user->isAdmin();
}
```

---

## Performance Considerations

1. **Index branch_id** on all school tables
2. **Use eager loading** for school relationships
3. **Cache school data** for 1 hour
4. **Implement query scopes** for automatic filtering
5. **Monitor slow queries** related to school filtering
6. **Consider denormalization** for frequently joined data

---

## Security Considerations

1. **Always verify school ownership** before data access
2. **Never trust client-provided school_id**
3. **Log all admin context switches**
4. **Implement rate limiting** for context switches
5. **Clear admin context** immediately after use
6. **Test cross-school access denial** thoroughly
7. **Implement audit logging** for sensitive operations
8. **Review permissions regularly** across schools
