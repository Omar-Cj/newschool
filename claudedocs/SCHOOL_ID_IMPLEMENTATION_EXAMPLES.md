# School ID Implementation - Code Examples

## Overview

This document provides practical code examples for working with the `school_id` field in the School Management System after migration.

---

## Database Queries

### Verification Queries (After Migration)

#### Check Records by School

```sql
-- Count students by school
SELECT school_id, COUNT(*) as student_count
FROM students
GROUP BY school_id
ORDER BY school_id;

-- Expected output for single school:
-- school_id: 1, student_count: [all students]
-- school_id: NULL, count: 0
```

#### Find Records Without school_id

```sql
-- Find students without school assignment
SELECT id, name, email
FROM students
WHERE school_id IS NULL OR school_id = 0;

-- Should return 0 rows after successful migration
```

#### Verify Admin Access Pattern

```sql
-- Check user school_id by role
SELECT
    u.id,
    u.name,
    r.name as role_name,
    u.school_id
FROM users u
LEFT JOIN roles r ON u.role_id = r.id
ORDER BY u.role_id, u.id;

-- Expected:
-- Admins (role_id=1): school_id = NULL
-- Teachers (role_id=2): school_id = 1
-- Students: school_id = 1
```

#### Identify Orphaned Records

```sql
-- Find records that might have issues
SELECT 'students' as table_name, COUNT(*) as problematic
FROM students
WHERE school_id IS NULL OR school_id = 0
UNION ALL
SELECT 'staff', COUNT(*)
FROM staff
WHERE school_id IS NULL OR school_id = 0;

-- Should return 0 for all tables
```

---

## Laravel ORM Queries

### Basic Queries with school_id

#### Get Students for Current School

```php
// Controller method
use App\Models\Student;

public function index()
{
    $schoolId = auth()->user()->school_id;

    // Get all students in current school
    $students = Student::where('school_id', $schoolId)->get();

    return view('students.index', ['students' => $students]);
}
```

#### Create Student with school_id

```php
public function store(StoreStudentRequest $request)
{
    $student = Student::create([
        'name' => $request->name,
        'email' => $request->email,
        'date_of_birth' => $request->dob,
        'school_id' => auth()->user()->school_id,  // Auto-assign
        // ... other fields
    ]);

    return redirect()->route('students.show', $student);
}
```

#### Admin Access (NULL school_id)

```php
public function showAllStudents()
{
    // Admin view - no school filter needed
    if (auth()->user()->role_id === 1) {  // Admin role
        // Admins see all students (school_id = NULL)
        $students = Student::all();
    } else {
        // Regular users see only their school
        $students = Student::where('school_id', auth()->user()->school_id)->get();
    }

    return view('students.all', ['students' => $students]);
}
```

---

### Query Scopes

#### Create Scope for school_id Filtering

```php
// In Student Model
use Illuminate\Database\Eloquent\Builder;

class Student extends Model
{
    /**
     * Scope to filter by current user's school
     *
     * Usage: Student::forCurrentSchool()->get()
     */
    public function scopeForCurrentSchool(Builder $query)
    {
        $user = auth()->user();

        // Admins (NULL school_id) see all records
        if ($user->school_id === null) {
            return $query;
        }

        // Regular users see only their school
        return $query->where('school_id', $user->school_id);
    }

    /**
     * Scope to filter by specific school
     *
     * Usage: Student::forSchool(1)->get()
     */
    public function scopeForSchool(Builder $query, int $schoolId)
    {
        return $query->where('school_id', $schoolId);
    }
}
```

#### Using Scopes in Controllers

```php
class StudentController extends Controller
{
    public function index()
    {
        // Automatically filters by current user's school (or all if admin)
        $students = Student::forCurrentSchool()
            ->with('class', 'section')
            ->paginate(15);

        return view('students.index', ['students' => $students]);
    }

    public function adminReport($schoolId)
    {
        // Get students from specific school
        $students = Student::forSchool($schoolId)
            ->where('status', 'active')
            ->count();

        return response()->json(['count' => $students]);
    }
}
```

---

### Global Scopes (Automatic Filtering)

#### Auto-Filter by school_id

```php
// In Student Model
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    protected static function booted()
    {
        // Automatically filter by school_id for all queries
        // except for admins
        static::addGlobalScope('school', function (Builder $builder) {
            $user = auth()->user();

            // Skip global scope for admins
            if ($user && $user->role_id === 1) {
                return;
            }

            // Filter by current user's school
            if ($user && $user->school_id) {
                $builder->where('students.school_id', $user->school_id);
            }
        });
    }

    // Method to bypass global scope when needed
    public static function withoutSchoolFilter()
    {
        return static::withoutGlobalScope('school');
    }
}
```

#### Usage with Global Scope

```php
class StudentController extends Controller
{
    public function index()
    {
        // Global scope automatically applied
        // Admin sees all, regular user sees only their school
        $students = Student::get();

        return view('students.index', ['students' => $students]);
    }

    public function adminAllStudents()
    {
        // Bypass global scope to see all students
        $students = Student::withoutSchoolFilter()->get();

        return view('students.all', ['students' => $students]);
    }
}
```

---

### Relationships with school_id

#### Define Relationships

```php
// Student Model
class Student extends Model
{
    public function school()
    {
        return $this->belongsTo(School::class, 'school_id');
    }

    public function class()
    {
        return $this->belongsTo(ClassRoom::class);
    }

    public function attendance()
    {
        // Ensure attendance belongs to same school
        return $this->hasMany(Attendance::class)
            ->where('school_id', $this->school_id);
    }
}

// Staff Model
class Staff extends Model
{
    public function school()
    {
        return $this->belongsTo(School::class, 'school_id');
    }

    public function department()
    {
        return $this->belongsTo(Department::class);
    }
}
```

#### Query with Relationships

```php
// Get student with school and class
$student = Student::with(['school', 'class'])
    ->find(1);

echo $student->school->name;  // Output: School 1
echo $student->class->name;   // Output: Class A

// Get all staff with their schools
$staff = Staff::with('school')
    ->forCurrentSchool()
    ->get();

foreach ($staff as $member) {
    echo $member->name . ' - ' . $member->school->name;
}
```

---

## API Implementation

### API Resources with school_id

#### Student Resource

```php
// app/Http/Resources/StudentResource.php
use Illuminate\Http\Resources\Json\JsonResource;

class StudentResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'school_id' => $this->school_id,
            'class' => new ClassResource($this->whenLoaded('class')),
            'school' => new SchoolResource($this->whenLoaded('school')),
            'created_at' => $this->created_at->toIsoString(),
            'updated_at' => $this->updated_at->toIsoString(),
        ];
    }
}
```

#### API Controller

```php
// app/Http/Controllers/Api/StudentController.php
use Illuminate\Http\Request;
use App\Models\Student;
use App\Http\Resources\StudentResource;

class StudentController extends Controller
{
    /**
     * Get all students for current school
     *
     * GET /api/students
     */
    public function index(Request $request)
    {
        $user = auth()->user();

        $query = Student::with('class', 'school');

        // Filter by school
        if ($user->role_id !== 1) {  // Not admin
            $query->where('school_id', $user->school_id);
        }

        // Optional filters
        if ($request->has('class_id')) {
            $query->where('class_id', $request->class_id);
        }

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        $students = $query->paginate($request->per_page ?? 15);

        return StudentResource::collection($students);
    }

    /**
     * Create new student
     *
     * POST /api/students
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:students',
            'class_id' => 'required|exists:class_rooms,id',
            'date_of_birth' => 'required|date',
        ]);

        // Auto-assign to current user's school
        $validated['school_id'] = auth()->user()->school_id;

        $student = Student::create($validated);

        return new StudentResource($student->load('class'));
    }

    /**
     * Get single student
     *
     * GET /api/students/{id}
     */
    public function show($id)
    {
        $user = auth()->user();

        $query = Student::with('class', 'school', 'attendance');

        // Verify access
        if ($user->role_id !== 1) {  // Not admin
            $query->where('school_id', $user->school_id);
        }

        $student = $query->findOrFail($id);

        return new StudentResource($student);
    }

    /**
     * Update student
     *
     * PUT /api/students/{id}
     */
    public function update(Request $request, $id)
    {
        $user = auth()->user();

        $student = Student::findOrFail($id);

        // Verify access
        if ($user->role_id !== 1 && $user->school_id !== $student->school_id) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $validated = $request->validate([
            'name' => 'string|max:255',
            'email' => 'email|unique:students,email,' . $id,
            'class_id' => 'exists:class_rooms,id',
        ]);

        // Never allow changing school_id via API
        unset($validated['school_id']);

        $student->update($validated);

        return new StudentResource($student);
    }
}
```

---

## Authorization Policies

### Create School-Aware Policies

```php
// app/Policies/StudentPolicy.php
use App\Models\User;
use App\Models\Student;

class StudentPolicy
{
    /**
     * View any students
     */
    public function viewAny(User $user)
    {
        // Admins can view any
        // Regular users can view in their school
        return true;
    }

    /**
     * View specific student
     */
    public function view(User $user, Student $student)
    {
        // Admin can view any
        if ($user->role_id === 1) {
            return true;
        }

        // Regular user can view only in their school
        return $user->school_id === $student->school_id;
    }

    /**
     * Create student
     */
    public function create(User $user)
    {
        // Only admins and staff (role_id > 1) can create
        return $user->role_id !== null && $user->role_id !== 3;  // 3 = student
    }

    /**
     * Update student
     */
    public function update(User $user, Student $student)
    {
        // Admin can update any
        if ($user->role_id === 1) {
            return true;
        }

        // Regular user can update only in their school
        return $user->school_id === $student->school_id;
    }

    /**
     * Delete student
     */
    public function delete(User $user, Student $student)
    {
        // Only admin can delete
        return $user->role_id === 1;
    }
}
```

### Using Policies in Controllers

```php
class StudentController extends Controller
{
    public function show(Student $student)
    {
        // Authorize user can view this student
        $this->authorize('view', $student);

        return view('students.show', ['student' => $student]);
    }

    public function update(Request $request, Student $student)
    {
        // Authorize user can update
        $this->authorize('update', $student);

        $student->update($request->validated());

        return redirect()->route('students.show', $student);
    }
}
```

---

## Service Layer Examples

### School-Aware Service

```php
// app/Services/StudentService.php
use App\Models\Student;
use App\Models\User;

class StudentService
{
    /**
     * Get students for user's school
     */
    public function getSchoolStudents(User $user, array $filters = [])
    {
        $query = Student::query();

        // Filter by school (unless admin)
        if ($user->role_id !== 1) {
            $query->where('school_id', $user->school_id);
        }

        // Apply additional filters
        if (isset($filters['class_id'])) {
            $query->where('class_id', $filters['class_id']);
        }

        if (isset($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        return $query->with('class', 'school')
            ->orderBy('name')
            ->get();
    }

    /**
     * Create student for school
     */
    public function createStudent(User $user, array $data)
    {
        // Ensure student is created for user's school
        $data['school_id'] = $user->school_id;

        return Student::create($data);
    }

    /**
     * Verify user can access student
     */
    public function canAccessStudent(User $user, Student $student): bool
    {
        // Admin can access any
        if ($user->role_id === 1) {
            return true;
        }

        // Regular user can access only in their school
        return $user->school_id === $student->school_id;
    }

    /**
     * Get student statistics for school
     */
    public function getSchoolStatistics(User $user)
    {
        $query = Student::query();

        if ($user->role_id !== 1) {
            $query->where('school_id', $user->school_id);
        }

        return [
            'total_students' => $query->count(),
            'active_students' => $query->where('status', 'active')->count(),
            'by_class' => $query->groupBy('class_id')
                ->selectRaw('class_id, count(*) as count')
                ->get(),
            'by_section' => $query->groupBy('section_id')
                ->selectRaw('section_id, count(*) as count')
                ->get(),
        ];
    }
}
```

### Using Service in Controller

```php
class StudentController extends Controller
{
    protected $studentService;

    public function __construct(StudentService $studentService)
    {
        $this->studentService = $studentService;
    }

    public function index()
    {
        $filters = request()->only(['class_id', 'status']);

        $students = $this->studentService->getSchoolStudents(
            auth()->user(),
            $filters
        );

        return view('students.index', ['students' => $students]);
    }

    public function statistics()
    {
        $stats = $this->studentService->getSchoolStatistics(auth()->user());

        return view('dashboard.statistics', ['stats' => $stats]);
    }
}
```

---

## Testing Examples

### Test with school_id

```php
// tests/Feature/StudentTest.php
use Tests\TestCase;
use App\Models\Student;
use App\Models\User;

class StudentTest extends TestCase
{
    /**
     * Test regular user can only see their school's students
     */
    public function test_user_can_only_see_their_school_students()
    {
        // Create users
        $user = User::factory()->create(['school_id' => 1]);
        $adminUser = User::factory()->create(['school_id' => null, 'role_id' => 1]);

        // Create students for different schools
        $student1 = Student::factory()->create(['school_id' => 1]);
        $student2 = Student::factory()->create(['school_id' => 2]);

        // Regular user should see only their school
        $this->actingAs($user)
            ->getJson('/api/students')
            ->assertJsonCount(1)
            ->assertJsonFragment(['id' => $student1->id]);

        // Admin should see all
        $this->actingAs($adminUser)
            ->getJson('/api/students')
            ->assertJsonCount(2);
    }

    /**
     * Test creating student assigns current school
     */
    public function test_creating_student_assigns_current_school()
    {
        $user = User::factory()->create(['school_id' => 1]);

        $this->actingAs($user)
            ->postJson('/api/students', [
                'name' => 'John Doe',
                'email' => 'john@example.com',
                'class_id' => 1,
                'date_of_birth' => '2010-01-01',
            ])
            ->assertSuccessful();

        $student = Student::where('email', 'john@example.com')->first();

        $this->assertEquals(1, $student->school_id);
    }

    /**
     * Test user cannot update student from different school
     */
    public function test_user_cannot_update_other_school_student()
    {
        $user = User::factory()->create(['school_id' => 1]);
        $student = Student::factory()->create(['school_id' => 2]);

        $this->actingAs($user)
            ->putJson("/api/students/{$student->id}", [
                'name' => 'New Name',
            ])
            ->assertForbidden();
    }
}
```

---

## Migration Verification Script

### PHP Script to Verify Migration

```php
<?php
// scripts/verify_school_id_migration.php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

$tables = [
    'students', 'staff', 'classes', 'sections',
    'fees_collects', 'exam_assigns', 'attendances'
];

echo "Verifying school_id migration...\n\n";

foreach ($tables as $table) {
    if (!Schema::hasTable($table)) {
        echo "❌ Table '$table' does not exist\n";
        continue;
    }

    if (!Schema::hasColumn($table, 'school_id')) {
        echo "❌ Table '$table' missing school_id column\n";
        continue;
    }

    $nullCount = DB::table($table)
        ->whereNull('school_id')
        ->count();

    $zeroCount = DB::table($table)
        ->where('school_id', 0)
        ->count();

    $totalCount = DB::table($table)->count();

    if ($nullCount === 0 && $zeroCount === 0) {
        echo "✅ Table '$table': All {$totalCount} records populated with school_id\n";
    } else {
        echo "⚠️  Table '$table': {$nullCount} NULL, {$zeroCount} zero values out of {$totalCount}\n";
    }
}

// Check users table special logic
echo "\n\nUsers table (role-based):\n";
$adminWithSchool = DB::table('users')
    ->where('role_id', 1)
    ->where('school_id', '!=', null)
    ->count();

if ($adminWithSchool === 0) {
    echo "✅ All admin users have NULL school_id\n";
} else {
    echo "⚠️  {$adminWithSchool} admin users have non-NULL school_id\n";
}

$regularWithSchool = DB::table('users')
    ->where('role_id', '!=', 1)
    ->where('school_id', '=', 1)
    ->count();

echo "✅ {$regularWithSchool} regular users have school_id = 1\n";

echo "\n✓ Migration verification complete\n";
```

### Running Verification Script

```bash
# Create script in project root
php scripts/verify_school_id_migration.php

# Or as artisan command
php artisan tinker < scripts/verify_school_id_migration.php
```

---

## Common Patterns

### Multi-School Report Generation

```php
class ReportService
{
    /**
     * Generate report for specific school
     */
    public function generateSchoolReport($schoolId)
    {
        return [
            'students' => Student::where('school_id', $schoolId)->count(),
            'staff' => Staff::where('school_id', $schoolId)->count(),
            'fee_collection' => FeesCollect::where('school_id', $schoolId)
                ->sum('amount'),
            'attendance_average' => Attendance::where('school_id', $schoolId)
                ->where('status', 'present')
                ->count() / Attendance::where('school_id', $schoolId)
                ->count() * 100,
        ];
    }

    /**
     * Compare statistics across schools
     */
    public function compareSchools()
    {
        return DB::table('students')
            ->select('school_id', DB::raw('count(*) as total'))
            ->groupBy('school_id')
            ->orderBy('school_id')
            ->get();
    }
}
```

