# SchoolScope - Automatic School Data Isolation

## What is SchoolScope?

SchoolScope is a Laravel Eloquent global scope that automatically filters all model queries by `school_id`. It provides transparent, application-wide data isolation in a multi-school system, ensuring users can only access data from their assigned school.

**Key Benefit**: Write normal queries and get automatic data isolation - no manual `where('school_id', ...)` needed.

```php
// Before: Manual filtering everywhere
$students = Student::where('school_id', auth()->user()->school_id)->get();

// After: Automatic filtering with SchoolScope
$students = Student::all(); // Automatically filters by school_id
```

## Quick Start

### 1. File Structure

```
app/Scopes/
├── SchoolScope.php                 (The scope implementation)
├── README.md                       (This file)
├── SCOPE_DOCUMENTATION.md          (Detailed documentation)
└── INTEGRATION_GUIDE.md            (Step-by-step integration)

tests/
├── Unit/Scopes/
│   └── SchoolScopeTest.php         (Unit tests)
└── Feature/
    └── SchoolScopeFeatureTest.php  (Feature tests)
```

### 2. One-Line Integration

Add this to `BaseModel::boot()`:

```php
static::addGlobalScope(new SchoolScope());
```

### 3. How It Works

```
User Query → SchoolScope applies filter → Filtered Results
             (if table has school_id column)
             (if user is not admin)
```

## Core Features

### ✅ Automatic Filtering

```php
$students = Student::all();
// SQL: SELECT * FROM students WHERE school_id = 5
```

### ✅ Admin Bypass

```php
// Super-admin with school_id = null
$admin = User::create(['school_id' => null]);

// Now queries show all schools (no WHERE school_id filter)
$students = Student::all(); // All schools
```

### ✅ Session School Switching

```php
// Admin temporarily viewing another school
session(['school_id' => 3]);

// Queries now filter by school_id = 3
$students = Student::all(); // Only school 3
```

### ✅ Explicit Scope Removal

```php
// For reports that need all schools
$allStudents = Student::withoutGlobalScope(SchoolScope::class)->get();
```

### ✅ Safe Column Checking

```php
// Only filters if table has school_id column
// Tables without school_id are unaffected
```

## User Types

### Regular User (school_id = 1)
- Sees only their school's data
- Cannot switch schools (enforced at controller/middleware level)
- Example: School principal, teacher, staff

### Super-Admin (school_id = null)
- Sees all school's data by default
- Can switch to single school view with session
- Example: System administrator, super-user

### School-Specific Admin (school_id = 2)
- Sees only their school's data
- Has admin privileges within their school
- Example: School principal, head administrator

## Implementation Steps

### Step 1: Verify SchoolScope exists
```bash
ls -la app/Scopes/SchoolScope.php
```

### Step 2: Update BaseModel
```php
// app/Models/BaseModel.php
use App\Scopes\SchoolScope;

class BaseModel extends Model
{
    protected static function boot()
    {
        parent::boot();
        static::addGlobalScope(new SchoolScope());
        // ... existing code
    }
}
```

### Step 3: Verify database schema
```php
// All models using scope must have school_id column
Schema::table('students', function (Blueprint $table) {
    $table->unsignedBigInteger('school_id')->nullable();
    $table->foreign('school_id')->references('id')->on('schools');
});
```

### Step 4: Test it works
```php
// In controller or test
$user = auth()->user();
echo $user->school_id; // Should not be null for regular users

// Models extending BaseModel should now be filtered
$students = Student::all(); // Only returns their school's students
```

## Common Usage Patterns

### Pattern 1: Normal CRUD Operations
```php
// All automatically filtered by school_id
$students = Student::all();
$active = Student::where('status', 'active')->get();
$top = Student::orderBy('grade')->limit(10)->get();
```

### Pattern 2: Cross-School Reports
```php
// Remove scope for system-wide views
$stats = Student::withoutGlobalScope(SchoolScope::class)
    ->selectRaw('school_id, COUNT(*) as total')
    ->groupBy('school_id')
    ->get();
```

### Pattern 3: Admin School Switching
```php
// In controller
public function switchSchool($schoolId)
{
    session(['school_id' => $schoolId]);
    return redirect()->back();
}

public function resetSchool()
{
    session()->forget('school_id');
    return redirect()->back();
}
```

### Pattern 4: Bulk Operations
```php
// Create (automatically set school_id if needed)
$student = Student::create([
    'name' => 'John',
    'school_id' => auth()->user()->school_id, // Set explicitly
]);

// Update
$student->update(['status' => 'inactive']);

// Delete (only their school's students)
$student->delete();
```

## Testing Examples

### Unit Test
```php
public function test_student_scope_filters_by_school()
{
    $user = User::factory()->create(['school_id' => 1]);
    $this->actingAs($user);

    // Add students to different schools
    Student::create(['name' => 'Alice', 'school_id' => 1]);
    Student::create(['name' => 'Bob', 'school_id' => 2]);

    // User1 should only see Alice
    $students = Student::all();
    $this->assertCount(1, $students);
}
```

### Feature Test
```php
public function test_admin_can_see_all_schools()
{
    $admin = User::factory()->create(['school_id' => null]);
    $this->actingAs($admin);

    Student::create(['name' => 'Alice', 'school_id' => 1]);
    Student::create(['name' => 'Bob', 'school_id' => 2]);

    // Admin should see all
    $students = Student::all();
    $this->assertCount(2, $students);
}
```

## Troubleshooting

### Problem: Queries not filtered
**Check**:
1. Is BaseModel using `addGlobalScope(new SchoolScope())`?
2. Does the table have `school_id` column?
3. Is user authenticated with `school_id` set?

```php
// Debug
dd(Student::query()->toSql()); // Should include school_id WHERE
```

### Problem: Admin still sees filtered data
**Check**:
1. Admin user has `school_id = null` (not 0)
2. No hardcoded school_id filters elsewhere
3. No middleware overriding it

```php
// Debug
dump(auth()->user()->school_id); // Must be null for admin
```

### Problem: Performance issues
**Solution**: Add indexes
```php
Schema::table('students', function (Blueprint $table) {
    $table->index('school_id');
    $table->index(['school_id', 'status']);
});
```

## File Reference

| File | Purpose |
|------|---------|
| `SchoolScope.php` | Core scope implementation |
| `README.md` | Quick reference (this file) |
| `SCOPE_DOCUMENTATION.md` | Comprehensive documentation |
| `INTEGRATION_GUIDE.md` | Step-by-step integration guide |
| `tests/Unit/Scopes/SchoolScopeTest.php` | Unit tests |
| `tests/Feature/SchoolScopeFeatureTest.php` | Feature tests |

## Related Documentation

- **Laravel Scopes**: https://laravel.com/docs/eloquent#global-scopes
- **BaseModel**: `/app/Models/BaseModel.php`
- **BranchScope**: Works alongside SchoolScope (if MultiBranch module enabled)
- **User Model**: `/app/Models/User.php` (must have school_id field)

## Best Practices

### ✅ DO

```php
// Do: Let the scope handle filtering
$students = Student::all();

// Do: Be explicit when removing scope
$all = Student::withoutGlobalScope(SchoolScope::class)->get();

// Do: Admin with school_id = null
User::create(['school_id' => null]);

// Do: Test with scope in mind
$this->actingAs($user);
```

### ❌ DON'T

```php
// Don't: Manually filter everywhere
$students = Student::where('school_id', ...)->get();

// Don't: Use 0 or empty string for admin
User::create(['school_id' => 0]);

// Don't: Forget scope exists
// (Your data isolation may not work as expected)

// Don't: Assume relationships are scoped
$students = Student::with('class')->get(); // Class might not be filtered
```

## Performance Tips

### 1. Add Indexes
```php
$table->index('school_id');
$table->index(['school_id', 'status']);
```

### 2. Cache School-Specific Data
```php
$students = Cache::remember(
    "school_{$schoolId}_students",
    now()->addHours(1),
    fn() => Student::get()
);
```

### 3. Monitor Queries
```php
// Development
DB::enableQueryLog();
// ... run queries
dd(DB::getQueryLog());
```

## Next Steps

1. **Read**: Full documentation in `SCOPE_DOCUMENTATION.md`
2. **Integrate**: Follow `INTEGRATION_GUIDE.md`
3. **Test**: Run `SchoolScopeTest.php` and `SchoolScopeFeatureTest.php`
4. **Deploy**: Implement in your application
5. **Monitor**: Check query logs for proper filtering

## Support

For detailed examples and comprehensive documentation:
- See `SCOPE_DOCUMENTATION.md` for complete reference
- See `INTEGRATION_GUIDE.md` for step-by-step integration
- Check test files for real-world usage examples
- Review `BaseModel.php` for integration point

## Summary

SchoolScope provides:
- ✅ Automatic, transparent data isolation by school
- ✅ Simple admin bypass (school_id = null)
- ✅ Session-based school switching for admins
- ✅ Safe (only affects tables with school_id column)
- ✅ Easy to remove when needed
- ✅ Production-ready implementation

**Result**: Multi-school system with guaranteed data isolation requiring minimal code changes.
