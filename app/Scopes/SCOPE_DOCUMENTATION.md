# SchoolScope Documentation

## Overview

`SchoolScope` is a Laravel Eloquent global scope that automatically filters all model queries by `school_id`. It provides automatic data isolation in a multi-school system, ensuring users can only access data belonging to their assigned school.

## Features

- **Automatic Filtering**: Transparently filters all queries by school_id
- **Admin Bypass**: Skips filtering for admin users (school_id === null)
- **Session Support**: Supports temporary school context switching via session
- **Safe Column Check**: Only applies to tables that have school_id column
- **Elegant Removal**: Can be easily removed with `withoutGlobalScope()`

## Architecture

### Data Isolation Hierarchy

```
BaseModel (parent class)
├── branch_id scope (if MultiBranch module active)
└── school_id scope (SchoolScope)

Individual Models can:
- Override scope behavior
- Remove scope for specific queries
- Extend scope functionality
```

### SchoolId Resolution Priority

The scope determines which school_id to use in this priority order:

```
1. Session 'school_id' (for temporary switching)
   └─ session('school_id')

2. Authenticated user's school_id
   └─ auth()->user()->school_id

3. null (admin users - view all schools)
   └─ Indicates super-admin with no school restriction
```

## Integration

### Method 1: Apply to BaseModel (Recommended)

Apply to all models at once by adding to `BaseModel::boot()`:

```php
<?php
namespace App\Models;

use App\Scopes\SchoolScope;
use Illuminate\Database\Eloquent\Model;

class BaseModel extends Model
{
    protected static function boot()
    {
        parent::boot();

        // Apply school scope (admin users with null school_id skip filtering)
        static::addGlobalScope(new SchoolScope());

        // Keep existing branch scope if MultiBranch module is active
        if (hasModule('MultiBranch')) {
            static::addGlobalScope('branch_id', function (Builder $builder) {
                // ... existing branch_id logic
            });
        }
    }
}
```

### Method 2: Apply to Specific Models

For selective application:

```php
<?php
namespace App\Models\StudentInfo;

use App\Models\BaseModel;
use App\Scopes\SchoolScope;

class Student extends BaseModel
{
    protected static function boot()
    {
        parent::boot();

        // Only this model gets school filtering
        static::addGlobalScope(new SchoolScope());
    }
}
```

## Usage Examples

### Standard Queries (With Scope)

```php
// Automatically filters by user's school_id
$students = Student::all(); // Only students in user's school

$student = Student::find(1); // Only finds if in user's school

$activeStudents = Student::where('status', 'active')->get();
// WHERE school_id = ? AND status = 'active'
```

### Bypass Scope (Without Scope)

```php
// Remove all global scopes
$allStudents = Student::withoutGlobalScopes()->get();

// Remove only school scope (keep other scopes)
$students = Student::withoutGlobalScope(SchoolScope::class)->get();

// Remove specific scopes
$students = Student::withoutGlobalScope(SchoolScope::class)
    ->withoutGlobalScope('branch_id')
    ->get();
```

### Admin Reports

```php
class ReportController extends Controller
{
    public function adminSummary()
    {
        // For admin users (school_id === null), scope is automatically skipped
        // This query will return data from all schools if user is admin
        return Student::where('status', 'active')->get();
    }

    public function schoolComparison()
    {
        // Explicitly remove scope to compare across schools
        return Student::select('school_id', \DB::raw('count(*) as total'))
            ->withoutGlobalScope(SchoolScope::class)
            ->groupBy('school_id')
            ->get();
    }
}
```

### School Switching (Session-Based)

```php
class SchoolSwitchController extends Controller
{
    public function switchSchool($schoolId)
    {
        // Admin user switching to view another school's data temporarily
        session(['school_id' => $schoolId]);

        // Now all queries will filter by this school_id
        $students = Student::all(); // Uses session school_id

        return redirect()->back();
    }

    public function resetSchool()
    {
        // Reset to user's default school
        session()->forget('school_id');

        // Now uses auth()->user()->school_id again
        $students = Student::all();
    }
}
```

## Query Examples with Generated SQL

### Example 1: Basic Student Query

```php
$students = Student::where('status', 'active')->get();

// Generated SQL (for user with school_id=5):
// SELECT * FROM students
// WHERE students.school_id = 5
// AND status = 'active'
```

### Example 2: With Relationships

```php
$students = Student::with('class', 'attendance')
    ->where('status', 'active')
    ->get();

// Scope applies to main query only:
// SELECT * FROM students
// WHERE students.school_id = 5
// AND status = 'active'

// Related queries not auto-scoped (must scope explicitly if needed):
// SELECT * FROM classes WHERE id IN (...)
// SELECT * FROM attendances WHERE student_id IN (...)
```

### Example 3: Bypass for Reporting

```php
$schoolStats = Student::select('school_id', DB::raw('COUNT(*) as total'))
    ->withoutGlobalScope(SchoolScope::class)
    ->groupBy('school_id')
    ->get();

// Generated SQL (no school_id filter):
// SELECT school_id, COUNT(*) as total FROM students
// GROUP BY school_id
```

## Configuration

### Database Requirements

Models using SchoolScope must have a `school_id` column:

```php
// Migration example
Schema::create('students', function (Blueprint $table) {
    $table->id();
    $table->string('name');
    $table->unsignedBigInteger('school_id');
    $table->foreign('school_id')->references('id')->on('schools');
    // ... other columns
});
```

### User Setup

The authenticated user should have a `school_id` attribute:

```php
// User model/migration
Schema::create('users', function (Blueprint $table) {
    $table->id();
    $table->string('name');
    $table->string('email')->unique();
    // null = admin/super-user (can see all schools)
    // > 0 = assigned to specific school
    $table->unsignedBigInteger('school_id')->nullable();
    $table->foreign('school_id')->references('id')->on('schools');
});
```

## Admin User Handling

### Super-Admin Users (No School Restriction)

Users with `school_id = NULL` in the database are treated as super-admins:

```php
// User setup
User::create([
    'name' => 'Super Admin',
    'email' => 'admin@school.test',
    'school_id' => null,  // null indicates super-admin
]);

// This user can see all data regardless of school_id
$students = Student::all(); // Returns students from ALL schools
```

### School-Specific Admin

Users with a specific `school_id` are restricted to that school:

```php
// School principal
User::create([
    'name' => 'Principal',
    'email' => 'principal@school.test',
    'school_id' => 1,  // Can only see School 1 data
]);

$students = Student::all(); // Only returns students with school_id = 1
```

### Temporary School Switching

Admin users can temporarily switch school context:

```php
// In controller
public function impersonate($schoolId)
{
    // Admin impersonating another school's context
    session(['school_id' => $schoolId]);

    // All queries now use this school_id temporarily
    $students = Student::all(); // Uses session school_id
}
```

## Best Practices

### 1. Always Scope Related Data

When eager loading relationships, ensure related models are also scoped:

```php
// BAD - relationships may not be filtered
$students = Student::with('class', 'attendance')->get();

// BETTER - explicitly scope relationships if needed
$students = Student::with(['class' => function($query) {
    // Classes table may not have school_id, but ensure consistency
}])->get();

// BEST - scope manually for clarity
$students = Student::where('school_id', auth()->user()->school_id)
    ->with('class', 'attendance')
    ->get();
```

### 2. Scoping in Controllers

Always be explicit about scope removal:

```php
class ReportController extends Controller
{
    // Clear intent: this report needs all schools
    public function systemWide()
    {
        return Student::withoutGlobalScope(SchoolScope::class)
            ->select('school_id', DB::raw('count(*) as total'))
            ->groupBy('school_id')
            ->get();
    }

    // Clear intent: this report uses user's school context
    public function schoolReport()
    {
        return Student::where('status', 'active')
            ->get(); // Uses scope automatically
    }
}
```

### 3. Scoping in Migrations & Seeders

Disable scopes when seeding or migrating data:

```php
class DatabaseSeeder extends Seeder
{
    public function run()
    {
        // Disable all scopes for data migration/seeding
        Model::unguarded(function () {
            $this->call([
                SchoolSeeder::class,
                StudentSeeder::class, // This might need to set school_id manually
            ]);
        });
    }
}
```

### 4. Testing with Scopes

Handle scopes in tests:

```php
class StudentTest extends TestCase
{
    use RefreshDatabase;

    public function test_student_list_filters_by_school()
    {
        $school1 = School::create(['name' => 'School 1']);
        $school2 = School::create(['name' => 'School 2']);

        $student1 = Student::create(['name' => 'Alice', 'school_id' => $school1->id]);
        $student2 = Student::create(['name' => 'Bob', 'school_id' => $school2->id]);

        // Login as school1 user
        $user = User::create([
            'name' => 'Principal',
            'school_id' => $school1->id,
        ]);
        $this->actingAs($user);

        // Should only see student1
        $students = Student::all();
        $this->assertCount(1, $students);
        $this->assertEquals('Alice', $students->first()->name);
    }

    public function test_admin_sees_all_students()
    {
        $school1 = School::create(['name' => 'School 1']);
        $school2 = School::create(['name' => 'School 2']);

        Student::create(['name' => 'Alice', 'school_id' => $school1->id]);
        Student::create(['name' => 'Bob', 'school_id' => $school2->id]);

        // Login as super-admin (school_id = null)
        $admin = User::create([
            'name' => 'Admin',
            'school_id' => null,
        ]);
        $this->actingAs($admin);

        // Should see all students
        $students = Student::all();
        $this->assertCount(2, $students);
    }
}
```

### 5. API Responses

Ensure API responses respect scope:

```php
class StudentResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'school_id' => $this->school_id,
            // Admin can see raw school_id, others see theirs
            'school_name' => $this->whenLoaded('school'),
        ];
    }
}
```

## Troubleshooting

### Issue: Scope Not Applied

**Symptoms**: Queries return data from other schools

**Solutions**:
1. Verify model extends BaseModel (if scope added there)
2. Check that table has `school_id` column
3. Verify user is authenticated and has `school_id` attribute
4. Check for `withoutGlobalScope()` calls

```php
// Debug: Check what scope is applied
$query = Student::query();
// The global scopes should include SchoolScope
dd($query->getModel()->getGlobalScopes());
```

### Issue: Admin Can't See All Data

**Symptoms**: Admin users still see filtered data

**Solutions**:
1. Verify admin user has `school_id = null` (not a number)
2. Check for hardcoded school_id filters elsewhere
3. Look for middleware that sets session school_id

```php
// Debug: Check user's school_id
dump(auth()->user()->school_id); // Should be null for super-admin
dump(session('school_id')); // Should be null if not switched
```

### Issue: Performance Degradation

**Symptoms**: Queries are slow after adding scope

**Solutions**:
1. Add index on school_id column
2. Use composite indexes for common query patterns
3. Consider caching school-specific data

```php
// Migration: Add index
Schema::table('students', function (Blueprint $table) {
    $table->index('school_id');
    $table->index(['school_id', 'status']); // Composite
});
```

## Migration Path

### Step 1: Add SchoolScope to BaseModel

```php
// app/Models/BaseModel.php
protected static function boot()
{
    parent::boot();
    static::addGlobalScope(new SchoolScope());
    // ... existing scopes
}
```

### Step 2: Verify Database Schema

Ensure all models that need filtering have `school_id` column.

### Step 3: Test User Scenarios

- [ ] Regular users see only their school data
- [ ] Admin users see all school data
- [ ] Session switching works correctly
- [ ] API responses are correct
- [ ] Reports include all necessary data

### Step 4: Update Tests

Update existing tests to account for scope filtering.

### Step 5: Monitor in Production

- Check query logs for unexpected filters
- Monitor performance impact
- Track admin user activities (school switching)

## Performance Considerations

### Index Strategy

```sql
-- Essential indexes for SchoolScope
CREATE INDEX idx_school_id ON students(school_id);
CREATE INDEX idx_school_status ON students(school_id, status);
CREATE INDEX idx_school_created ON students(school_id, created_at);
```

### Query Analysis

```php
// Check generated queries
DB::enableQueryLog();
$students = Student::all();
dd(DB::getQueryLog());

// Example output:
// SELECT * FROM students WHERE school_id = 5
```

### Caching Strategy

```php
// Cache school-specific data
$students = Cache::remember(
    "school_{$schoolId}_students",
    now()->addHours(1),
    fn() => Student::get()
);
```

## Related Scopes

### BranchScope

If using MultiBranch module, SchoolScope works alongside:

```php
// Query with both scopes
$students = Student::all();
// WHERE school_id = 5 AND branch_id = 2
```

### RemoveAllScopes

Sometimes you need to bypass everything:

```php
// Remove all scopes at once
$data = Student::withoutGlobalScopes()->get();
```

## References

- Laravel Eloquent Global Scopes: https://laravel.com/docs/eloquent#global-scopes
- BaseModel Implementation: `/app/Models/BaseModel.php`
- Related Middleware: Check auth middleware for school_id injection
- Database Schema: Check migrations for school_id column definitions
