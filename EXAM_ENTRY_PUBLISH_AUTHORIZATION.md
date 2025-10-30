# Exam Entry Authorization Implementation (Publish & Delete)

## Overview
This document describes the implementation of role-based authorization for the exam entry **publish** and **delete** functionality, restricting these actions to users with role_id 1 (Super Admin) and role_id 2 (Admin) only.

## Implementation Dates
- **Publish Authorization**: January 30, 2025
- **Delete Authorization**: January 30, 2025

## Requirements
- ✅ Only users with `role_id` 1 (Super Admin) or `role_id` 2 (Admin) can **publish** exam entries
- ✅ Only users with `role_id` 1 (Super Admin) or `role_id` 2 (Admin) can **delete** exam entries
- ❌ All other roles (3+), regardless of permissions, cannot publish or delete exam entries

## Security Architecture

### Four-Layer Security Model
Both publish and delete operations implement defense-in-depth with four layers of security:

1. **Frontend Layer**: Action buttons (Publish & Delete) hidden for unauthorized users
2. **Route Middleware Layer**: `PermissionCheck` validates permissions with role restrictions
3. **Controller Layer**: Explicit role_id check (1 or 2) AND permission validation
4. **Helper Function Layer**: `hasPermission()` enforces role restrictions globally

## Files Modified

### 1. Migration: `database/migrations/tenant/2025_01_30_000001_add_exam_entry_publish_permission.php`
**Purpose**: Create new `exam_entry_publish` permission and assign to roles 1 and 2

**Actions**:
- Updates `exam_entry` permission in `permissions` table to include `publish` keyword
- Adds `exam_entry_publish` to roles 1 and 2 permissions array
- Removes `exam_entry_publish` from all other roles (role_id > 2)
- Provides rollback functionality

**Key Code**:
```php
// Add publish permission to roles 1 and 2
$roles = Role::whereIn('id', [1, 2])->get();
foreach ($roles as $role) {
    if (!in_array('exam_entry_publish', $role->permissions)) {
        $permissions = $role->permissions;
        $permissions[] = 'exam_entry_publish';
        $role->permissions = $permissions;
        $role->save();
    }
}

// Remove from other roles
$otherRoles = Role::where('id', '>', 2)->get();
foreach ($otherRoles as $role) {
    $permissions = array_diff($role->permissions, ['exam_entry_publish']);
    $role->permissions = array_values($permissions);
    $role->save();
}
```

### 2. Route: `routes/examination.php` (Line 87)
**Change**: Updated middleware from `exam_entry_update` to `exam_entry_publish`

**Before**:
```php
Route::put('/publish/{id}', 'publish')->name('exam-entry.publish')
    ->middleware('PermissionCheck:exam_entry_update', 'DemoCheck');
```

**After**:
```php
Route::put('/publish/{id}', 'publish')->name('exam-entry.publish')
    ->middleware('PermissionCheck:exam_entry_publish', 'DemoCheck');
```

### 3. Controller: `app/Http/Controllers/Backend/Examination/ExamEntryController.php` (Lines 394-437)
**Purpose**: Add explicit role validation with dual-check security

**Implementation**:
```php
public function publish($id)
{
    // Check if user has required role (1 or 2) AND exam_entry_publish permission
    $userRoleId = auth()->user()->role_id;

    if (!in_array($userRoleId, [1, 2])) {
        return response()->json([
            'success' => false,
            'message' => 'Only Super Admin and Admin can publish exam entries'
        ], 403);
    }

    if (!hasPermission('exam_entry_publish')) {
        return response()->json([
            'success' => false,
            'message' => 'You do not have permission to publish exam entries'
        ], 403);
    }

    // Continue with publishing logic...
}
```

**Security Features**:
- **Role validation first**: Checks if user role_id is 1 or 2
- **Permission validation second**: Verifies user has `exam_entry_publish` permission
- **Clear error messages**: Distinguishes between role restriction and permission denial
- **HTTP 403 responses**: Standard unauthorized access code

### 4. Middleware: `app/Http/Middleware/PermissionCheck.php` (Lines 20-29)
**Purpose**: Add special handling for `exam_entry_publish` permission at middleware level

**Implementation**:
```php
public function handle(Request $request, Closure $next, $permission)
{
    // Special handling for exam_entry_publish permission - only roles 1 and 2
    if ($permission === 'exam_entry_publish') {
        if (Auth::check() && in_array(Auth::user()->role_id, [1, 2])) {
            // Check if user also has the permission
            if (in_array($permission, Auth::user()->permissions)) {
                return $next($request);
            }
        }
        return abort(403, 'Only Super Admin and Admin can publish exam entries');
    }

    // Default permission handling...
}
```

**Security Benefits**:
- **Defense-in-depth**: Additional security layer before reaching controller
- **Role-specific validation**: Enforces role restriction at middleware level
- **Consistent error handling**: Provides clear unauthorized message

### 5. Frontend: `app/Repositories/Examination/ExamEntryRepository.php` (Lines 139-150)
**Purpose**: Hide publish button for unauthorized users in DataTables

**Before**:
```php
// Publish button (only for completed)
if ($row->status === 'completed') {
    // Render publish button
}
```

**After**:
```php
// Publish button (only for completed AND user has role_id 1 or 2)
if ($row->status === 'completed' && in_array(auth()->user()->role_id, [1, 2])) {
    $action .= '<button type="button" class="btn btn-sm btn-success publish-entry"
                data-id="'.$row->id.'"
                data-exam-type="'.htmlspecialchars($examTypeName, ENT_QUOTES).'"
                data-class="'.htmlspecialchars($className, ENT_QUOTES).'"
                data-subject="'.htmlspecialchars($subjectName, ENT_QUOTES).'"
                data-results-count="'.$resultsCount.'"
                title="Publish">
                <i class="fas fa-paper-plane"></i></button>';
}
```

**User Experience**:
- Users without proper role never see the publish button
- Prevents confusion and unauthorized access attempts
- Clean UI that reflects user capabilities

### 6. Helper Function: `app/Helpers/common-helpers.php` (Lines 354-375)
**Purpose**: Enforce role restrictions globally through helper function

**Implementation**:
```php
function hasPermission($keyword)
{
    // Special handling for exam_entry_publish - only roles 1 and 2
    if ($keyword === 'exam_entry_publish') {
        if (Auth::check() && in_array(Auth::user()->role_id, [1, 2])) {
            return in_array($keyword, Auth::user()->permissions ?? []);
        }
        return false;
    }

    // Default permission check
    if (Auth::check() && Auth::user()->role_id == 1) {
        return true;
    }
    if (in_array($keyword, Auth::user()->permissions ?? [])) {
        return true;
    }
    return false;
}
```

**Benefits**:
- **Centralized authorization**: Single source of truth for permission checks
- **Consistent behavior**: All permission checks respect role restrictions
- **Maintainability**: Easy to update authorization logic in one place

### 7. Permission Seeder: `database/seeders/PermissionSeeder.php` (Line 55)
**Purpose**: Define `exam_entry` permission structure with publish action

**Addition**:
```php
'exam_entry' => [
    'read' => 'exam_entry_read',
    'create' => 'exam_entry_create',
    'update' => 'exam_entry_update',
    'delete' => 'exam_entry_delete',
    'publish' => 'exam_entry_publish'
],
```

**Result**: Creates `exam_entry_publish` permission keyword when seeding

### 8. Role Seeder: `database/seeders/RoleSeeder.php`
**Purpose**: Assign `exam_entry_publish` permission to Super Admin and Admin roles

**Super Admin (Lines 167-172)**:
```php
// exam_entry
'exam_entry_read',
'exam_entry_create',
'exam_entry_update',
'exam_entry_delete',
'exam_entry_publish',
```

**Admin (Lines 437-442)**:
```php
// exam_entry permissions
'exam_entry_read',
'exam_entry_create',
'exam_entry_update',
'exam_entry_delete',
'exam_entry_publish',
```

## Delete Authorization Implementation

### Overview
The delete authorization follows the exact same pattern as publish authorization, ensuring consistent security across critical exam entry operations.

### Files Modified for Delete Authorization

#### 1. Repository: `app/Repositories/Examination/ExamEntryRepository.php` (Lines 152-161)
**Purpose**: Hide delete button for unauthorized users

**Implementation**:
```php
// Delete button (only for users with role_id 1 or 2)
if (in_array(auth()->user()->role_id, [1, 2])) {
    $action .= '<button type="button" class="btn btn-sm btn-danger delete-entry"
                data-id="'.$row->id.'"
                data-exam-type="'.htmlspecialchars($examTypeName, ENT_QUOTES).'"
                data-class="'.htmlspecialchars($className, ENT_QUOTES).'"
                data-results-count="'.$resultsCount.'"
                title="Delete">
                <i class="fas fa-trash"></i></button>';
}
```

**User Experience**: Users without proper role never see the delete button

#### 2. Controller: `app/Http/Controllers/Backend/Examination/ExamEntryController.php` (Lines 360-403)
**Purpose**: Add explicit role validation to destroy() method

**Implementation**:
```php
public function destroy($id)
{
    // Check if user has required role (1 or 2) AND exam_entry_delete permission
    $userRoleId = auth()->user()->role_id;

    if (!in_array($userRoleId, [1, 2])) {
        return response()->json([
            'success' => false,
            'message' => 'Only Super Admin and Admin can delete exam entries'
        ], 403);
    }

    if (!hasPermission('exam_entry_delete')) {
        return response()->json([
            'success' => false,
            'message' => 'You do not have permission to delete exam entries'
        ], 403);
    }

    // Continue with deletion logic...
}
```

**Security Features**:
- Role validation first (must be 1 or 2)
- Permission validation second
- Clear error messages
- HTTP 403 Forbidden responses

#### 3. Middleware: `app/Http/Middleware/PermissionCheck.php` (Lines 31-40)
**Purpose**: Add special handling for `exam_entry_delete` permission

**Implementation**:
```php
// Special handling for exam_entry_delete permission - only roles 1 and 2
if ($permission === 'exam_entry_delete') {
    if (Auth::check() && in_array(Auth::user()->role_id, [1, 2])) {
        // Check if user also has the permission
        if (in_array($permission, Auth::user()->permissions)) {
            return $next($request);
        }
    }
    return abort(403, 'Only Super Admin and Admin can delete exam entries');
}
```

#### 4. Helper Function: `app/Helpers/common-helpers.php` (Lines 366-372)
**Purpose**: Enforce role restrictions globally

**Implementation**:
```php
// Special handling for exam_entry_delete - only roles 1 and 2
if ($keyword === 'exam_entry_delete') {
    if (Auth::check() && in_array(Auth::user()->role_id, [1, 2])) {
        return in_array($keyword, Auth::user()->permissions ?? []);
    }
    return false;
}
```

### Delete Authorization Benefits
- ✅ **Data Integrity**: Prevents accidental deletion by unauthorized users
- ✅ **Audit Trail**: Clear control over who can delete critical exam data
- ✅ **Consistent Security**: Same pattern as publish for maintainability
- ✅ **Defense-in-Depth**: Four layers of protection

## Deployment Instructions

### Step 1: Run Migration (Required for existing installations)
```bash
# For SaaS multi-tenant setup
php artisan module:migrate MainApp

# For single school installation
php artisan migrate --path=database/migrations/tenant
```

### Step 2: Clear Application Caches
```bash
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear
```

### Step 3: Verify Implementation (Optional)
```bash
# Check if permission exists in database
php artisan tinker
>>> App\Models\Permission::where('attribute', 'exam_entry')->first()->keywords

# Check if roles 1 and 2 have the permission
>>> App\Models\Role::find(1)->permissions
>>> App\Models\Role::find(2)->permissions
```

## Testing Checklist

### ✅ Role ID 1 (Super Admin) Testing

**Publish Operations**:
- [ ] Can view exam entry list
- [ ] Sees publish button for completed exam entries
- [ ] Can successfully publish exam entries
- [ ] Receives success message after publishing
- [ ] Published entries show "Published" status badge

**Delete Operations**:
- [ ] Sees delete button for all exam entries
- [ ] Can successfully delete exam entries
- [ ] Receives success message after deletion
- [ ] Entry removed from list after deletion

### ✅ Role ID 2 (Admin) Testing

**Publish Operations**:
- [ ] Can view exam entry list
- [ ] Sees publish button for completed exam entries
- [ ] Can successfully publish exam entries
- [ ] Receives success message after publishing
- [ ] Published entries show "Published" status badge

**Delete Operations**:
- [ ] Sees delete button for all exam entries
- [ ] Can successfully delete exam entries
- [ ] Receives success message after deletion
- [ ] Entry removed from list after deletion

### ❌ Role ID 3+ (Other Roles) Testing

**Publish Operations**:
- [ ] Can view exam entry list (if has read permission)
- [ ] Does NOT see publish button for completed exam entries
- [ ] Cannot make direct API call to publish endpoint (receives 403)
- [ ] Receives clear error message: "Only Super Admin and Admin can publish exam entries"

**Delete Operations**:
- [ ] Does NOT see delete button for any exam entries
- [ ] Cannot make direct API call to delete endpoint (receives 403)
- [ ] Receives clear error message: "Only Super Admin and Admin can delete exam entries"

### Security Testing - Publish
- [ ] Direct API POST/PUT to `/exam-entry/publish/{id}` is blocked for role_id > 2
- [ ] Middleware returns 403 Forbidden with appropriate message
- [ ] Controller validates both role AND permission
- [ ] Frontend never renders publish button for unauthorized users
- [ ] Helper function returns false for exam_entry_publish on role_id > 2

### Security Testing - Delete
- [ ] Direct API DELETE to `/exam-entry/delete/{id}` is blocked for role_id > 2
- [ ] Middleware returns 403 Forbidden with appropriate message
- [ ] Controller validates both role AND permission
- [ ] Frontend never renders delete button for unauthorized users
- [ ] Helper function returns false for exam_entry_delete on role_id > 2

## Error Handling

### 403 Forbidden Responses - Publish

**Middleware Level** (Route Protection):
```json
{
  "error": "Only Super Admin and Admin can publish exam entries"
}
```

**Controller Level** (Role Check):
```json
{
  "success": false,
  "message": "Only Super Admin and Admin can publish exam entries"
}
```

**Controller Level** (Permission Check):
```json
{
  "success": false,
  "message": "You do not have permission to publish exam entries"
}
```

### 403 Forbidden Responses - Delete

**Middleware Level** (Route Protection):
```json
{
  "error": "Only Super Admin and Admin can delete exam entries"
}
```

**Controller Level** (Role Check):
```json
{
  "success": false,
  "message": "Only Super Admin and Admin can delete exam entries"
}
```

**Controller Level** (Permission Check):
```json
{
  "success": false,
  "message": "You do not have permission to delete exam entries"
}
```

### 400 Bad Request Responses

**Invalid Status** (Publish):
```json
{
  "success": false,
  "message": "Only completed entries can be published"
}
```

**Business Logic Error** (Delete):
```json
{
  "success": false,
  "message": "[Specific deletion error from repository]"
}
```

### 500 Internal Server Error

**Exception Handling**:
```json
{
  "success": false,
  "message": "[Exception message]"
}
```

## Rollback Instructions

If issues arise, rollback using these steps:

### 1. Rollback Migration
```bash
# For SaaS
php artisan module:migrate:rollback MainApp

# For single school
php artisan migrate:rollback --path=database/migrations/tenant
```

### 2. Restore Original Files
```bash
# Restore routes file
git checkout routes/examination.php

# Restore controller
git checkout app/Http/Controllers/Backend/Examination/ExamEntryController.php

# Restore middleware
git checkout app/Http/Middleware/PermissionCheck.php

# Restore repository
git checkout app/Repositories/Examination/ExamEntryRepository.php

# Restore helper
git checkout app/Helpers/common-helpers.php

# Restore seeders
git checkout database/seeders/PermissionSeeder.php
git checkout database/seeders/RoleSeeder.php
```

### 3. Clear Caches
```bash
php artisan cache:clear
php artisan config:clear
php artisan route:clear
```

## Security Considerations

### Why Four Layers?

1. **Frontend (Repository)**: Prevents UI clutter and confusion
2. **Middleware (Route)**: Blocks unauthorized HTTP requests early
3. **Controller (Business Logic)**: Validates authorization before processing
4. **Helper (Global)**: Ensures consistent permission checks throughout application

### Defense-in-Depth Benefits

- **Multiple validation points**: Attacker must bypass all four layers
- **Clear error messages**: Each layer provides specific feedback
- **Maintainability**: Each layer has single responsibility
- **Testability**: Each layer can be tested independently

### Permission vs Role-Based Access

This implementation uses **hybrid authorization**:
- **Permission-based**: User must have `exam_entry_publish` permission
- **Role-based**: User must have role_id 1 or 2

**Why hybrid?**
- Permissions provide flexibility for future role additions
- Role checks provide strict control for critical actions
- Combined approach balances flexibility with security

## Future Enhancements

### Potential Improvements

1. **Audit Logging**
   - Add `published_by` field to `exam_entries` table
   - Track publish actions in audit log table
   - Record user IP, timestamp, and entry details

2. **Unpublish Functionality**
   - Allow admins to unpublish exam entries
   - Add unpublish button for published entries
   - Track unpublish actions with reason

3. **Granular Permissions**
   - Create separate `exam_entry_unpublish` permission
   - Add `exam_entry_bulk_publish` for batch operations
   - Implement exam type-specific publish permissions

4. **Notification System**
   - Notify students when results are published
   - Send email/SMS to parents
   - Push notifications via Firebase

5. **Approval Workflow**
   - Add review step before publishing
   - Require secondary approval for large result sets
   - Implement role-based approval chains

## Related Documentation

- **CLAUDE.md**: Project development guidelines
- **Tasks.md**: Implementation task tracking
- **README.md**: Project setup instructions

## Support & Troubleshooting

### Common Issues

**Issue**: Publish button not appearing for Admin users
**Solution**: Run migration and clear caches, verify role has exam_entry_publish permission

**Issue**: 403 error when Super Admin tries to publish
**Solution**: Check if permission exists in permissions array: `Role::find(1)->permissions`

**Issue**: Button visible but publish fails
**Solution**: Verify exam entry status is 'completed', not 'draft' or 'published'

### Debug Commands

```bash
# Check current user role and permissions
php artisan tinker
>>> auth()->user()->role_id
>>> auth()->user()->permissions

# Check exam entry status
>>> App\Models\Examination\ExamEntry::find(ID)->status

# Test hasPermission helper
>>> hasPermission('exam_entry_publish')
>>> hasPermission('exam_entry_delete')
```

## Conclusion

This implementation provides robust, multi-layered authorization for both **publish** and **delete** exam entry operations, ensuring only authorized Super Admin and Admin users can perform these critical actions. The defense-in-depth approach with four security layers provides:

- ✅ **Security**: Multi-layer protection against unauthorized access
- ✅ **Consistency**: Same pattern for both operations
- ✅ **Maintainability**: Clear, well-documented code structure
- ✅ **User Experience**: Clear feedback and intuitive access control
- ✅ **Data Integrity**: Protected exam entries from accidental or unauthorized modifications

Both publish and delete operations follow identical authorization patterns, making the system predictable, secure, and easy to maintain.

---

**Implementation Status**: ✅ Complete (Publish & Delete)
**Testing Status**: ⏳ Pending user testing
**Deployment Status**: ⏳ Pending migration execution
