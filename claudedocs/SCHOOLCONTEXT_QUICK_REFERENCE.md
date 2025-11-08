# SchoolContext Middleware - Quick Reference Card

## Registration

```php
// app/Http/Kernel.php
protected $middleware = [
    \App\Http\Middleware\SchoolContext::class,
];
```

---

## In Controllers

### Access School Context
```php
$schoolId = $request->attributes->get('school_id');
$currentSchool = $request->attributes->get('current_school');
$isAdmin = $request->attributes->get('is_admin');
```

### Verify School Access
```php
if (!SchoolContext::userBelongsToSchool($request, $studentSchoolId)) {
    abort(403);
}
```

---

## In Services/Jobs

### Get Current School
```php
$schoolId = SchoolContext::getSessionSchoolId();
```

---

## In Views

### Access Shared Variables
```blade
{{ $school_id }}
{{ $currentSchool->name }}
{{ $isAdmin ? 'Admin' : 'User' }}
{{ $currentUser->name }}
```

---

## Admin Context Switching

### Switch Context
```php
SchoolContext::setAdminSchoolContext($schoolId);
```

### Reset Context
```php
SchoolContext::clearAdminSchoolContext();
```

---

## Model Scope

### Auto-Scope Queries
```php
$students = Student::currentSchool()->get();
```

---

## Data Structure

### Stored in Session
```php
session('school_id')           // Current school ID
session('admin_school_context') // Admin's switched context
```

### Stored in Request Attributes
```php
$request->attributes->get('school_id')
$request->attributes->get('current_school')
$request->attributes->get('is_admin')
```

### Shared with Views
```php
$school_id      // User's school ID
$currentSchool  // School object
$isAdmin        // Boolean
$currentUser    // Authenticated user
```

---

## Role Detection

### Admin Roles
- RoleEnum::SUPERADMIN (1)
- RoleEnum::ADMIN (2)

### Regular Roles
- All others (STAFF, TEACHER, STUDENT, GUARDIAN)

---

## Database Requirements

### Users Table
```php
$table->unsignedBigInteger('branch_id')->default(1);
```

### Branches Table
```php
Schema::create('branches', function (Blueprint $table) {
    $table->id();
    $table->string('name');
    $table->string('status')->default('active');
    $table->timestamps();
});
```

### School-Related Tables
```php
$table->unsignedBigInteger('branch_id')->default(1);
$table->foreign('branch_id')->references('id')->on('branches');
```

---

## Common Patterns

### Verify & Update
```php
if (!SchoolContext::userBelongsToSchool($request, $item->branch_id)) {
    abort(403);
}

$item->update($request->validated());
```

### Filter & Paginate
```php
$items = Item::where('branch_id', $request->attributes->get('school_id'))
    ->paginate(15);
```

### Create with Context
```php
Item::create([
    ...$data,
    'branch_id' => $request->attributes->get('school_id'),
]);
```

---

## Error Handling

### Check School Context
```php
$schoolId = $request->attributes->get('school_id');

if (!$schoolId) {
    return back()->with('error', 'School context not available');
}
```

### Try-Catch in Service
```php
try {
    $service = new StudentService();
} catch (Exception $e) {
    Log::error('Service error: ' . $e->getMessage());
    return back()->with('error', 'Service unavailable');
}
```

---

## Testing

### Set Up Test
```php
$school = Branch::factory()->create();
$user = User::factory()->create(['branch_id' => $school->id]);

$this->actingAs($user)
    ->get(route('students.index'))
    ->assertOk();
```

### Test Admin Access
```php
$admin = User::factory()->create(['role_id' => RoleEnum::ADMIN]);

$this->actingAs($admin)
    ->get(route('students.index'))
    ->assertOk();
```

---

## Security Checklist

- [ ] Always verify school ownership before data access
- [ ] Use policies for authorization
- [ ] Log context switches for audit
- [ ] Validate school_id from authenticated user only
- [ ] Clear admin context after operations
- [ ] Test cross-school access denial
- [ ] Implement rate limiting for admin switches
- [ ] Monitor for unusual context switching patterns

---

## Performance Tips

1. **Cache school data**
   ```php
   $school = Cache::remember("school_{$schoolId}", 3600, function() {
       return Branch::find($schoolId);
   });
   ```

2. **Index branch_id columns**
   ```php
   $table->index('branch_id');
   ```

3. **Eager load school relationships**
   ```php
   Student::with('school')->currentSchool()->get();
   ```

4. **Use scopes for filtering**
   ```php
   Student::currentSchool()->active()->get();
   ```

---

## Troubleshooting

| Issue | Solution |
|-------|----------|
| School context is null | Check user authentication and branch_id |
| Views show undefined variables | Verify middleware is registered globally |
| Admin context not switching | Ensure user role is ADMIN or SUPERADMIN |
| Cross-school access allowed | Verify userBelongsToSchool check is in place |
| Session loses context | Check session driver configuration |
| Jobs lose context | Capture schoolId in constructor |

---

## File Locations

- **Middleware**: `/app/Http/Middleware/SchoolContext.php`
- **Kernel**: `/app/Http/Kernel.php`
- **Full Documentation**: `/claudedocs/SCHOOLCONTEXT_MIDDLEWARE.md`
- **Examples**: `/claudedocs/SCHOOLCONTEXT_EXAMPLES.md`
- **This Reference**: `/claudedocs/SCHOOLCONTEXT_QUICK_REFERENCE.md`

---

## Key Methods Reference

```php
// Get school context from request
$context = SchoolContext::getSchoolContext($request);

// Check user belongs to school
$belongs = SchoolContext::userBelongsToSchool($request, $schoolId);

// Get school ID from session
$schoolId = SchoolContext::getSessionSchoolId();

// Set admin context
SchoolContext::setAdminSchoolContext($schoolId);

// Clear admin context
SchoolContext::clearAdminSchoolContext();
```

---

## API Documentation

### Response with School Context
```json
{
    "success": true,
    "message": "Operation successful",
    "data": {...},
    "school_id": 1
}
```

### Request Headers
```http
Authorization: Bearer {token}
X-School-ID: 1  (optional, for admin context)
```

---

## Environment Configuration

Ensure your `.env` has:
```
APP_SAAS=true|false          # Multi-tenant mode
CACHE_DRIVER=redis|database   # For caching school data
SESSION_DRIVER=file|database  # For session storage
```

---

## Related Documentation

- [Laravel Multi-Tenancy Patterns](https://laravel.com/docs/authorization)
- [Authorization & Policies](https://laravel.com/docs/authorization)
- [Middleware Documentation](https://laravel.com/docs/middleware)
- Project CLAUDE.md - Development guidelines
