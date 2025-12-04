# Implementation Plan: Super Admin & Staff Password Management

> **Status**: Planned
> **Created**: December 2024
> **Based on**: improvements.md requirements

---

## Table of Contents

1. [Overview](#overview)
2. [Improvement 1: Edit Super Admin from School Edit](#improvement-1-edit-super-admin-from-school-edit)
3. [Improvement 2: Staff Password Updates](#improvement-2-staff-password-updates)
4. [Security Considerations](#security-considerations)
5. [Language Translations](#language-translations)
6. [Implementation Order](#implementation-order)
7. [Testing Checklist](#testing-checklist)

---

## Overview

Two improvements for the School Management System to enhance admin user management:

| Improvement | Location | Feature |
|-------------|----------|---------|
| **1** | MainApp Dashboard | Edit Super Admin email/password from School Edit |
| **2** | School Dashboard | Role-based staff password updates |

### Role Hierarchy Reference

```
role_id=0: MAIN_SYSTEM_ADMIN (SaaS System Level)
role_id=1: SUPERADMIN (School Super Admin - all branches)
role_id=2: ADMIN (Branch Admin - own branch only)
role_id=3+: Staff, Teacher, Student, Guardian
```

---

## Improvement 1: Edit Super Admin from School Edit

### Summary

Allow system administrators to edit the Super Admin's email and password directly from the school edit page in the MainApp module.

### Files to Modify

| # | File | Purpose |
|---|------|---------|
| 1 | `Modules/MainApp/Http/Requests/School/UpdateRequest.php` | Add validation rules |
| 2 | `Modules/MainApp/Http/Controllers/SchoolController.php` | Pass Super Admin to view |
| 3 | `Modules/MainApp/Http/Repositories/SchoolRepository.php` | Add update logic |
| 4 | `Modules/MainApp/Resources/views/school/edit.blade.php` | Add form fields |

### Detailed Implementation

#### 1. UpdateRequest.php

**Location**: `Modules/MainApp/Http/Requests/School/UpdateRequest.php`

Add validation rules for Super Admin credentials:

```php
public function rules()
{
    // Get Super Admin ID for email uniqueness exclusion
    $superAdmin = \App\Models\User::where('school_id', Request()->id)
        ->where('role_id', \App\Enums\RoleEnum::SUPERADMIN)
        ->first();
    $superAdminId = $superAdmin ? $superAdmin->id : 0;

    return [
        'name'           => 'required|max:255|unique:schools,name,' . Request()->id,
        'status'         => 'required',
        // Super Admin fields (optional)
        'admin_email'    => 'nullable|email|max:255|unique:users,email,' . $superAdminId,
        'admin_password' => 'nullable|min:8|confirmed',
    ];
}

public function messages()
{
    return [
        'admin_email.unique' => 'This email is already in use by another user.',
        'admin_email.email' => 'Please enter a valid email address.',
        'admin_password.min' => 'Password must be at least 8 characters.',
        'admin_password.confirmed' => 'Password confirmation does not match.',
    ];
}
```

#### 2. SchoolController.php

**Location**: `Modules/MainApp/Http/Controllers/SchoolController.php`

Update the `edit()` method to pass Super Admin data:

```php
public function edit($id)
{
    $data['school']   = $this->repo->show($id);
    $data['title']    = ___('settings.Edit school');
    $data['packages'] = $this->packageRepo->all();

    // Get the Super Admin for this school
    $data['superAdmin'] = \App\Models\User::where('school_id', $id)
        ->where('role_id', \App\Enums\RoleEnum::SUPERADMIN)
        ->first();

    return view('mainapp::school.edit', compact('data'));
}
```

#### 3. SchoolRepository.php

**Location**: `Modules/MainApp/Http/Repositories/SchoolRepository.php`

Update the `update()` method and add helper method:

```php
public function update($request, $id)
{
    DB::beginTransaction();
    try {
        // Update school
        $row         = $this->model->findOrfail($id);
        $row->name   = $request->name;
        $row->status = $request->status;
        $row->save();

        // Update Super Admin credentials if provided
        $this->updateSuperAdmin($request, $id);

        DB::commit();
        return $this->responseWithSuccess(___('alert.updated_successfully'), []);
    } catch (\Throwable $th) {
        DB::rollback();
        \Log::error('School update error', [
            'school_id' => $id,
            'error' => $th->getMessage(),
            'trace' => $th->getTraceAsString()
        ]);
        return $this->responseWithError(___('alert.something_went_wrong_please_try_again'), []);
    }
}

/**
 * Update Super Admin email and/or password
 *
 * @param \Illuminate\Http\Request $request
 * @param int $schoolId
 * @return void
 */
protected function updateSuperAdmin($request, int $schoolId): void
{
    $superAdmin = User::where('school_id', $schoolId)
        ->where('role_id', \App\Enums\RoleEnum::SUPERADMIN)
        ->first();

    if (!$superAdmin) {
        \Log::warning('No Super Admin found for school', ['school_id' => $schoolId]);
        return;
    }

    $fieldsChanged = [];

    // Update email if provided and different
    if ($request->filled('admin_email') && $superAdmin->email !== $request->admin_email) {
        $superAdmin->email = $request->admin_email;
        $superAdmin->username = $request->admin_email; // Keep username in sync
        $fieldsChanged[] = 'email';
    }

    // Update password if provided
    if ($request->filled('admin_password')) {
        $superAdmin->password = Hash::make($request->admin_password);
        $fieldsChanged[] = 'password';
    }

    if (!empty($fieldsChanged)) {
        $superAdmin->save();

        // Audit logging
        \Log::info('AUDIT: Super Admin credentials updated', [
            'school_id' => $schoolId,
            'super_admin_id' => $superAdmin->id,
            'updated_by' => auth()->id() ?? 'system',
            'fields_changed' => $fieldsChanged,
            'ip_address' => request()->ip(),
            'timestamp' => now()->toIso8601String()
        ]);
    }
}
```

#### 4. edit.blade.php

**Location**: `Modules/MainApp/Resources/views/school/edit.blade.php`

Add Super Admin section after the address field (around line 141):

```blade
{{-- Super Admin Credentials Section --}}
@if(isset($data['superAdmin']))
<div class="col-md-12 mb-3">
    <hr>
    <h5 class="text-primary mb-3">
        <i class="fa-solid fa-user-shield"></i>
        {{ ___('mainapp_common.Super Admin Credentials') }}
    </h5>
</div>

<div class="col-md-6 mb-3">
    <label for="admin_email" class="form-label">
        {{ ___('mainapp_common.Super Admin Email') }}
    </label>
    <input type="email"
           class="form-control ot-input @error('admin_email') is-invalid @enderror"
           name="admin_email"
           id="admin_email"
           placeholder="{{ ___('mainapp_common.Enter super admin email') }}"
           value="{{ old('admin_email', @$data['superAdmin']->email) }}">
    @error('admin_email')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
    <small class="text-muted">
        {{ ___('mainapp_common.Leave blank to keep current email') }}
    </small>
</div>

<div class="col-md-6 mb-3">
    <label for="admin_password" class="form-label">
        {{ ___('mainapp_common.New Password') }}
    </label>
    <input type="password"
           class="form-control ot-input @error('admin_password') is-invalid @enderror"
           name="admin_password"
           id="admin_password"
           placeholder="{{ ___('mainapp_common.Enter new password') }}">
    @error('admin_password')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
    <small class="text-muted">
        {{ ___('mainapp_common.Leave blank to keep current password') }}
    </small>
</div>

<div class="col-md-6 mb-3">
    <label for="admin_password_confirmation" class="form-label">
        {{ ___('mainapp_common.Confirm Password') }}
    </label>
    <input type="password"
           class="form-control ot-input"
           name="admin_password_confirmation"
           id="admin_password_confirmation"
           placeholder="{{ ___('mainapp_common.Confirm new password') }}">
</div>
@endif
```

---

## Improvement 2: Staff Password Updates

### Summary

Enable role-based staff password management:
- **Super Admins (role_id=1)**: Can edit ANY staff password in their school
- **Branch Admins (role_id=2)**: Can edit ONLY their branch's staff passwords

### Files to Modify

| # | File | Purpose |
|---|------|---------|
| 1 | `app/Http/Requests/User/UserUpdateRequest.php` | Add password validation |
| 2 | `app/Http/Controllers/Backend/UserController.php` | Add authorization logic |
| 3 | `app/Repositories/UserRepository.php` | Add password handling |
| 4 | `resources/views/backend/users/edit.blade.php` | Add password fields |

### Detailed Implementation

#### 1. UserUpdateRequest.php

**Location**: `app/Http/Requests/User/UserUpdateRequest.php`

Add password validation rules:

```php
public function rules()
{
    return [
        'role'         => 'required',
        'designation'  => 'required',
        'department'   => 'required',
        'first_name'   => 'required|max:25',
        'email'        => 'required|unique:users,email,' . Request()->user_id,
        'gender'       => 'required',
        'dob'          => 'nullable|date',
        'phone'        => 'nullable|regex:/^([0-9\s\-\+\(\)]*)$/|max:11',
        'status'       => 'required',
        'image'        => 'max:2048',
        // Password fields - optional
        'password'     => 'nullable|min:8|confirmed',
    ];
}

public function messages()
{
    return [
        'password.min' => 'Password must be at least 8 characters.',
        'password.confirmed' => 'Password confirmation does not match.',
    ];
}
```

#### 2. UserController.php

**Location**: `app/Http/Controllers/Backend/UserController.php`

Add authorization method and update edit/update methods:

```php
/**
 * Check if current user can edit the target staff member's password
 *
 * Authorization Rules:
 * - Super Admin (role_id=1): Can edit ANY staff password in their school
 * - Branch Admin (role_id=2): Can edit ONLY their branch's staff passwords
 *
 * @param \App\Models\Staff\Staff $staff
 * @return bool
 */
protected function canEditStaffPassword($staff): bool
{
    $currentUser = auth()->user();

    if (!$currentUser) {
        return false;
    }

    // Get the staff's user record
    $staffUser = \App\Models\User::find($staff->user_id);

    if (!$staffUser) {
        return false;
    }

    // Super Admin (role_id = 1) - can edit any staff in their school
    if ($currentUser->role_id === \App\Enums\RoleEnum::SUPERADMIN) {
        return $currentUser->school_id === $staffUser->school_id;
    }

    // Branch Admin (role_id = 2) - only their branch
    if ($currentUser->role_id === \App\Enums\RoleEnum::ADMIN) {
        return $currentUser->school_id === $staffUser->school_id
            && $currentUser->branch_id === $staffUser->branch_id;
    }

    return false;
}

public function edit($id)
{
    $data['user']          = $this->user->show($id);
    $data['title']         = ___('staff.update_staff');
    $data['permissions']   = $this->permission->all();
    $data['roles']         = $this->role->all();
    $data['designations']  = $this->designation->all();
    $data['departments']   = $this->department->all();
    $data['genders']       = $this->gender->all();

    // Determine if current user can edit this staff's password
    $data['canEditPassword'] = $this->canEditStaffPassword($data['user']);

    return view('backend.users.edit', compact('data'));
}

public function update(UserUpdateRequest $request, $id)
{
    // Authorization check for password update
    if ($request->filled('password')) {
        $staff = $this->user->show($id);
        if (!$this->canEditStaffPassword($staff)) {
            return redirect()->route('users.index')
                ->with('danger', ___('alert.unauthorized_password_update'));
        }
    }

    $result = $this->user->update($request, $id);
    if ($result) {
        return redirect()->route('users.index')
            ->with('success', ___('alert.user_updated_successfully'));
    }
    return redirect()->route('users.index')
        ->with('danger', ___('alert.something_went_wrong_please_try_again'));
}
```

#### 3. UserRepository.php

**Location**: `app/Repositories/UserRepository.php`

Add password handling to the `update()` method (inside the try block, after existing user field updates):

```php
// Handle password update if provided
if ($request->filled('password')) {
    $user->password = Hash::make($request->password);

    // Audit logging for password change
    \Log::info('AUDIT: Staff password updated', [
        'staff_id' => $staff->id,
        'user_id' => $user->id,
        'updated_by' => auth()->id(),
        'updater_role' => auth()->user()->role_id,
        'updater_branch_id' => auth()->user()->branch_id,
        'staff_branch_id' => $user->branch_id,
        'ip_address' => request()->ip(),
        'timestamp' => now()->toIso8601String()
    ]);
}
```

#### 4. edit.blade.php

**Location**: `resources/views/backend/users/edit.blade.php`

Add password section (conditionally displayed based on authorization):

```blade
{{-- Password Update Section - Only shown if user has permission --}}
@if(isset($data['canEditPassword']) && $data['canEditPassword'])
<div class="col-lg-12 mb-3 mt-4">
    <hr>
    <h5 class="text-primary mb-3">
        <i class="fa-solid fa-key"></i>
        {{ ___('staff.password_update') }}
    </h5>
    <p class="text-muted small">
        {{ ___('staff.password_update_hint') }}
    </p>
</div>

<div class="col-lg-6 col-md-6 mb-3">
    <label for="password" class="form-label">
        {{ ___('staff.new_password') }}
    </label>
    <div class="input-group">
        <input type="password"
               class="form-control ot-input @error('password') is-invalid @enderror"
               name="password"
               id="password"
               placeholder="{{ ___('staff.enter_new_password') }}"
               autocomplete="new-password">
        <button class="btn btn-outline-secondary" type="button" onclick="togglePasswordVisibility('password')">
            <i class="fa-solid fa-eye" id="password-toggle-icon"></i>
        </button>
    </div>
    @error('password')
        <div class="invalid-feedback d-block">{{ $message }}</div>
    @enderror
    <small class="text-muted">
        {{ ___('staff.leave_blank_to_keep_current') }}
    </small>
</div>

<div class="col-lg-6 col-md-6 mb-3">
    <label for="password_confirmation" class="form-label">
        {{ ___('staff.confirm_password') }}
    </label>
    <div class="input-group">
        <input type="password"
               class="form-control ot-input"
               name="password_confirmation"
               id="password_confirmation"
               placeholder="{{ ___('staff.confirm_new_password') }}"
               autocomplete="new-password">
        <button class="btn btn-outline-secondary" type="button" onclick="togglePasswordVisibility('password_confirmation')">
            <i class="fa-solid fa-eye" id="password_confirmation-toggle-icon"></i>
        </button>
    </div>
</div>
@endif

@push('scripts')
<script>
function togglePasswordVisibility(fieldId) {
    const field = document.getElementById(fieldId);
    const icon = document.getElementById(fieldId + '-toggle-icon');

    if (field.type === 'password') {
        field.type = 'text';
        icon.classList.remove('fa-eye');
        icon.classList.add('fa-eye-slash');
    } else {
        field.type = 'password';
        icon.classList.remove('fa-eye-slash');
        icon.classList.add('fa-eye');
    }
}
</script>
@endpush
```

---

## Security Considerations

### Password Security
- All passwords hashed using `Hash::make()` (bcrypt)
- Minimum 8 character requirement
- Password confirmation required
- Passwords NEVER logged in plain text

### Authorization
- **Improvement 1**: Only system administrators (MainApp users) can access school edit
- **Improvement 2**: Double authorization check:
  - Permission middleware (`PermissionCheck:user_update`)
  - Role-based branch access control in controller

### Audit Logging
All credential changes logged with:
- Timestamp (ISO 8601 format)
- User who made the change
- Target user/staff ID
- IP address
- Fields changed (never actual password values)

### Data Integrity
- Database transactions used for atomicity
- Validation before any updates
- Rollback on failure

---

## Language Translations

### MainApp Module

**File**: `Modules/MainApp/Resources/lang/en/mainapp_common.php`

```php
'Super Admin Credentials' => 'Super Admin Credentials',
'Super Admin Email' => 'Super Admin Email',
'Enter super admin email' => 'Enter super admin email',
'Leave blank to keep current email' => 'Leave blank to keep current email',
'New Password' => 'New Password',
'Enter new password' => 'Enter new password',
'Leave blank to keep current password' => 'Leave blank to keep current password',
'Confirm Password' => 'Confirm Password',
'Confirm new password' => 'Confirm new password',
```

### Staff Module

**File**: `resources/lang/en/staff.php`

```php
'password_update' => 'Password Update',
'password_update_hint' => 'Leave fields blank to keep current password.',
'new_password' => 'New Password',
'enter_new_password' => 'Enter new password',
'leave_blank_to_keep_current' => 'Leave blank to keep current password',
'confirm_password' => 'Confirm Password',
'confirm_new_password' => 'Confirm new password',
```

### Alerts

**File**: `resources/lang/en/alert.php`

```php
'unauthorized_password_update' => 'You are not authorized to update this user\'s password.',
```

---

## Implementation Order

### Phase 1: Improvement 1 (Super Admin Edit)

1. [ ] Update `UpdateRequest.php` - Add validation rules
2. [ ] Update `SchoolController::edit()` - Pass Super Admin to view
3. [ ] Update `SchoolRepository::update()` - Add Super Admin update logic
4. [ ] Update `edit.blade.php` - Add form fields
5. [ ] Add MainApp translations
6. [ ] Manual testing

### Phase 2: Improvement 2 (Staff Password Update)

1. [ ] Update `UserUpdateRequest.php` - Add password validation
2. [ ] Add `canEditStaffPassword()` to `UserController`
3. [ ] Update `UserController::edit()` - Pass canEditPassword flag
4. [ ] Update `UserController::update()` - Add authorization check
5. [ ] Update `UserRepository::update()` - Add password handling
6. [ ] Update `users/edit.blade.php` - Add password fields
7. [ ] Add staff translations
8. [ ] Manual testing

### Phase 3: Quality Assurance

1. [ ] Run existing tests (no regressions)
2. [ ] Test Super Admin as different roles
3. [ ] Test Branch Admin branch restrictions
4. [ ] Verify audit logs are created
5. [ ] Test validation error messages
6. [ ] Security review

---

## Testing Checklist

### Improvement 1: Super Admin Edit

| Test | Expected Result |
|------|-----------------|
| Edit school without changing Super Admin | School updates, Super Admin unchanged |
| Update Super Admin email only | Email updated, password unchanged |
| Update Super Admin password only | Password updated, email unchanged |
| Update both email and password | Both updated |
| Use duplicate email | Validation error |
| Use short password (<8 chars) | Validation error |
| Password mismatch | Validation error |
| Check audit log | Log entry with changed fields |

### Improvement 2: Staff Password Update

| Test | Expected Result |
|------|-----------------|
| Super Admin edits staff in same school | Password field visible, update works |
| Super Admin edits staff in different school | Should not be possible (school isolation) |
| Branch Admin edits staff in same branch | Password field visible, update works |
| Branch Admin edits staff in different branch | Password field NOT visible |
| Regular staff tries to edit | Password field NOT visible |
| Password too short | Validation error |
| Password mismatch | Validation error |
| Check audit log | Log entry with staff/updater info |

---

## Critical File Paths Reference

```
# Improvement 1 - MainApp Module
Modules/MainApp/Http/Requests/School/UpdateRequest.php
Modules/MainApp/Http/Controllers/SchoolController.php
Modules/MainApp/Http/Repositories/SchoolRepository.php
Modules/MainApp/Resources/views/school/edit.blade.php
Modules/MainApp/Resources/lang/en/mainapp_common.php

# Improvement 2 - School Dashboard
app/Http/Requests/User/UserUpdateRequest.php
app/Http/Controllers/Backend/UserController.php
app/Repositories/UserRepository.php
resources/views/backend/users/edit.blade.php
resources/lang/en/staff.php
resources/lang/en/alert.php

# Reference Files
app/Enums/RoleEnum.php
app/Models/User.php
app/Models/Staff/Staff.php
```

---

## Notes

- The existing `UserRepository::passwordUpdate()` method is for self-service password changes
- This implementation adds admin-initiated password changes to the staff edit workflow
- Multi-tenancy is preserved through `school_id` checks
- Branch isolation is enforced for Branch Admins (role_id=2)
