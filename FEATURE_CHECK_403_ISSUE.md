# Feature Check 403 Forbidden Issue Documentation

## Issue Summary

**Problem:** Staff and Roles routes return 403 Forbidden errors when accessed by School Super Admin (role_id=1, school_id=1), while other routes like Cash Transfer and Exam Entry work correctly.

**Environment:**
- PHP: 8.3.8
- Laravel: 12.12.0
- APP_SAAS: false (in .env)
- Multi-tenant architecture: school_id-based (single database)

**Date:** 2025-01-11

---

## Observed Behavior

### Working Routes ✅
- Cash Transfer (`/cash-transfers`)
- Exam Entry (`/examination`)
- Most other application routes

### Failing Routes ❌
- Staff Management (`/users`)
- Roles Management (`/roles`)

**Error:** 403 Forbidden - "Access Denied - Feature not available in your package"

---

## Root Cause Analysis

### 1. Route Middleware Difference

**Working Routes (Cash Transfer):**
```php
// routes/accounts.php
Route::controller(CashTransferController::class)
    ->prefix('cash-transfers')
    ->group(function () {
        Route::get('/', 'index')
            ->middleware('PermissionCheck:cash_transfer_read'); // Only PermissionCheck
    });
```

**Failing Routes (Staff/Roles):**
```php
// routes/web.php
Route::controller(RoleController::class)
    ->middleware('FeatureCheck:staff_manage') // ← Additional FeatureCheck middleware
    ->prefix('roles')
    ->group(function () {
        Route::get('/', 'index')
            ->middleware('PermissionCheck:role_read'); // Also has PermissionCheck
    });
```

**Key Difference:** Staff/Roles routes have `FeatureCheck:staff_manage` middleware that other routes don't have.

---

### 2. FeatureCheck Middleware Behavior

**File:** `app/Http/Middleware/FeatureCheck.php`

**Current Code (Line 35):**
```php
public function handle(Request $request, Closure $next, $feature)
{
    // Bypass feature checks for single-school installations
    if (!env('APP_SAAS', false)) {
        return $next($request);
    }

    // Require authentication
    if (!Auth::check()) {
        return abort(403, 'Authentication required');
    }

    // Translate OLD feature key to NEW permission attributes
    $newFeatures = $this->featureMap[$feature] ?? [$feature];

    // Check if user has ANY of the mapped features in their package
    if (hasAnyFeature($newFeatures)) {
        return $next($request);
    }

    return abort(403, 'Access Denied - Feature not available in your package');
}
```

**Feature Mapping:**
```php
private $featureMap = [
    'staff_manage' => ['users', 'roles', 'department', 'designation'],
];
```

---

### 3. The Call Chain

When accessing `/roles`:

```
1. Request → FeatureCheck:staff_manage middleware
2. Check: if (!env('APP_SAAS', false))
   → APP_SAAS=false in .env
   → Should bypass and return $next($request) ✅
3. BUT STILL getting 403 ❌
```

---

### 4. Investigation Steps Taken

#### Attempt 1: Fix ENV Type Coercion
**Theory:** `env('APP_SAAS')` returns STRING "false" instead of boolean

**Fix Applied:**
```php
// Changed from:
if (!env('APP_SAAS')) {

// To:
if (!env('APP_SAAS', false)) {  // Add boolean default
```

**Result:** Still 403 ❌

---

#### Attempt 2: Remove APP_SAAS Dependency
**Theory:** APP_SAAS is legacy and should be removed entirely

**Changes Made:**
1. Removed APP_SAAS check from `FeatureCheck.php` (lines 34-37)
2. Removed APP_SAAS check from `FeatureAccessService.php` (lines 53-56)

**Result:** TypeError - `School::getAllowedFeatures()` returns array instead of Collection

**Issue Found:** System started using subscription-based features, exposed type mismatch bug:
```php
// Modules/MainApp/Entities/School.php:77
return $this->package->getAllowedPermissions(); // Returns array
// But method signature declares: Collection
```

**Attempted Fix:**
```php
return collect($this->package->getAllowedPermissions());
```

**Result:** Still TypeError due to 24-hour cache containing old array value

**Reverted:** All changes reverted back to original state with APP_SAAS checks

---

## System Architecture

### Multi-Tenancy Model
```
Single Database with school_id Column Isolation
├── Users have school_id (except System Admins: school_id=NULL)
├── SchoolContext middleware establishes school context
├── SchoolScope global scopes filter queries by school_id
└── Subscriptions linked to schools via school_id
```

### Feature Access Control Layers

**Layer 1: Role Permissions** (PermissionCheck middleware)
```
User → Role → Role Permissions → Permission Keywords
Example: user.role_id=1 → roles table → role_permissions → 'role_read'
```

**Layer 2: Package Features** (FeatureCheck middleware)
```
User → School → Subscription → Package → Package Features
Example: user.school_id=1 → subscriptions → package_id → package_permission_features
```

### Sidebar vs Routes

**Sidebar Check:**
```php
@if (
    (hasPermission('role_read') || hasPermission('user_read') || ...) &&
    hasAnyFeature(['users', 'roles', 'department', 'designation'])
)
```

**Route Check:**
```php
->middleware('FeatureCheck:staff_manage')  // Maps to ['users', 'roles', 'department', 'designation']
->middleware('PermissionCheck:role_read')
```

**Both should behave identically with APP_SAAS=false**, but routes fail while sidebar works.

---

## Current State

### What Works
- ✅ Sidebar displays "Staff Management" menu correctly
- ✅ hasAnyFeature(['users', 'roles'...]) returns true in sidebar
- ✅ APP_SAAS=false bypasses feature checks in FeatureAccessService
- ✅ Cash Transfer and other routes without FeatureCheck middleware work
- ✅ Role permissions (PermissionCheck) work correctly

### What Doesn't Work
- ❌ Accessing `/users` or `/roles` returns 403 Forbidden
- ❌ FeatureCheck middleware's APP_SAAS bypass not working
- ❌ Despite identical env() call with boolean default

---

## Potential Root Causes (Unresolved)

### Theory 1: Cache Inconsistency
- Config cache might have old APP_SAAS value
- Route cache might be stale
- **Note:** Cleared multiple times with `php artisan cache:clear`, `route:clear`, `config:clear`

### Theory 2: Middleware Execution Order
- FeatureCheck might execute before SchoolContext
- school_id context might not be established
- **Note:** SchoolContext is in global middleware, should execute first

### Theory 3: Hidden Logic Path
- hasAnyFeature() might have different code path in middleware context
- Package relationship might be null for some reason
- Subscription might not be loaded correctly

### Theory 4: Database State
- School might not have active subscription
- Package might not include 'users', 'roles', 'department', 'designation' features
- **Need to verify:**
  ```sql
  SELECT * FROM subscriptions WHERE school_id=1 AND status='active';

  SELECT ppf.*, pf.name, p.attribute
  FROM package_permission_features ppf
  JOIN permission_features pf ON ppf.permission_feature_id = pf.id
  JOIN permissions p ON pf.permission_id = p.id
  WHERE ppf.package_id = (SELECT package_id FROM subscriptions WHERE school_id=1 LIMIT 1);
  ```

---

## Recommended Next Steps

### 1. Database Verification
Check if school has proper subscription and package features:
```bash
php artisan tinker
>>> $school = \Modules\MainApp\Entities\School::find(1);
>>> $school->subscription;  // Should have active subscription
>>> $school->package;  // Should have package
>>> $school->package->getAllowedPermissions();  // Should include 'users', 'roles'
```

### 2. Add Debug Logging
Temporarily add logging to FeatureCheck middleware:
```php
public function handle(Request $request, Closure $next, $feature)
{
    \Log::info('FeatureCheck START', [
        'feature' => $feature,
        'app_saas' => env('APP_SAAS'),
        'app_saas_with_default' => env('APP_SAAS', false),
        'bypass_condition' => !env('APP_SAAS', false),
    ]);

    if (!env('APP_SAAS', false)) {
        \Log::info('FeatureCheck BYPASSED');
        return $next($request);
    }

    \Log::info('FeatureCheck CHECKING', [
        'user_id' => auth()->id(),
        'mapped_features' => $this->featureMap[$feature] ?? [$feature],
    ]);

    // ... rest of code
}
```

### 3. Compare Helper vs Middleware
Check if hasAnyFeature() behaves differently in middleware context:
```php
// In FeatureCheck middleware, before checking:
\Log::info('hasAnyFeature test', [
    'result' => hasAnyFeature(['users', 'roles', 'department', 'designation']),
    'context' => 'middleware',
]);
```

### 4. Alternative Solution: Remove FeatureCheck
Since APP_SAAS=false should bypass all feature checking:
```php
// Option A: Remove middleware from routes
Route::controller(RoleController::class)
    // ->middleware('FeatureCheck:staff_manage')  // Comment out
    ->prefix('roles')
    ->group(function () {
        // ...
    });

// Option B: Make FeatureCheck always bypass
public function handle(Request $request, Closure $next, $feature)
{
    // Temporary bypass for debugging
    return $next($request);
}
```

---

## Files Involved

### Modified During Investigation
- `app/Http/Middleware/FeatureCheck.php` (line 35 - env() call)
- `app/Services/FeatureAccessService.php` (attempted removal of APP_SAAS check - reverted)
- `Modules/MainApp/Entities/School.php` (attempted Collection type fix - reverted)

### Related Files
- `routes/web.php` (Staff/Roles route definitions)
- `routes/accounts.php` (Cash Transfer route definitions - working example)
- `app/Helpers/feature-helpers.php` (hasAnyFeature, hasFeatureAccess functions)
- `resources/views/backend/partials/sidebar.blade.php` (line 850 - working sidebar check)

### Configuration
- `.env` (APP_SAAS=false)
- Database tables:
  - `subscriptions` (school_id, package_id, status)
  - `packages` (id, name)
  - `package_permission_features` (package_id, permission_feature_id)
  - `permission_features` (id, permission_id, name)
  - `permissions` (id, attribute, keywords)

---

## Workaround (Temporary)

Until the root cause is identified, remove FeatureCheck middleware from Staff/Roles routes:

```php
// routes/web.php

// BEFORE:
Route::controller(RoleController::class)
    ->middleware('FeatureCheck:staff_manage')
    ->prefix('roles')
    ->group(function () {
        // ...
    });

// AFTER:
Route::controller(RoleController::class)
    // ->middleware('FeatureCheck:staff_manage')  // Temporarily disabled
    ->prefix('roles')
    ->group(function () {
        // ...
    });
```

This will make Staff/Roles work like Cash Transfer (permission-only checking).

---

## Additional Notes

- Multi-tenancy works correctly via school_id column isolation
- SchoolContext middleware properly establishes school context
- Permission-based access control (PermissionCheck) works correctly
- Only package-based feature checking (FeatureCheck) fails for Staff/Roles
- Issue appears to be specific to FeatureCheck middleware execution
- Sidebar uses same hasAnyFeature() function but works correctly
- May be related to middleware execution order or context availability

---

## Contact for Follow-up

This issue requires deeper investigation into:
1. Exact middleware execution order and context availability
2. Database state verification (subscription and package features)
3. Potential framework-level caching or config loading issues
4. Possible differences in how env() behaves in middleware vs helper context

**Last Updated:** 2025-01-11
**Status:** Unresolved - Documented for future investigation
