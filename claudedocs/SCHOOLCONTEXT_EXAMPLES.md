# SchoolContext Middleware - Implementation Examples

Complete working examples for using the SchoolContext middleware in your application.

---

## Table of Contents
1. [Controller Examples](#controller-examples)
2. [Service Layer Examples](#service-layer-examples)
3. [Route Protection](#route-protection)
4. [Database Scopes](#database-scopes)
5. [Job Examples](#job-examples)
6. [Blade Template Examples](#blade-template-examples)
7. [Testing Examples](#testing-examples)

---

## Controller Examples

### Example 1: Basic Resource Controller with School Filtering

```php
<?php

namespace App\Http\Controllers;

use App\Models\Student;
use App\Http\Requests\StoreStudentRequest;
use App\Http\Middleware\SchoolContext;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class StudentController extends Controller
{
    /**
     * Display list of students for current school
     */
    public function index(Request $request)
    {
        $schoolId = $request->attributes->get('school_id');

        $students = Student::where('branch_id', $schoolId)
            ->where('status', 'active')
            ->with(['user', 'parentGuardian'])
            ->paginate(15);

        return view('students.index', [
            'students' => $students,
            'school_id' => $schoolId,
        ]);
    }

    /**
     * Show single student with access control
     */
    public function show(Request $request, Student $student)
    {
        // Verify user can access this student
        if (!SchoolContext::userBelongsToSchool($request, $student->branch_id)) {
            abort(403, 'You do not have access to this student');
        }

        return view('students.show', [
            'student' => $student->load(['user', 'parentGuardian', 'attendance']),
            'school_id' => $request->attributes->get('school_id'),
        ]);
    }

    /**
     * Store new student in current school
     */
    public function store(Request $request, StoreStudentRequest $validated)
    {
        $schoolId = $request->attributes->get('school_id');

        // Create student with school context
        $student = Student::create([
            ...$validated->validated(),
            'branch_id' => $schoolId,
        ]);

        // Send notification
        event(new StudentEnrolled($student, $schoolId));

        return back()->with('success', 'Student enrolled successfully');
    }

    /**
     * Update student in same school
     */
    public function update(Request $request, Student $student)
    {
        if (!SchoolContext::userBelongsToSchool($request, $student->branch_id)) {
            abort(403, 'Cannot update student from different school');
        }

        $student->update($request->validated());

        return back()->with('success', 'Student updated');
    }

    /**
     * Delete student from current school
     */
    public function destroy(Request $request, Student $student)
    {
        if (!SchoolContext::userBelongsToSchool($request, $student->branch_id)) {
            abort(403, 'Cannot delete student from different school');
        }

        $student->delete();

        return redirect()->route('students.index')
            ->with('success', 'Student deleted');
    }
}
```

### Example 2: Admin Dashboard Controller with Context Switching

```php
<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Middleware\SchoolContext;
use App\Models\StudentInfo\Student;
use App\Models\Staff\Staff;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Enums\RoleEnum;

class AdminDashboardController extends Controller
{
    /**
     * Show admin dashboard
     */
    public function index(Request $request)
    {
        $schoolId = $request->attributes->get('school_id');
        $isAdmin = $request->attributes->get('is_admin');

        if (!$isAdmin) {
            abort(403, 'Admin access required');
        }

        $school = $request->attributes->get('current_school');

        $statistics = [
            'total_students' => Student::where('branch_id', $schoolId)->count(),
            'active_students' => Student::where('branch_id', $schoolId)
                ->where('status', 'active')
                ->count(),
            'total_staff' => Staff::where('branch_id', $schoolId)->count(),
            'fees_pending' => DB::table('fees_assign')
                ->where('branch_id', $schoolId)
                ->where('payment_status', 'pending')
                ->count(),
        ];

        $schools = DB::table('branches')
            ->where('status', 'active')
            ->get();

        return view('admin.dashboard', [
            'statistics' => $statistics,
            'school' => $school,
            'schools' => $schools,
            'currentSchoolId' => $schoolId,
        ]);
    }

    /**
     * Switch admin school context
     */
    public function switchSchool(Request $request, int $schoolId)
    {
        // Verify admin role
        if (auth()->user()->role_id != RoleEnum::SUPERADMIN &&
            auth()->user()->role_id != RoleEnum::ADMIN) {
            abort(403, 'Admin access required');
        }

        // Verify school exists
        $school = DB::table('branches')->find($schoolId);
        if (!$school) {
            return back()->with('error', 'School not found');
        }

        // Set temporary context
        SchoolContext::setAdminSchoolContext($schoolId);

        return back()->with('success', "Switched to {$school->name}");
    }

    /**
     * Reset admin context to default
     */
    public function resetContext(Request $request)
    {
        SchoolContext::clearAdminSchoolContext();

        return back()->with('success', 'Context reset to default');
    }

    /**
     * View school comparison report
     */
    public function compareSchools(Request $request)
    {
        if (!$request->attributes->get('is_admin')) {
            abort(403);
        }

        $schools = DB::table('branches')
            ->where('status', 'active')
            ->get();

        $comparison = [];

        foreach ($schools as $school) {
            $comparison[$school->id] = [
                'school' => $school,
                'students' => Student::where('branch_id', $school->id)->count(),
                'staff' => Staff::where('branch_id', $school->id)->count(),
                'classes' => DB::table('class_rooms')
                    ->where('branch_id', $school->id)
                    ->count(),
            ];
        }

        return view('admin.comparison', ['comparison' => $comparison]);
    }
}
```

### Example 3: API Controller with School Context

```php
<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Student;
use App\Http\Resources\StudentResource;
use Illuminate\Http\Request;

class StudentApiController extends Controller
{
    /**
     * Get students list as JSON
     */
    public function index(Request $request)
    {
        $schoolId = $request->attributes->get('school_id');

        if (!$schoolId) {
            return $this->errorResponse('School context not available', 400);
        }

        $query = Student::where('branch_id', $schoolId);

        // Apply filters
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        if ($request->has('search')) {
            $search = $request->search;
            $query->where('first_name', 'like', "%{$search}%")
                ->orWhere('last_name', 'like', "%{$search}%");
        }

        $students = $query->paginate($request->per_page ?? 15);

        return $this->successResponse(
            StudentResource::collection($students),
            'Students retrieved successfully'
        );
    }

    /**
     * Get single student
     */
    public function show(Request $request, Student $student)
    {
        if ($student->branch_id != $request->attributes->get('school_id')) {
            return $this->errorResponse('Unauthorized', 403);
        }

        return $this->successResponse(
            new StudentResource($student),
            'Student retrieved'
        );
    }

    /**
     * Create new student
     */
    public function store(Request $request)
    {
        $schoolId = $request->attributes->get('school_id');

        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|unique:students',
            'dob' => 'required|date|before:today',
            'admission_date' => 'required|date',
        ]);

        $student = Student::create([
            ...$validated,
            'branch_id' => $schoolId,
        ]);

        return $this->successResponse(
            new StudentResource($student),
            'Student created successfully',
            201
        );
    }

    /**
     * API Response Helper
     */
    protected function successResponse($data = null, $message = '', $code = 200)
    {
        return response()->json([
            'success' => true,
            'message' => $message,
            'data' => $data,
            'school_id' => session('school_id'),
        ], $code);
    }

    protected function errorResponse($message, $code = 400)
    {
        return response()->json([
            'success' => false,
            'message' => $message,
            'code' => $code,
        ], $code);
    }
}
```

---

## Service Layer Examples

### Example 1: Student Service with School Context

```php
<?php

namespace App\Services;

use App\Models\Student;
use App\Http\Middleware\SchoolContext;
use Illuminate\Support\Collection;

class StudentService
{
    protected ?int $schoolId;

    /**
     * Initialize service with current school context
     */
    public function __construct()
    {
        $this->schoolId = SchoolContext::getSessionSchoolId();

        if (!$this->schoolId) {
            throw new \Exception('School context not available');
        }
    }

    /**
     * Get all active students for current school
     */
    public function getActive(): Collection
    {
        return Student::where('branch_id', $this->schoolId)
            ->where('status', 'active')
            ->with('user')
            ->orderBy('first_name')
            ->get();
    }

    /**
     * Get students by class
     */
    public function getByClass(int $classId): Collection
    {
        return Student::where('branch_id', $this->schoolId)
            ->whereHas('sessionClassStudent', function ($query) use ($classId) {
                $query->where('class_id', $classId);
            })
            ->get();
    }

    /**
     * Get student with full details
     */
    public function getWithDetails(int $studentId): ?Student
    {
        return Student::where('branch_id', $this->schoolId)
            ->where('id', $studentId)
            ->with([
                'user',
                'parentGuardian',
                'attendance',
                'sessionClassStudent.classRoom',
            ])
            ->first();
    }

    /**
     * Enroll new student
     */
    public function enroll(array $data): Student
    {
        $data['branch_id'] = $this->schoolId;

        return Student::create($data);
    }

    /**
     * Update student record
     */
    public function update(int $studentId, array $data): bool
    {
        return Student::where('branch_id', $this->schoolId)
            ->where('id', $studentId)
            ->update($data);
    }

    /**
     * Suspend student
     */
    public function suspend(int $studentId, string $reason = ''): bool
    {
        return $this->update($studentId, [
            'status' => 'suspended',
            'suspension_reason' => $reason,
        ]);
    }

    /**
     * Get student statistics
     */
    public function getStatistics(): array
    {
        $total = Student::where('branch_id', $this->schoolId)->count();
        $active = Student::where('branch_id', $this->schoolId)
            ->where('status', 'active')
            ->count();
        $suspended = $total - $active;

        return [
            'total' => $total,
            'active' => $active,
            'suspended' => $suspended,
        ];
    }
}
```

### Example 2: Attendance Service

```php
<?php

namespace App\Services;

use App\Models\StudentInfo\Student;
use App\Http\Middleware\SchoolContext;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class AttendanceService
{
    protected ?int $schoolId;

    public function __construct()
    {
        $this->schoolId = SchoolContext::getSessionSchoolId();
    }

    /**
     * Mark student present
     */
    public function markPresent(int $studentId, Carbon $date = null): bool
    {
        $date = $date ?? now()->toDateString();

        return DB::table('attendance')
            ->updateOrCreate(
                [
                    'student_id' => $studentId,
                    'branch_id' => $this->schoolId,
                    'attendance_date' => $date,
                ],
                ['status' => 'present']
            );
    }

    /**
     * Mark student absent
     */
    public function markAbsent(int $studentId, Carbon $date = null): bool
    {
        $date = $date ?? now()->toDateString();

        return DB::table('attendance')
            ->updateOrCreate(
                [
                    'student_id' => $studentId,
                    'branch_id' => $this->schoolId,
                    'attendance_date' => $date,
                ],
                ['status' => 'absent']
            );
    }

    /**
     * Bulk mark attendance
     */
    public function bulkMark(array $records): int
    {
        $insertData = array_map(function ($record) {
            return [
                'student_id' => $record['student_id'],
                'branch_id' => $this->schoolId,
                'attendance_date' => $record['date'] ?? now()->toDateString(),
                'status' => $record['status'],
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }, $records);

        return DB::table('attendance')
            ->upsert(
                $insertData,
                ['student_id', 'attendance_date', 'branch_id'],
                ['status']
            );
    }

    /**
     * Get attendance percentage for student
     */
    public function getPercentage(int $studentId, Carbon $from = null, Carbon $to = null): float
    {
        $from = $from ?? now()->startOfMonth();
        $to = $to ?? now()->endOfMonth();

        $present = DB::table('attendance')
            ->where('student_id', $studentId)
            ->where('branch_id', $this->schoolId)
            ->whereBetween('attendance_date', [$from, $to])
            ->where('status', 'present')
            ->count();

        $total = DB::table('attendance')
            ->where('student_id', $studentId)
            ->where('branch_id', $this->schoolId)
            ->whereBetween('attendance_date', [$from, $to])
            ->count();

        return $total > 0 ? ($present / $total) * 100 : 0;
    }
}
```

---

## Route Protection

### Example: Protected Routes with School Context

```php
<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\Admin\AdminDashboardController;

// Apply middleware to all these routes
Route::middleware(['auth', 'verified', 'school-context'])
    ->group(function () {
        // Student management
        Route::resource('students', StudentController::class);
        Route::post('students/bulk-upload', [StudentController::class, 'bulkUpload']);

        // Attendance management
        Route::prefix('attendance')->group(function () {
            Route::get('/', [AttendanceController::class, 'index']);
            Route::post('mark', [AttendanceController::class, 'mark']);
            Route::post('bulk-mark', [AttendanceController::class, 'bulkMark']);
            Route::get('report', [AttendanceController::class, 'report']);
        });

        // Admin routes with additional protection
        Route::middleware(['admin']) // Custom admin middleware
            ->prefix('admin')
            ->group(function () {
                Route::get('dashboard', [AdminDashboardController::class, 'index']);
                Route::post('switch-school/{school}', [AdminDashboardController::class, 'switchSchool']);
                Route::post('reset-context', [AdminDashboardController::class, 'resetContext']);
                Route::get('comparison', [AdminDashboardController::class, 'compareSchools']);
            });
    });
```

---

## Database Scopes

### Example: Global School Context Scope

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use App\Http\Middleware\SchoolContext;

abstract class BaseModel extends Model
{
    /**
     * Auto-scope queries to current school
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
     * Scope to specific school
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
            if (!$model->branch_id) {
                $model->branch_id = SchoolContext::getSessionSchoolId();
            }
        });
    }
}
```

### Using the Scope

```php
// Automatically filtered to current school
$students = Student::currentSchool()->get();

// Filtered to specific school
$schoolStudents = Student::forSchool(5)->get();

// Chaining with other conditions
$activeStudents = Student::currentSchool()
    ->where('status', 'active')
    ->orderBy('first_name')
    ->get();
```

---

## Job Examples

### Example: Async Processing with School Context

```php
<?php

namespace App\Jobs;

use App\Models\StudentInfo\Student;
use App\Http\Middleware\SchoolContext;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class ProcessStudentImport implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected int $schoolId;
    protected array $studentData;

    /**
     * Capture school context in constructor
     */
    public function __construct(array $studentData)
    {
        $this->studentData = $studentData;
        $this->schoolId = SchoolContext::getSessionSchoolId();

        if (!$this->schoolId) {
            throw new \Exception('School context not available for job');
        }
    }

    /**
     * Execute the job
     */
    public function handle(): void
    {
        if (!$this->schoolId) {
            $this->fail(new \Exception('School context lost during job execution'));
        }

        try {
            // Process students for this school
            foreach ($this->studentData as $data) {
                Student::create([
                    ...$data,
                    'branch_id' => $this->schoolId,
                ]);
            }

            Log::info('Student import completed', [
                'school_id' => $this->schoolId,
                'count' => count($this->studentData),
            ]);
        } catch (\Exception $e) {
            Log::error('Student import failed', [
                'school_id' => $this->schoolId,
                'error' => $e->getMessage(),
            ]);

            throw $e;
        }
    }

    /**
     * Handle job failure
     */
    public function failed(\Throwable $exception): void
    {
        Log::critical('Student import job failed', [
            'school_id' => $this->schoolId,
            'error' => $exception->getMessage(),
        ]);
    }
}
```

### Dispatching Jobs

```php
class StudentController extends Controller
{
    public function bulkImport(Request $request)
    {
        $schoolId = $request->attributes->get('school_id');

        // School context is available during dispatch
        ProcessStudentImport::dispatch($request->input('students'));

        return back()->with('success', 'Import started');
    }
}
```

---

## Blade Template Examples

### Example 1: Basic School Context Display

```blade
<!-- Show current school information -->
<div class="school-banner">
    <h1>{{ $currentSchool->name ?? 'School Management System' }}</h1>
    <p>Location: {{ $currentSchool->location ?? 'N/A' }}</p>
</div>

<!-- Show user role and school -->
<div class="user-info">
    <span>User: {{ $currentUser->name }}</span>
    @if($isAdmin)
        <span class="badge badge-admin">Admin</span>
    @else
        <span class="badge badge-user">Staff</span>
    @endif
    <span>School: {{ $school_id }}</span>
</div>
```

### Example 2: Admin Panel with Context Switching

```blade
@if($isAdmin)
    <div class="admin-panel">
        <h3>School Context</h3>

        <form action="{{ route('admin.switch-school') }}" method="POST" class="d-flex gap-2">
            @csrf

            <select name="school_id" class="form-control" required>
                <option value="">Select School...</option>
                @foreach($schoolOptions ?? [] as $school)
                    <option value="{{ $school->id }}"
                        {{ $school->id == $school_id ? 'selected' : '' }}>
                        {{ $school->name }}
                    </option>
                @endforeach
            </select>

            <button type="submit" class="btn btn-primary">Switch</button>
        </form>

        @if(session('school_id') != $currentUser->branch_id)
            <form action="{{ route('admin.reset-context') }}" method="POST" class="mt-2">
                @csrf
                <button type="submit" class="btn btn-secondary btn-sm">
                    Reset to Default
                </button>
            </form>
        @endif
    </div>
@endif
```

### Example 3: Data Table with School Filtering

```blade
<table class="table">
    <thead>
        <tr>
            <th>Name</th>
            <th>Email</th>
            <th>Class</th>
            <th>Status</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        @forelse($students as $student)
            <tr data-school-id="{{ $school_id }}">
                <td>{{ $student->full_name }}</td>
                <td>{{ $student->user->email ?? 'N/A' }}</td>
                <td>{{ $student->sessionClassStudent?->classRoom->name ?? 'N/A' }}</td>
                <td>
                    <span class="badge badge-{{ $student->status }}">
                        {{ $student->status }}
                    </span>
                </td>
                <td>
                    <a href="{{ route('students.show', $student) }}" class="btn btn-sm btn-info">
                        View
                    </a>
                    @if(auth()->user()->role_id <= 2) {{-- Admin or SuperAdmin --}}
                        <a href="{{ route('students.edit', $student) }}" class="btn btn-sm btn-warning">
                            Edit
                        </a>
                        <form action="{{ route('students.destroy', $student) }}"
                              method="POST" class="d-inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-danger"
                                    onclick="return confirm('Delete student?')">
                                Delete
                            </button>
                        </form>
                    @endif
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="5" class="text-center">No students found</td>
            </tr>
        @endforelse
    </tbody>
</table>

<!-- Pagination -->
<div class="d-flex justify-content-center">
    {{ $students->links() }}
</div>
```

---

## Testing Examples

### Example 1: Unit Tests with School Context

```php
<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Branch;
use App\Models\StudentInfo\Student;
use App\Enums\RoleEnum;

class StudentControllerTest extends TestCase
{
    use RefreshDatabase;

    protected Branch $school;
    protected User $schoolUser;
    protected User $adminUser;

    protected function setUp(): void
    {
        parent::setUp();

        $this->school = Branch::factory()->create(['name' => 'Test School']);

        $this->schoolUser = User::factory()->create([
            'branch_id' => $this->school->id,
            'role_id' => RoleEnum::STAFF,
        ]);

        $this->adminUser = User::factory()->create([
            'role_id' => RoleEnum::ADMIN,
        ]);
    }

    public function test_user_can_view_students_in_their_school()
    {
        $student = Student::factory()->create(['branch_id' => $this->school->id]);

        $response = $this->actingAs($this->schoolUser)
            ->get(route('students.show', $student));

        $response->assertOk();
        $response->assertSee($student->full_name);
    }

    public function test_user_cannot_view_students_from_other_schools()
    {
        $otherSchool = Branch::factory()->create();
        $student = Student::factory()->create(['branch_id' => $otherSchool->id]);

        $response = $this->actingAs($this->schoolUser)
            ->get(route('students.show', $student));

        $response->assertForbidden();
    }

    public function test_admin_can_view_students_from_any_school()
    {
        $otherSchool = Branch::factory()->create();
        $student = Student::factory()->create(['branch_id' => $otherSchool->id]);

        $response = $this->actingAs($this->adminUser)
            ->get(route('students.show', $student));

        $response->assertOk();
    }

    public function test_admin_can_switch_school_context()
    {
        $otherSchool = Branch::factory()->create();

        $response = $this->actingAs($this->adminUser)
            ->post(route('admin.switch-school', $otherSchool));

        $response->assertRedirect();
        $this->assertEquals($otherSchool->id, session('admin_school_context'));
    }

    public function test_student_creation_includes_school_context()
    {
        $data = [
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email' => 'john@example.com',
            'dob' => '2010-01-01',
        ];

        $response = $this->actingAs($this->schoolUser)
            ->post(route('students.store'), $data);

        $this->assertDatabaseHas('students', [
            ...$data,
            'branch_id' => $this->school->id,
        ]);
    }
}
```

### Example 2: Service Tests

```php
<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\User;
use App\Models\Branch;
use App\Models\StudentInfo\Student;
use App\Services\StudentService;
use App\Http\Middleware\SchoolContext;
use Illuminate\Support\Facades\Session;

class StudentServiceTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Initialize session for service
        Session::start();
    }

    public function test_service_retrieves_only_current_school_students()
    {
        $school1 = Branch::factory()->create();
        $school2 = Branch::factory()->create();

        Student::factory(5)->create(['branch_id' => $school1->id]);
        Student::factory(3)->create(['branch_id' => $school2->id]);

        // Set school context
        session(['school_id' => $school1->id]);

        $service = new StudentService();
        $students = $service->getActive();

        $this->assertEquals(5, $students->count());
        $this->assertTrue($students->every(fn($s) => $s->branch_id === $school1->id));
    }

    public function test_service_throws_without_school_context()
    {
        // Clear school context
        session()->forget('school_id');

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('School context not available');

        new StudentService();
    }
}
```

---

## Integration Checklist

- [ ] Register middleware in `app/Http/Kernel.php`
- [ ] Update User model if `branch_id` doesn't exist
- [ ] Ensure all school-related tables have `branch_id` column
- [ ] Update controllers to use school context
- [ ] Add scopes to models for automatic filtering
- [ ] Update routes to use middleware
- [ ] Create tests for school context boundaries
- [ ] Document API endpoints with school context
- [ ] Review security of admin context switching
- [ ] Add logging for audit trails
