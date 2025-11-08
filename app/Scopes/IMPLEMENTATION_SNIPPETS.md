# SchoolScope - Implementation Code Snippets

Quick-copy code snippets for implementing SchoolScope in your application.

## 1. Update BaseModel (5 minutes)

**File**: `app/Models/BaseModel.php`

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

        // Existing MultiBranch scope (if module is active)
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

## 2. Database Migration (if needed)

**Ensure your tables have school_id column**:

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Add school_id to existing table
        Schema::table('students', function (Blueprint $table) {
            // Add if not exists
            if (!Schema::hasColumn('students', 'school_id')) {
                $table->unsignedBigInteger('school_id')->nullable()->after('id');
                $table->foreign('school_id')
                    ->references('id')
                    ->on('schools')
                    ->nullOnDelete();
            }
        });

        // Create index for performance
        Schema::table('students', function (Blueprint $table) {
            $table->index('school_id');
            $table->index(['school_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::table('students', function (Blueprint $table) {
            $table->dropForeignKeyIfExists(['school_id']);
            $table->dropIndex(['school_id']);
            $table->dropIndex(['school_id', 'status']);
            $table->dropColumn('school_id');
        });
    }
};
```

## 3. User Model Setup

**Ensure User has school_id**:

```php
<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    protected $fillable = [
        'name',
        'email',
        'password',
        'school_id',  // Add this
        // ... other fields
    ];

    /**
     * Get the user's assigned school
     */
    public function school()
    {
        return $this->belongsTo(School::class);
    }

    /**
     * Check if user is super-admin (no school restriction)
     */
    public function isSuperAdmin(): bool
    {
        return $this->school_id === null;
    }

    /**
     * Check if user is admin (any admin type)
     */
    public function isAdmin(): bool
    {
        return $this->hasRole('admin') || $this->isSuperAdmin();
    }
}
```

## 4. User Migration

**Ensure User table has school_id**:

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Add school_id to users if not present
        if (!Schema::hasColumn('users', 'school_id')) {
            Schema::table('users', function (Blueprint $table) {
                $table->unsignedBigInteger('school_id')
                    ->nullable()
                    ->after('id')
                    ->comment('null = super-admin, id = assigned school');

                $table->foreign('school_id')
                    ->references('id')
                    ->on('schools')
                    ->nullOnDelete();

                $table->index('school_id');
            });
        }
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeignKeyIfExists(['school_id']);
            $table->dropIndex(['school_id']);
            $table->dropColumn('school_id');
        });
    }
};
```

## 5. Controller Usage Examples

### Example 1: Normal CRUD (Auto-Filtered)

```php
<?php

namespace App\Http\Controllers\StudentInfo;

use App\Models\StudentInfo\Student;
use Illuminate\Http\Request;

class StudentController extends Controller
{
    /**
     * List students (automatically filtered by school_id)
     */
    public function index()
    {
        // Automatically filtered by SchoolScope
        $students = Student::with('class', 'user')
            ->where('status', 'active')
            ->orderBy('name')
            ->paginate(15);

        return view('students.index', compact('students'));
    }

    /**
     * Create new student (school_id set automatically via model)
     */
    public function store(Request $request)
    {
        $student = Student::create(
            $request->validated() + ['school_id' => auth()->user()->school_id]
        );

        return redirect()->route('students.show', $student);
    }

    /**
     * Update student (scope ensures only their school's students)
     */
    public function update(Request $request, Student $student)
    {
        // Scope ensures this student belongs to user's school
        $student->update($request->validated());

        return redirect()->back();
    }

    /**
     * Delete student
     */
    public function destroy(Student $student)
    {
        // Scope ensures they can only delete their school's students
        $student->delete();

        return redirect()->back();
    }
}
```

### Example 2: Reports (Remove Scope)

```php
<?php

namespace App\Http\Controllers\Report;

use App\Models\StudentInfo\Student;
use App\Scopes\SchoolScope;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    /**
     * System-wide report (admin only)
     */
    public function systemReport()
    {
        // Remove scope to see all schools
        $stats = Student::withoutGlobalScope(SchoolScope::class)
            ->select(
                'school_id',
                DB::raw('COUNT(*) as total_students'),
                DB::raw('AVG(grade) as avg_grade')
            )
            ->groupBy('school_id')
            ->get();

        return view('reports.system', compact('stats'));
    }

    /**
     * School report (filtered to user's school)
     */
    public function schoolReport()
    {
        // Uses SchoolScope automatically
        $stats = Student::select(
            'status',
            DB::raw('COUNT(*) as count')
        )
            ->groupBy('status')
            ->get();

        return view('reports.school', compact('stats'));
    }
}
```

### Example 3: Admin School Switching

```php
<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;

class SchoolSwitchController extends Controller
{
    /**
     * Switch to view another school (admin only)
     */
    public function switchSchool($schoolId)
    {
        // Verify user is admin
        if (!auth()->user()->isSuperAdmin()) {
            abort(403, 'Only super-admin can switch schools');
        }

        // Set temporary school context
        session(['school_id' => $schoolId]);

        return redirect('/dashboard')
            ->with('success', "Switched to school #{$schoolId}");
    }

    /**
     * Reset to user's default school context
     */
    public function resetSchool()
    {
        session()->forget('school_id');

        return redirect('/dashboard')
            ->with('success', 'Returned to your school context');
    }

    /**
     * Show current school context
     */
    public function currentContext()
    {
        return response()->json([
            'user_school_id' => auth()->user()->school_id,
            'session_school_id' => session('school_id'),
            'is_admin' => auth()->user()->isSuperAdmin(),
        ]);
    }
}
```

## 6. Middleware for School Context

**Optional: Middleware to inject school_id into session**:

```php
<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureSchoolContext extends Middleware
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (auth()->check()) {
            $user = auth()->user();

            // Set school context in session if not already set
            if (!session()->has('school_id') && !$user->isSuperAdmin()) {
                session(['school_id' => $user->school_id]);
            }
        }

        return $next($request);
    }
}
```

**Register in Kernel**:

```php
// app/Http/Kernel.php
protected $middleware = [
    // ...
    \App\Http\Middleware\EnsureSchoolContext::class,
];
```

## 7. Service Layer Usage

```php
<?php

namespace App\Services\StudentInfo;

use App\Models\StudentInfo\Student;
use App\Scopes\SchoolScope;

class StudentService
{
    /**
     * Get active students (filtered to user's school)
     */
    public function getActiveStudents()
    {
        return Student::where('status', 'active')
            ->orderBy('name')
            ->get();
    }

    /**
     * Get students with low attendance (filtered to user's school)
     */
    public function getLowAttendanceStudents($threshold = 75)
    {
        return Student::where('attendance_percentage', '<', $threshold)
            ->orderBy('attendance_percentage')
            ->get();
    }

    /**
     * Get system-wide statistics (admin only)
     */
    public function getSystemStatistics()
    {
        return Student::withoutGlobalScope(SchoolScope::class)
            ->selectRaw('school_id, COUNT(*) as total, AVG(grade) as avg_grade')
            ->groupBy('school_id')
            ->get();
    }

    /**
     * Export students (from user's school)
     */
    public function exportStudents()
    {
        return Student::select('id', 'name', 'email', 'status')
            ->get();
    }
}
```

## 8. API Resource (with scope awareness)

```php
<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class StudentResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'status' => $this->status,
            'grade' => $this->grade,

            // Show school_id only if user is admin
            'school_id' => $request->user()->isSuperAdmin()
                ? $this->school_id
                : null,

            'school' => $this->when(
                $request->user()->isSuperAdmin(),
                $this->school_name
            ),
        ];
    }
}
```

## 9. API Controller with Scope Management

```php
<?php

namespace App\Http\Controllers\Api;

use App\Models\StudentInfo\Student;
use App\Http\Resources\StudentResource;
use App\Scopes\SchoolScope;

class StudentApiController extends Controller
{
    /**
     * List students (filtered by scope)
     */
    public function index()
    {
        return StudentResource::collection(
            Student::paginate(15)
        );
    }

    /**
     * Admin endpoint: List all students across schools
     */
    public function adminIndex()
    {
        $this->authorize('admin');

        return StudentResource::collection(
            Student::withoutGlobalScope(SchoolScope::class)->paginate(15)
        );
    }

    /**
     * Show single student
     */
    public function show(Student $student)
    {
        // Scope automatically filters, so student must belong to user's school
        return new StudentResource($student);
    }
}
```

## 10. Test Examples

### Unit Test

```php
<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\User;
use App\Models\StudentInfo\Student;
use App\Scopes\SchoolScope;

class StudentScopeTest extends TestCase
{
    use RefreshDatabase;

    public function test_student_scope_filters_by_school()
    {
        // Create users with different schools
        $user1 = User::factory()->create(['school_id' => 1]);
        $user2 = User::factory()->create(['school_id' => 2]);

        // Create students for each school
        $student1 = Student::create([
            'name' => 'Alice',
            'school_id' => 1,
            'user_id' => $user1->id,
        ]);

        $student2 = Student::create([
            'name' => 'Bob',
            'school_id' => 2,
            'user_id' => $user2->id,
        ]);

        // User 1 should only see their student
        $this->actingAs($user1);
        $students = Student::all();

        $this->assertCount(1, $students);
        $this->assertEquals('Alice', $students->first()->name);
    }

    public function test_admin_sees_all_schools()
    {
        $admin = User::factory()->create(['school_id' => null]);

        Student::create(['name' => 'Alice', 'school_id' => 1]);
        Student::create(['name' => 'Bob', 'school_id' => 2]);

        $this->actingAs($admin);
        $students = Student::all();

        $this->assertCount(2, $students);
    }

    public function test_scope_can_be_removed()
    {
        $user = User::factory()->create(['school_id' => 1]);

        Student::create(['name' => 'Alice', 'school_id' => 1]);
        Student::create(['name' => 'Bob', 'school_id' => 2]);

        $this->actingAs($user);

        // Without scope, should see all
        $students = Student::withoutGlobalScope(SchoolScope::class)->get();
        $this->assertCount(2, $students);
    }
}
```

### Feature Test

```php
<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\StudentInfo\Student;

class StudentFeatureTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_list_their_school_students()
    {
        $user = User::factory()->create(['school_id' => 1]);

        Student::create(['name' => 'Alice', 'school_id' => 1]);
        Student::create(['name' => 'Bob', 'school_id' => 2]);

        $response = $this->actingAs($user)->get('/api/students');

        $response->assertStatus(200)
                 ->assertJsonCount(1, 'data');
    }
}
```

## Quick Checklist

- [ ] Copy `SchoolScope.php` to `app/Scopes/`
- [ ] Update `BaseModel.php` with scope registration
- [ ] Run migration to add `school_id` to users table
- [ ] Run migration to add `school_id` to school-filtered tables
- [ ] Update User model with `isSuperAdmin()` helper
- [ ] Test with regular user (should see only their school)
- [ ] Test with admin (should see all schools)
- [ ] Run unit tests: `./vendor/bin/phpunit tests/Unit/Scopes/`
- [ ] Run feature tests: `./vendor/bin/phpunit tests/Feature/SchoolScopeFeatureTest.php`

## Need Help?

- **Quick Start**: Read `README.md`
- **Full Details**: Read `SCOPE_DOCUMENTATION.md`
- **Step-by-Step**: Follow `INTEGRATION_GUIDE.md`
- **See Tests**: Check test files for real examples
