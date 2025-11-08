# SchoolScope Integration Guide

## Quick Start

### 1. Update BaseModel (5 minutes)

Add SchoolScope to your BaseModel to apply automatic school_id filtering to all models:

```php
<?php
namespace App\Models;

use App\Scopes\SchoolScope;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class BaseModel extends Model
{
    protected static function boot()
    {
        parent::boot();

        // Add SchoolScope for automatic school_id filtering
        static::addGlobalScope(new SchoolScope());

        // Keep existing MultiBranch scope if module is active
        if (hasModule('MultiBranch')) {
            static::addGlobalScope('branch_id', function (Builder $builder) {
                $table = $builder->getQuery()->from;
                $branchId = auth()->user()->branch_id ?? null;

                if ($branchId && Schema::hasColumn($table, 'branch_id')) {
                    $builder->where("{$table}.branch_id", $branchId);
                }
            });

            static::creating(function ($model) {
                $branchId = auth()->user()->branch_id ?? null;

                if (
                    $branchId &&
                    Schema::hasColumn($model->getTable(), 'branch_id')
                ) {
                    $model->branch_id = $branchId;
                }
            });
        }
    }
}
```

### 2. Verify Database Schema

Ensure your models have the `school_id` column:

```php
// Check database migration
Schema::table('students', function (Blueprint $table) {
    // Should exist:
    $table->unsignedBigInteger('school_id')->nullable();
    $table->foreign('school_id')->references('id')->on('schools');
});
```

### 3. Verify User Setup

Users should have `school_id` attribute:

```php
// User model - school_id field
Schema::table('users', function (Blueprint $table) {
    // null = super-admin (can see all schools)
    // > 0 = assigned to specific school
    $table->unsignedBigInteger('school_id')->nullable();
    $table->foreign('school_id')->references('id')->on('schools');
});

// User authentication includes school_id
$user = auth()->user();
$schoolId = $user->school_id; // null for admin, id for regular users
```

## Implementation Checklist

- [ ] SchoolScope class created at `/app/Scopes/SchoolScope.php`
- [ ] BaseModel updated with `addGlobalScope(new SchoolScope())`
- [ ] All models that need filtering inherit from BaseModel
- [ ] Database migrations include `school_id` column
- [ ] User model has `school_id` attribute
- [ ] Admin users have `school_id = null`
- [ ] Regular users have `school_id = assigned_school_id`
- [ ] Tests updated to account for scope filtering
- [ ] API endpoints verified for correct data filtering
- [ ] Reports updated to use `withoutGlobalScope()` if needed

## File Locations

```
app/
├── Scopes/
│   ├── SchoolScope.php (NEW)
│   └── SCOPE_DOCUMENTATION.md (NEW)
├── Models/
│   └── BaseModel.php (UPDATED)
└── ...
```

## Core Concepts

### What is SchoolScope?

A Laravel Eloquent Global Scope that automatically adds a `WHERE school_id = ?` clause to all queries for models that use it.

```php
// Without SchoolScope
$students = Student::all();
// SELECT * FROM students

// With SchoolScope
$students = Student::all();
// SELECT * FROM students WHERE school_id = 5
```

### How Does It Work?

1. **Triggered on Query**: Every query on a model with the scope triggers the scope's `apply()` method
2. **Gets School ID**: Retrieves user's school_id from auth or session
3. **Checks Admin**: If school_id is null (admin), skips filtering
4. **Applies Filter**: Adds WHERE clause only if table has school_id column
5. **Returns Result**: Query executes with or without the filter

### Priority Order for School ID

```
session('school_id')
    ↓ (if not set)
auth()->user()->school_id
    ↓ (if null - admin user)
null (skip filtering)
```

## Usage Patterns

### Pattern 1: Standard Query (Automatically Filtered)

```php
// In your controller or service
$students = Student::all();

// For user with school_id = 5:
// Generated SQL: SELECT * FROM students WHERE school_id = 5

// For admin with school_id = null:
// Generated SQL: SELECT * FROM students (no WHERE school_id filter)
```

### Pattern 2: Bypass Scope (When Needed)

```php
// Admin report showing all schools
$allStudents = Student::withoutGlobalScope(SchoolScope::class)->get();

// Remove all scopes at once
$data = Student::withoutGlobalScopes()->get();
```

### Pattern 3: With Additional Filters

```php
// School context + additional filters
$activeStudents = Student::where('status', 'active')
    ->whereDate('created_at', '>', now()->subDays(30))
    ->get();

// Generated SQL:
// SELECT * FROM students
// WHERE school_id = 5
// AND status = 'active'
// AND created_at > DATE
```

### Pattern 4: School Switching (Session)

```php
// Admin temporarily switching to another school's data
public function switchSchool($schoolId)
{
    session(['school_id' => $schoolId]);

    // Now queries use this school_id
    $data = Student::all();
}

public function resetSchool()
{
    session()->forget('school_id');
    // Queries use auth()->user()->school_id again
}
```

## Testing Integration

### Test Case: School Isolation

```php
class StudentScopeTest extends TestCase
{
    use RefreshDatabase;

    public function test_student_scope_filters_by_school()
    {
        // Setup
        $school1 = School::create(['name' => 'School 1']);
        $school2 = School::create(['name' => 'School 2']);

        $user1 = User::create([
            'name' => 'Principal 1',
            'email' => 'p1@test.local',
            'school_id' => $school1->id,
        ]);

        $student1 = Student::create([
            'name' => 'Alice',
            'school_id' => $school1->id,
        ]);
        $student2 = Student::create([
            'name' => 'Bob',
            'school_id' => $school2->id,
        ]);

        // Act: Login as user1
        $this->actingAs($user1);

        // Assert: Should only see students from their school
        $students = Student::all();
        $this->assertCount(1, $students);
        $this->assertEquals('Alice', $students->first()->name);
    }

    public function test_admin_sees_all_schools()
    {
        // Setup
        $school1 = School::create(['name' => 'School 1']);
        $school2 = School::create(['name' => 'School 2']);

        $admin = User::create([
            'name' => 'Super Admin',
            'email' => 'admin@test.local',
            'school_id' => null, // Admin indicator
        ]);

        Student::create(['name' => 'Alice', 'school_id' => $school1->id]);
        Student::create(['name' => 'Bob', 'school_id' => $school2->id]);

        // Act: Login as admin
        $this->actingAs($admin);

        // Assert: Should see all students
        $students = Student::all();
        $this->assertCount(2, $students);
    }

    public function test_scope_can_be_removed()
    {
        // Setup
        $school1 = School::create(['name' => 'School 1']);
        $school2 = School::create(['name' => 'School 2']);

        $user1 = User::create([
            'name' => 'Principal 1',
            'email' => 'p1@test.local',
            'school_id' => $school1->id,
        ]);

        Student::create(['name' => 'Alice', 'school_id' => $school1->id]);
        Student::create(['name' => 'Bob', 'school_id' => $school2->id]);

        // Act: Login and remove scope
        $this->actingAs($user1);
        $students = Student::withoutGlobalScope(SchoolScope::class)->all();

        // Assert: Should see all students (scope removed)
        $this->assertCount(2, $students);
    }
}
```

## Common Issues & Solutions

### Issue 1: Scope Not Filtering

**Symptom**: Users see data from other schools

**Checklist**:
- [ ] BaseModel has `addGlobalScope(new SchoolScope())`
- [ ] Model extends BaseModel
- [ ] Database table has `school_id` column
- [ ] User is authenticated with school_id set
- [ ] No `withoutGlobalScope()` calls removing it

**Debug**:
```php
// Check if scope is applied
$query = Student::query();
$scopes = $query->getModel()->getGlobalScopes();
dd($scopes); // Should show SchoolScope
```

### Issue 2: Admin Can't See All Data

**Symptom**: Admin users still see filtered data

**Checklist**:
- [ ] Admin users have `school_id = null` (not 0, not empty string)
- [ ] No other hardcoded school_id filters in queries
- [ ] No middleware overriding school_id
- [ ] session('school_id') is not set for admin

**Debug**:
```php
// Check admin user's school_id
dd(auth()->user()->school_id); // Must be null, not 0

// Check if scope logic is correct
dump(session('school_id')); // Should be null if not switching
```

### Issue 3: Performance Degradation

**Symptom**: Queries are slow after adding scope

**Solution**: Add indexes to school_id column

```php
// Migration
Schema::table('students', function (Blueprint $table) {
    $table->index('school_id'); // Add index
});

// Check query with EXPLAIN
\DB::statement("EXPLAIN SELECT * FROM students WHERE school_id = 5");
```

### Issue 4: Relationships Not Filtered

**Symptom**: Eager loaded relationships include data from other schools

**Note**: Global scopes only apply to the main query, not relationships

**Solution**: Scope relationships explicitly if needed

```php
// This scopes Student but not Class
$students = Student::with('class')->get();

// Scope Class explicitly
$students = Student::with(['class' => function($query) {
    $query->where('school_id', auth()->user()->school_id);
}])->get();

// Or: Class inherits BaseModel and gets scoped automatically
```

## Migration Checklist

### Phase 1: Preparation
- [ ] Create SchoolScope.php
- [ ] Review database schema for school_id columns
- [ ] Review User model for school_id attribute
- [ ] Identify models that need filtering

### Phase 2: Implementation
- [ ] Add scope to BaseModel
- [ ] Test in local environment
- [ ] Update test suite
- [ ] Update API documentation

### Phase 3: Verification
- [ ] Test regular user access
- [ ] Test admin access
- [ ] Test session school switching
- [ ] Test report generation
- [ ] Test with multiple schools

### Phase 4: Monitoring
- [ ] Monitor query logs
- [ ] Check performance metrics
- [ ] Track admin activities
- [ ] Verify data isolation

## Best Practices

### ✅ DO

```php
// Do: Let scope handle filtering automatically
$students = Student::all();

// Do: Be explicit when removing scope
$allStudents = Student::withoutGlobalScope(SchoolScope::class)->get();

// Do: Set admin users with school_id = null
User::create(['name' => 'Admin', 'school_id' => null]);

// Do: Scope migrations/seeders if needed
Model::withoutGlobalScope(SchoolScope::class)->create([...]);
```

### ❌ DON'T

```php
// Don't: Manually filter by school_id everywhere
$students = Student::where('school_id', auth()->user()->school_id)->get();

// Don't: Use 0 or empty string for admin school_id
User::create(['name' => 'Admin', 'school_id' => 0]); // Wrong!

// Don't: Remove scope without clear reason
$students = Student::withoutGlobalScopes()->get(); // Too broad

// Don't: Assume relationships are scoped
$students = Student::with('class')->get(); // Class may not be filtered
```

## Performance Optimization

### Index Strategy

```php
// Essential indexes for SchoolScope
Schema::table('students', function (Blueprint $table) {
    // Single column index
    $table->index('school_id');

    // Composite indexes for common queries
    $table->index(['school_id', 'status']);
    $table->index(['school_id', 'created_at']);
    $table->index(['school_id', 'class_id']);
});
```

### Caching Strategy

```php
// Cache school-specific data
$students = Cache::remember(
    "school_{$schoolId}_students",
    now()->addHours(1),
    fn() => Student::get()
);

// Clear cache when data changes
public function updateStudent(Student $student, array $data)
{
    $student->update($data);
    Cache::forget("school_{$student->school_id}_students");
}
```

## Reference Documentation

- **SchoolScope Code**: `/app/Scopes/SchoolScope.php`
- **Full Documentation**: `/app/Scopes/SCOPE_DOCUMENTATION.md`
- **BaseModel**: `/app/Models/BaseModel.php`
- **Laravel Scopes Docs**: https://laravel.com/docs/eloquent#global-scopes

## Support

For issues or questions:
1. Check troubleshooting section above
2. Review SCOPE_DOCUMENTATION.md for detailed examples
3. Check test cases in SchoolScope test file
4. Review BaseModel implementation
