# School ID Implementation Notes

## Overview
This document provides implementation notes and best practices for working with the school_id columns added by the migration.

## File Locations
1. **Migration**: `database/migrations/tenant/2025_01_01_000001_add_school_id_to_all_tables.php`
2. **Migration Guide**: `MIGRATION_GUIDE.md`
3. **Tables Reference**: `SCHOOL_ID_TABLES_REFERENCE.md`

## Key Implementation Decisions

### 1. Column Properties
- **Type**: `unsignedBigInteger` - 64-bit integer for future scalability
- **Default Value**: 1 - Supports single-school installations out of the box
- **Nullable**:
  - `false` for business tables (enforces school assignment)
  - `true` for users table (allows admin users without school assignment)
- **Position**: After 'id' column for logical grouping
- **Indexed**: Yes - Automatic index for query performance

### 2. Migration Safety
The migration is built with production safety in mind:

#### Idempotency
```php
if (!Schema::hasColumn($tableName, 'school_id')) {
    // Only add if not exists
}
```
- Safe to run multiple times
- Won't fail if columns already exist
- Supports partial deployments

#### Table Existence Checks
```php
if (!Schema::hasTable($tableName)) {
    Log::warning("Table doesn't exist, skipping...");
    continue; // Don't fail, just continue
}
```
- Works with disabled modules
- Supports partial installations
- No hard failures for missing tables

#### Comprehensive Logging
```php
Log::info("Added school_id column to '{$tableName}' table");
Log::warning("Table '{$tableName}' does not exist...");
Log::error("Error during school_id migration...");
```
- All operations logged to `storage/logs/laravel.log`
- Helps with debugging and auditing
- Production-grade observability

### 3. Rollback Safety
The rollback mirrors the up() function with extra protection:

```php
// Drop index first
try {
    $blueprint->dropIndex(['school_id']);
} catch (\Exception $e) {
    Log::warning("Could not drop index...");
    // Continue anyway
}

// Then drop column
$blueprint->dropColumn('school_id');
```

- Gracefully handles missing indexes
- Doesn't crash on partial states
- Safe for rolling back from any state

## Implementation Patterns

### Global Scopes for Automatic Filtering

**Pattern**: Automatically filter by school_id in all queries

```php
// app/Models/Student.php
namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    protected static function booted(): void
    {
        static::addGlobalScope('school', function (Builder $query) {
            if (auth()->check() && auth()->user()->school_id) {
                $query->where('school_id', auth()->user()->school_id);
            }
        });
    }

    // Usage:
    // Student::all() - automatically filters by current user's school
    // Student::withoutGlobalScopes()->get() - bypass filtering if needed
}
```

**Benefits**:
- Prevents accidental cross-school data access
- Enforces school isolation by default
- Works transparently across all queries

### School Relationship Pattern

**Pattern**: Make school explicit in relationships

```php
// app/Models/School.php
class School extends Model
{
    public function students()
    {
        return $this->hasMany(Student::class);
    }

    public function staff()
    {
        return $this->hasMany(Staff::class);
    }

    public function classes()
    {
        return $this->hasMany(Class::class);
    }

    public function fees()
    {
        return $this->hasMany(FeeMaster::class);
    }
}

// app/Models/Student.php
class Student extends Model
{
    public function school()
    {
        return $this->belongsTo(School::class);
    }
}
```

**Usage**:
```php
// Eager load school
$students = Student::with('school')->get();

// Access school
$student->school->name; // "Primary School"

// Reverse relationship
$school->students(); // All students in this school
```

### Authorization Checks

**Pattern**: Verify school ownership before operations

```php
// app/Policies/StudentPolicy.php
class StudentPolicy
{
    public function update(User $user, Student $student): bool
    {
        return $user->school_id === $student->school_id
            && $user->hasPermission('students.edit');
    }

    public function delete(User $user, Student $student): bool
    {
        return $user->school_id === $student->school_id
            && $user->hasPermission('students.delete');
    }
}

// In Controller
public function update(Request $request, Student $student)
{
    $this->authorize('update', $student);
    // Safe to update - verified school ownership
    $student->update($request->validated());
}
```

### Query Building Pattern

**Pattern**: Consistent query building with school_id

```php
// app/Repositories/StudentRepository.php
class StudentRepository
{
    public function getBySchool(int $schoolId)
    {
        return Student::where('school_id', $schoolId)
            ->with(['class', 'guardian'])
            ->orderBy('name')
            ->get();
    }

    public function getActiveBySchool(int $schoolId)
    {
        return Student::where('school_id', $schoolId)
            ->where('status', 'active')
            ->with(['class', 'guardian'])
            ->get();
    }

    public function countBySchool(int $schoolId): int
    {
        return Student::where('school_id', $schoolId)->count();
    }
}
```

## Middleware for School Context

**Pattern**: Set school context for entire request

```php
// app/Http/Middleware/SetSchoolContext.php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class SetSchoolContext
{
    public function handle(Request $request, Closure $next)
    {
        if ($request->user()) {
            // Store in request for easy access
            $request->attributes->set('school_id', $request->user()->school_id);

            // Could also store in container
            app()->bind('current_school_id', fn() => $request->user()->school_id);
        }

        return $next($request);
    }
}

// In routes/api.php
Route::middleware(['auth:sanctum', 'set.school.context'])
    ->prefix('api/v1')
    ->group(function () {
        Route::apiResource('students', StudentController::class);
    });
```

## Testing with School ID

**Pattern**: Include school_id in test factories and seeding

```php
// database/factories/StudentFactory.php
class StudentFactory extends Factory
{
    public function definition(): array
    {
        return [
            'school_id' => 1,  // Default school
            'name' => $this->faker->name(),
            'email' => $this->faker->unique()->safeEmail(),
            'enrollment_number' => 'STU' . $this->faker->unique()->numberBetween(10000, 99999),
            'status' => 'active',
        ];
    }

    public function forSchool(int $schoolId): self
    {
        return $this->state(['school_id' => $schoolId]);
    }
}

// tests/Feature/StudentTest.php
class StudentTest extends TestCase
{
    public function test_admin_can_view_school_students()
    {
        $school = School::factory()->create();
        $admin = User::factory()->admin()->forSchool($school->id)->create();
        $students = Student::factory(5)->forSchool($school->id)->create();

        $response = $this->actingAs($admin)
            ->get('/api/v1/students');

        $response->assertOk()
            ->assertJsonCount(5);
    }

    public function test_cannot_access_other_school_students()
    {
        $school1 = School::factory()->create();
        $school2 = School::factory()->create();

        $admin1 = User::factory()->admin()->forSchool($school1->id)->create();
        $students2 = Student::factory(3)->forSchool($school2->id)->create();

        $response = $this->actingAs($admin1)
            ->get('/api/v1/students');

        // Should return empty list, not throw error
        $response->assertOk()
            ->assertJsonCount(0);
    }
}
```

## Data Migration Examples

### Scenario 1: Single School Migration
```bash
# All data goes to school_id = 1 automatically
php artisan migrate --path=database/migrations/tenant/2025_01_01_000001_add_school_id_to_all_tables.php

# No additional migration needed - defaults handle it
```

### Scenario 2: Multi-Branch to Multi-School
```php
// database/migrations/tenant/2025_01_02_000002_map_branches_to_schools.php
public function up()
{
    // Assuming you have branch_id in data
    DB::statement('UPDATE students SET school_id = branch_id WHERE school_id = 1');
    DB::statement('UPDATE staff SET school_id = branch_id WHERE school_id = 1');
    DB::statement('UPDATE classes SET school_id = branch_id WHERE school_id = 1');
}

public function down()
{
    // Reset to default
    DB::statement('UPDATE students SET school_id = 1');
    DB::statement('UPDATE staff SET school_id = 1');
    DB::statement('UPDATE classes SET school_id = 1');
}
```

### Scenario 3: Keeping Historic Data
```php
// If you need to keep branch_id alongside school_id
public function up()
{
    // Copy branch_id to school_id only if branch_id > 1
    DB::statement('UPDATE students SET school_id = COALESCE(branch_id, 1)');
}
```

## Performance Considerations

### 1. Composite Indexes
After base migration, consider these composite indexes for common queries:

```php
// database/migrations/tenant/2025_01_03_000003_add_composite_indexes.php
Schema::table('students', function (Blueprint $table) {
    $table->index(['school_id', 'class_id']);
    $table->index(['school_id', 'status']);
    $table->index(['school_id', 'created_at']);
});

Schema::table('marks_registers', function (Blueprint $table) {
    $table->index(['school_id', 'exam_id', 'class_id']);
});

Schema::table('attendances', function (Blueprint $table) {
    $table->index(['school_id', 'attendance_date', 'student_id']);
});
```

### 2. Query Optimization Tips
```php
// Good - indexed columns
Student::where('school_id', $schoolId)
    ->where('status', 'active')
    ->get();

// Avoid - using LIKE on indexed column loses index benefit
Student::where('school_id', $schoolId)
    ->where('name', 'LIKE', '%John%')
    ->get();

// Better - use full-text search if available
Student::where('school_id', $schoolId)
    ->whereFullText('name', 'John')
    ->get();
```

### 3. Lazy Loading Risk
```php
// Risky - causes N+1 queries
$students = Student::all();
foreach ($students as $student) {
    echo $student->school->name; // Query per student
}

// Safe - uses eager loading
$students = Student::with('school')->get();
foreach ($students as $student) {
    echo $student->school->name; // Single school table query
}
```

## Monitoring & Debugging

### Check Migration Status
```bash
php artisan migrate:status --path=database/migrations/tenant
```

### Verify School ID Columns
```bash
php artisan tinker

# Check columns in a table
Schema::getColumnListing('students')
// returns: ['id', 'school_id', 'name', 'email', ...]

# Check column details
Schema::getColumns('students')

# Check if indexed
Schema::getIndexes('students')
```

### Query School ID Distribution
```php
// In Laravel Tinker
php artisan tinker

// See data distribution
Student::groupBy('school_id')->selectRaw('school_id, COUNT(*) as count')->get();

// Check for orphaned records (school_id not in schools table)
Student::whereNotIn('school_id', School::pluck('id'))->count();
```

### Log Review
```bash
# Follow migration logs
tail -f storage/logs/laravel.log

# Search for school_id operations
grep -i "school_id" storage/logs/laravel.log
```

## Common Issues & Solutions

### Issue: "SQLSTATE[HY000]: General error: 1030"
**Cause**: Unique column name across multiple statements
**Solution**: This migration handles it with existence checks

### Issue: Index already exists
**Cause**: Running migration twice on same database
**Solution**: Migration checks for existing columns, won't duplicate indexes

### Issue: Foreign key constraint violations
**Cause**: If you add foreign keys without checking school_id
**Solution**: Always join on both school_id and entity_id:
```php
// Instead of:
$table->foreign('class_id')->references('id')->on('classes');

// Do:
$table->foreign(['school_id', 'class_id'])
    ->references(['school_id', 'id'])
    ->on('classes');
```

## Rollback Scenarios

### Full Rollback
```bash
php artisan migrate:rollback --path=database/migrations/tenant
```
Removes all school_id columns and indexes.

### Selective Rollback
If you only want to remove from specific tables:
```php
// Create a new migration to remove selectively
Schema::table('students', function (Blueprint $table) {
    if (Schema::hasColumn('students', 'school_id')) {
        $table->dropIndex(['school_id']);
        $table->dropColumn('school_id');
    }
});
```

## Next Steps After Migration

1. **Update Models**
   - Add school relationship
   - Add global scope for filtering
   - Update factory for tests

2. **Update Controllers**
   - Add school_id in create/update
   - Add authorization checks
   - Return school-filtered data

3. **Update Tests**
   - Include school_id in test data
   - Test cross-school isolation
   - Test authorization policies

4. **Add Documentation**
   - Update API documentation
   - Add school_id to OpenAPI schema
   - Document multi-tenancy behavior

5. **Monitor in Production**
   - Check query performance
   - Monitor slow queries
   - Review authorization logs

---

**Version**: 1.0
**Last Updated**: 2025-01-01
**Status**: Production Ready
