# SchoolContext Middleware Documentation

## Overview

The `SchoolContext` middleware establishes and manages multi-tenant school context for authenticated users. It handles both regular school users and admin users with different access patterns.

**Location**: `/app/Http/Middleware/SchoolContext.php`

---

## Features

### 1. School Context Detection
- Automatically determines school ID from authenticated user
- Handles both school users (via `branch_id`) and admin users (via session)
- Provides fallback for missing context

### 2. View Sharing
- Shares school context variables with all Blade templates
- Available variables: `school_id`, `currentSchool`, `isAdmin`, `currentUser`

### 3. Request Context
- Stores school context in request attributes
- Accessible in controllers via request methods

### 4. Admin Context Switching
- Allows admins to temporarily switch school context
- Changes stored in session (not persisted to database)
- Can be cleared to return to default context

### 5. Session Management
- Stores `school_id` in session for cross-request access
- Supports admin temporary context switching

---

## Registration

### Step 1: Register in Kernel

Add the middleware to your HTTP kernel at `/app/Http/Kernel.php`:

```php
protected $middleware = [
    // ... other middleware
    \App\Http\Middleware\SchoolContext::class,
];
```

Or add to a specific route group:

```php
Route::middleware(['auth', 'school-context'])
    ->group(function () {
        // Protected routes
    });
```

### Step 2: Add Alias (Optional)

Add alias in `$routeMiddleware` array:

```php
protected $routeMiddleware = [
    // ... other middleware
    'school-context' => \App\Http\Middleware\SchoolContext::class,
];
```

---

## Usage

### In Controllers

#### Access via Request Attributes
```php
class StudentController extends Controller
{
    public function index(Request $request)
    {
        $schoolId = $request->attributes->get('school_id');
        $currentSchool = $request->attributes->get('current_school');
        $isAdmin = $request->attributes->get('is_admin');

        $students = Student::where('branch_id', $schoolId)->get();

        return view('students.index', [
            'students' => $students,
            'school' => $currentSchool,
        ]);
    }
}
```

#### Using Helper Method
```php
class StudentController extends Controller
{
    public function show(Request $request, Student $student)
    {
        // Verify user belongs to student's school
        if (!SchoolContext::userBelongsToSchool($request, $student->branch_id)) {
            abort(403, 'Unauthorized school access');
        }

        return view('students.show', ['student' => $student]);
    }
}
```

#### Static Access (Non-Request Contexts)
```php
class StudentRepository
{
    public function getActive()
    {
        $schoolId = SchoolContext::getSessionSchoolId();

        return Student::where('branch_id', $schoolId)
            ->where('status', 'active')
            ->get();
    }
}
```

### In Blade Templates

```blade
<!-- Access school context in views -->
<div class="school-header">
    <h1>{{ $currentSchool->name ?? 'School Management' }}</h1>
    <p>School ID: {{ $school_id }}</p>
</div>

<!-- Conditional rendering based on role -->
@if($isAdmin)
    <div class="admin-panel">
        <a href="{{ route('admin.dashboard') }}">Admin Dashboard</a>
    </div>
@else
    <div class="user-panel">
        <p>User: {{ $currentUser->name }}</p>
    </div>
@endif

<!-- Current school information -->
@if($currentSchool)
    <p>Current School: {{ $currentSchool->name }}</p>
    <p>Status: {{ $currentSchool->status }}</p>
@endif
```

### In Jobs/Event Listeners

```php
class ProcessStudentImport implements ShouldQueue
{
    public function handle()
    {
        // Get school context from session
        $schoolId = SchoolContext::getSessionSchoolId();

        if (!$schoolId) {
            $this->fail(new Exception('School context not available'));
        }

        Student::where('branch_id', $schoolId)
            ->update(['status' => 'imported']);
    }
}
```

---

## Admin Context Switching

### Switching School Context

Admins can temporarily switch their active school context:

```php
class AdminDashboardController extends Controller
{
    public function switchSchool(Request $request, int $schoolId)
    {
        // Verify admin has access to this school
        $school = DB::table('branches')->find($schoolId);

        if (!$school) {
            return back()->with('error', 'School not found');
        }

        // Set temporary context
        SchoolContext::setAdminSchoolContext($schoolId);

        return back()->with('success', "Switched to {$school->name}");
    }

    public function resetContext(Request $request)
    {
        // Clear temporary context
        SchoolContext::clearAdminSchoolContext();

        return back()->with('success', 'Context reset to default');
    }
}
```

### Accessing Multiple Schools

```php
class AdminReportController extends Controller
{
    public function compareSchools(Request $request)
    {
        // Admins can query multiple schools
        $schools = DB::table('branches')->get();

        $statistics = collect();

        foreach ($schools as $school) {
            SchoolContext::setAdminSchoolContext($school->id);

            $statistics[$school->id] = [
                'school' => $school,
                'students' => Student::where('branch_id', $school->id)->count(),
                'staff' => Staff::where('branch_id', $school->id)->count(),
            ];
        }

        SchoolContext::clearAdminSchoolContext();

        return view('admin.comparison', ['statistics' => $statistics]);
    }
}
```

---

## Authorization Patterns

### Middleware-Based Protection

```php
// routes/web.php

Route::middleware(['auth', 'verified'])
    ->group(function () {
        Route::resource('students', StudentController::class);

        // Admin-only routes with context switching
        Route::middleware('admin')
            ->prefix('admin')
            ->group(function () {
                Route::get('schools/{school}/switch', [AdminController::class, 'switchSchool']);
                Route::get('reports/comparison', [AdminController::class, 'compareSchools']);
            });
    });
```

### Controller-Based Protection

```php
class StudentController extends Controller
{
    public function show(Request $request, Student $student)
    {
        // Method 1: Using helper
        if (!SchoolContext::userBelongsToSchool($request, $student->branch_id)) {
            abort(403);
        }

        // Method 2: Compare directly
        $userSchoolId = $request->attributes->get('school_id');
        if ($userSchoolId !== $student->branch_id) {
            abort(403);
        }

        return view('students.show', ['student' => $student]);
    }

    public function destroy(Request $request, Student $student)
    {
        // Verify school access
        if (!SchoolContext::userBelongsToSchool($request, $student->branch_id)) {
            abort(403, 'Cannot delete student from different school');
        }

        $student->delete();

        return back()->with('success', 'Student deleted');
    }
}
```

### Policy-Based Authorization

```php
class StudentPolicy
{
    public function view(User $user, Student $student): bool
    {
        // Super admins can view any student
        if ($user->role_id == RoleEnum::SUPERADMIN) {
            return true;
        }

        // Admins from the school can view
        if ($user->role_id == RoleEnum::ADMIN && $user->branch_id == $student->branch_id) {
            return true;
        }

        // Teachers can view students in their classes
        if ($user->role_id == RoleEnum::TEACHER) {
            return $this->isTeacherOfStudent($user, $student);
        }

        return false;
    }

    private function isTeacherOfStudent(User $teacher, Student $student): bool
    {
        return DB::table('subject_assign_children')
            ->where('teacher_id', $teacher->id)
            ->where('student_id', $student->id)
            ->where('branch_id', $student->branch_id)
            ->exists();
    }
}
```

---

## Scope Patterns

### Query Scope

Create a scope to automatically filter by school context:

```php
class BaseModel extends Model
{
    public function scopeCurrentSchool($query)
    {
        $schoolId = SchoolContext::getSessionSchoolId();

        if ($schoolId) {
            return $query->where('branch_id', $schoolId);
        }

        return $query;
    }
}
```

Usage:
```php
// Automatically filtered to current school
$students = Student::currentSchool()->get();

$teachers = Staff::currentSchool()
    ->where('role_id', RoleEnum::TEACHER)
    ->get();
```

### Service Layer Pattern

```php
class StudentService
{
    protected int|null $schoolId;

    public function __construct()
    {
        $this->schoolId = SchoolContext::getSessionSchoolId();
    }

    public function getActive()
    {
        if (!$this->schoolId) {
            throw new Exception('School context not available');
        }

        return Student::where('branch_id', $this->schoolId)
            ->where('status', 'active')
            ->get();
    }

    public function enroll(array $data): Student
    {
        $data['branch_id'] = $this->schoolId;

        return Student::create($data);
    }
}
```

---

## Database Considerations

### Schema Requirements

Ensure your tables have the `branch_id` column:

```php
Schema::create('students', function (Blueprint $table) {
    $table->id();
    $table->unsignedBigInteger('branch_id')->default(1);
    $table->string('name');
    // ... other fields
    $table->foreign('branch_id')->references('id')->on('branches')->onDelete('cascade');
});
```

### Branches Table

Ensure a `branches` table exists:

```php
Schema::create('branches', function (Blueprint $table) {
    $table->id();
    $table->string('name');
    $table->string('code')->unique();
    $table->string('location')->nullable();
    $table->string('status')->default('active');
    $table->timestamps();
});
```

---

## Error Handling

### Missing School Context

```php
class StudentController extends Controller
{
    public function index(Request $request)
    {
        $schoolId = $request->attributes->get('school_id');

        if (!$schoolId) {
            // Log incident
            Log::warning('No school context available', [
                'user_id' => Auth::id(),
                'route' => $request->path(),
            ]);

            return back()->with('error', 'School context not available');
        }

        $students = Student::where('branch_id', $schoolId)->get();

        return view('students.index', compact('students'));
    }
}
```

### Exception Handling

```php
use App\Http\Middleware\SchoolContext;

class ErrorHandler
{
    public function handleAuthorizationError(\Exception $e)
    {
        $context = SchoolContext::getSessionSchoolId();

        Log::error('Authorization error', [
            'school_id' => $context,
            'user_id' => Auth::id(),
            'error' => $e->getMessage(),
        ]);

        return response()->view('errors.403', [], 403);
    }
}
```

---

## Testing

### Testing with School Context

```php
class StudentControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_only_view_their_school_students()
    {
        $school1 = Branch::factory()->create();
        $school2 = Branch::factory()->create();

        $user = User::factory()->create(['branch_id' => $school1->id]);
        $student1 = Student::factory()->create(['branch_id' => $school1->id]);
        $student2 = Student::factory()->create(['branch_id' => $school2->id]);

        $this->actingAs($user)
            ->get(route('students.show', $student1))
            ->assertOk();

        $this->actingAs($user)
            ->get(route('students.show', $student2))
            ->assertForbidden();
    }

    public function test_admin_can_view_any_school_students()
    {
        $admin = User::factory()
            ->create(['role_id' => RoleEnum::ADMIN]);

        $student = Student::factory()->create();

        $this->actingAs($admin)
            ->get(route('students.show', $student))
            ->assertOk();
    }

    public function test_admin_can_switch_school_context()
    {
        $admin = User::factory()->create(['role_id' => RoleEnum::ADMIN]);
        $school = Branch::factory()->create();

        $this->actingAs($admin)
            ->get(route('admin.switch-school', $school))
            ->assertRedirect();

        $this->assertEquals($school->id, session('admin_school_context'));
    }
}
```

---

## Common Patterns

### Multi-School Dashboard

```php
class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $isAdmin = $request->attributes->get('is_admin');
        $schoolId = $request->attributes->get('school_id');

        $statistics = [
            'students' => Student::where('branch_id', $schoolId)->count(),
            'staff' => Staff::where('branch_id', $schoolId)->count(),
            'classes' => ClassRoom::where('branch_id', $schoolId)->count(),
        ];

        return view('dashboard', [
            'statistics' => $statistics,
            'isAdmin' => $isAdmin,
            'schoolOptions' => $isAdmin ? Branch::all() : collect(),
        ]);
    }
}
```

### Cross-School Reporting (Admin Only)

```php
class ReportService
{
    public function generateComparison($schoolIds)
    {
        $report = [];

        foreach ($schoolIds as $schoolId) {
            SchoolContext::setAdminSchoolContext($schoolId);

            $report[$schoolId] = [
                'students' => Student::currentSchool()->count(),
                'staff' => Staff::currentSchool()->count(),
                'fees_collected' => FeesCollect::currentSchool()->sum('amount'),
            ];
        }

        SchoolContext::clearAdminSchoolContext();

        return $report;
    }
}
```

---

## Troubleshooting

### School Context is Null

1. **Verify User Authentication**
   ```php
   if (!Auth::check()) {
       dd('User not authenticated');
   }
   ```

2. **Check branch_id in Database**
   ```php
   $user = Auth::user();
   dd($user->branch_id); // Should not be null
   ```

3. **Verify Middleware Registration**
   - Check kernel.php for middleware registration
   - Ensure middleware is in correct route group

### Admin Context Not Switching

```php
// Verify user is admin
if (Auth::user()->role_id != RoleEnum::ADMIN) {
    dd('User is not an admin');
}

// Set context
SchoolContext::setAdminSchoolContext($schoolId);

// Verify it was set
dd(session('admin_school_context'));
```

### View Variables Not Available

1. **Check middleware is registered globally or in route group**
2. **Verify user is authenticated**
3. **Check View::share() is being called**

---

## Security Considerations

1. **Always verify school ownership** before allowing data access
2. **Use policies or middleware** to enforce school boundaries
3. **Log all school context switches** for audit trails
4. **Clear admin context** after operations to prevent leaks
5. **Validate school_id** before using in queries
6. **Never trust client-provided school_id** - always use authenticated user's context

---

## Performance Tips

1. **Cache school information** to avoid repeated queries
2. **Use database indexes** on `branch_id` columns
3. **Eager load school relationships** in queries
4. **Consider adding scope globally** for automatic filtering
5. **Profile queries** to find N+1 problems

---

## API Usage

If building APIs, consider using custom responses:

```php
class ApiController extends Controller
{
    protected function response($data = null, $message = '', $code = 200)
    {
        return response()->json([
            'success' => $code < 400,
            'message' => $message,
            'data' => $data,
            'school_id' => session('school_id'),
        ], $code);
    }

    protected function unauthorized()
    {
        return $this->response(null, 'Unauthorized school access', 403);
    }
}
```

---

## Related Files

- `/app/Http/Kernel.php` - Middleware registration
- `/app/Models/User.php` - User model with role_id
- `/app/Enums/RoleEnum.php` - Role enumeration
- `/database/migrations/*/users_table.php` - User schema
- `routes/*.php` - Route definitions

---

## Support & Debugging

Enable debug logging:

```php
// In SchoolContext middleware
Log::debug('School context set', [
    'user_id' => $user->id,
    'school_id' => $schoolId,
    'is_admin' => $isAdmin,
    'current_school' => $currentSchool,
]);
```

Check session values:

```php
dd(session()->all()); // Show all session data
dd(session('school_id')); // Show school_id specifically
```
