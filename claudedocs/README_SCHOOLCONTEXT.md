# SchoolContext Middleware Documentation Suite

This directory contains comprehensive documentation for the SchoolContext middleware implementation in the School Management System.

## Files Overview

### 1. **SchoolContext Middleware** (Implementation)
- **File**: `/app/Http/Middleware/SchoolContext.php`
- **Purpose**: Middleware that establishes school context for authenticated users
- **Features**:
  - Automatic school ID detection from user
  - View variable sharing
  - Request attribute storage
  - Admin context switching
  - Session management
  - Static helper methods

### 2. **SCHOOLCONTEXT_MIDDLEWARE.md** (Full Documentation)
- **Purpose**: Complete reference documentation
- **Sections**:
  - Features overview
  - Registration instructions
  - Usage in controllers, services, views, and jobs
  - Admin context switching patterns
  - Authorization patterns
  - Database considerations
  - Error handling
  - Testing strategies
  - API usage examples

### 3. **SCHOOLCONTEXT_EXAMPLES.md** (Implementation Examples)
- **Purpose**: Production-ready code examples
- **Sections**:
  - Controller examples (resource, admin, API)
  - Service layer examples
  - Route protection patterns
  - Database scopes
  - Job/queue examples
  - Blade template examples
  - Test suite examples

### 4. **SCHOOLCONTEXT_QUICK_REFERENCE.md** (Quick Lookup)
- **Purpose**: Fast lookup for common tasks
- **Sections**:
  - Quick code snippets
  - Key method reference
  - Data structures
  - Role detection
  - Common patterns
  - Troubleshooting table

### 5. **SCHOOLCONTEXT_SETUP.md** (Setup & Configuration)
- **Purpose**: Step-by-step implementation guide
- **Sections**:
  - Prerequisites checklist
  - Installation steps
  - Database setup
  - Kernel registration
  - Route configuration
  - Model updates
  - Testing configuration
  - Troubleshooting guide
  - Verification checklist

---

## Quick Start (5 Minutes)

### 1. Register Middleware
```php
// app/Http/Kernel.php
protected $middleware = [
    \App\Http\Middleware\SchoolContext::class,
];
```

### 2. Use in Controller
```php
public function index(Request $request)
{
    $schoolId = $request->attributes->get('school_id');
    $students = Student::where('branch_id', $schoolId)->get();
    return view('students.index', compact('students'));
}
```

### 3. Use in View
```blade
<h1>{{ $currentSchool->name }}</h1>
<p>School ID: {{ $school_id }}</p>
```

---

## Architecture

### Data Flow
```
User Login
    ↓
Request → SchoolContext Middleware
    ↓
Determine School ID (user.branch_id or session)
    ↓
Set Session Variables
    ↓
Share with Views & Store in Request
    ↓
Available in Controllers, Views, Services, Jobs
```

### Context Storage
```
Session:
  ├── school_id (user's school)
  └── admin_school_context (admin's temporary context)

Request Attributes:
  ├── school_id
  ├── current_school (object)
  ├── is_admin (boolean)
  └── (available via $request->attributes->get())

View Variables:
  ├── $school_id
  ├── $currentSchool
  ├── $isAdmin
  └── $currentUser
```

---

## Key Features

### 1. Automatic Detection
- Determines school from authenticated user's `branch_id`
- Admin users can override context temporarily

### 2. View Sharing
- All Blade templates have access to school context
- Reduce controller boilerplate

### 3. Request Storage
- Context available in request attributes
- Accessible throughout request lifecycle

### 4. Admin Flexibility
- Admins can switch school context
- Non-persistent session changes
- Can be reverted

### 5. Static Helpers
- `userBelongsToSchool()` - Authorization check
- `getSessionSchoolId()` - Non-request context
- `setAdminSchoolContext()` - Context switching
- `clearAdminSchoolContext()` - Reset context

---

## Role-Based Behavior

### Regular Users (STAFF, TEACHER, STUDENT, etc.)
- Limited to their assigned school (`branch_id`)
- Cannot access other schools' data
- Straightforward context: `user.branch_id`

### Admin Users (ADMIN, SUPERADMIN)
- Can access all schools
- Can temporarily switch context
- Context can be overridden in session
- Default context: `user.branch_id`

---

## Security Model

### Data Isolation
- Every school-related table must have `branch_id`
- Queries automatically scoped to school
- Cross-school access requires explicit bypass

### Authorization Layers
1. **Middleware Level**: Context detection
2. **Controller Level**: `userBelongsToSchool()` check
3. **Policy Level**: Authorization policies
4. **Query Level**: Automatic scopes

### Audit Trail
- Admin context switches should be logged
- Database changes should include school_id
- Sensitive operations tracked

---

## Common Use Cases

### 1. Multi-Branch School System
```php
$students = Student::where('branch_id', $schoolId)->get();
```

### 2. Admin Dashboard with School Switching
```php
SchoolContext::setAdminSchoolContext($newSchoolId);
// ... admin operations ...
SchoolContext::clearAdminSchoolContext();
```

### 3. API with School Context
```php
{
    "success": true,
    "data": [...],
    "school_id": 1
}
```

### 4. Background Jobs
```php
public function __construct()
{
    $this->schoolId = SchoolContext::getSessionSchoolId();
}
```

---

## Database Requirements

### Mandatory Schema Changes
1. **Users table**: Add `branch_id` column
2. **Branches table**: Create with basic fields
3. **School-related tables**: Add `branch_id` to all
4. **Indexes**: Add index on `branch_id` for performance

### Migration Example
```php
Schema::table('students', function (Blueprint $table) {
    $table->unsignedBigInteger('branch_id')->default(1);
    $table->foreign('branch_id')->references('id')->on('branches');
    $table->index('branch_id');
});
```

---

## Testing Strategy

### Unit Tests
```php
public function test_service_respects_school_context()
{
    session(['school_id' => $school->id]);
    $service = new StudentService();
    $students = $service->getActive();
    
    $this->assertTrue($students->every(fn($s) => $s->branch_id === $school->id));
}
```

### Feature Tests
```php
public function test_user_cannot_access_other_schools()
{
    $response = $this->actingAs($schoolUser)
        ->get(route('students.show', $otherSchoolStudent));
    
    $response->assertForbidden();
}
```

---

## Performance Tips

1. **Cache School Data**
   ```php
   Cache::remember("school_{$id}", 3600, fn() => Branch::find($id));
   ```

2. **Index branch_id Columns**
   ```php
   $table->index('branch_id');
   ```

3. **Use Eager Loading**
   ```php
   Student::with('school')->currentSchool()->get();
   ```

4. **Implement Scopes**
   ```php
   Student::currentSchool()->active()->paginate();
   ```

---

## Common Issues & Solutions

| Issue | Solution |
|-------|----------|
| School context null | Check user authentication and `branch_id` |
| View variables undefined | Verify middleware registered globally |
| Admin context not switching | Ensure user role is ADMIN/SUPERADMIN |
| Cross-school access allowed | Add `userBelongsToSchool()` check |
| Session loses context | Configure session driver |
| Jobs lose context | Capture `schoolId` in constructor |

---

## Documentation Map

```
SchoolContext Implementation
├── Middleware
│   └── app/Http/Middleware/SchoolContext.php
├── Setup & Installation
│   └── SCHOOLCONTEXT_SETUP.md
├── Usage & Integration
│   ├── SCHOOLCONTEXT_MIDDLEWARE.md (full reference)
│   ├── SCHOOLCONTEXT_EXAMPLES.md (code examples)
│   └── SCHOOLCONTEXT_QUICK_REFERENCE.md (quick lookup)
└── This File
    └── README_SCHOOLCONTEXT.md
```

---

## Next Steps

1. **Review** SCHOOLCONTEXT_SETUP.md for installation
2. **Register** middleware in app/Http/Kernel.php
3. **Update** database with branch_id columns
4. **Implement** in controllers using examples
5. **Test** with multiple schools
6. **Deploy** with confidence

---

## Integration Points

- **Authentication**: Works with any auth system
- **Authorization**: Compatible with policies
- **Database**: Requires `branch_id` column
- **Sessions**: Uses Laravel sessions
- **Caching**: Integrates with cache
- **Testing**: Trait-based test support
- **APIs**: JSON response integration

---

## Key Principles

1. **Default Safe**: By default restricts to user's school
2. **Explicit Override**: Admin overrides are explicit and temporary
3. **Audit Trail**: All context switches should be logged
4. **Zero Trust**: Never assume school context, always verify
5. **Performance First**: Uses caching and indexes strategically
6. **Backward Compatible**: Works with existing code structure

---

## Support Resources

- **Full Documentation**: See SCHOOLCONTEXT_MIDDLEWARE.md
- **Code Examples**: See SCHOOLCONTEXT_EXAMPLES.md
- **Quick Lookup**: See SCHOOLCONTEXT_QUICK_REFERENCE.md
- **Setup Guide**: See SCHOOLCONTEXT_SETUP.md
- **Middleware Source**: See app/Http/Middleware/SchoolContext.php

---

## Version Information

- **Created**: 2025-11-05
- **Laravel Version**: 8.0+
- **PHP Version**: 7.4+
- **Status**: Production Ready

---

## Contributing

When extending SchoolContext middleware:

1. Maintain backward compatibility
2. Update all documentation files
3. Add tests for new features
4. Follow existing code style
5. Document breaking changes

---

## License

This middleware is part of the School Management System project.
