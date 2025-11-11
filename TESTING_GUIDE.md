# Feature Access Control - Testing Guide

## Quick Test Instructions

### Prerequisites
```bash
# Clear all caches before testing
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear
```

### Test User Setup

#### 1. System Admin (school_id = NULL)
```sql
-- Find or create system admin
SELECT id, name, email, role_id, school_id
FROM users
WHERE school_id IS NULL
LIMIT 1;

-- If none exists, update an existing admin:
UPDATE users
SET school_id = NULL
WHERE id = [admin_user_id];
```

#### 2. School Admin (school_id != NULL, Package ID = 1)
```sql
-- Find school admin with limited package
SELECT u.id, u.name, u.email, u.role_id, u.school_id, s.package_id
FROM users u
JOIN schools s ON u.school_id = s.id
WHERE u.role_id = 1 AND s.package_id = 1
LIMIT 1;
```

---

## Test Scenarios

### TEST 1: System Admin Access (✅ PASS CRITERIA)
**Login as:** System admin (school_id = NULL)

**Expected Results:**
- ✅ Can see all sidebar menu items including:
  - Schools
  - Subscriptions
  - Packages
  - Feature Groups
  - Permission Features
- ✅ Can access `/school` route
- ✅ Can access `/package` route
- ✅ Can access `/feature-groups` route
- ✅ Dashboard shows all widgets
- ✅ Can access all routes without 403 errors

**Test Commands:**
```bash
# Login and navigate to these URLs:
/school/index
/package/index
/feature-groups/index
/permission-features/index
```

---

### TEST 2: School Admin Package Restrictions (✅ PASS CRITERIA)
**Login as:** School admin (role_id = 1, school_id = 1, package_id = 1)

**Expected Results:**
- ❌ Cannot see admin features in sidebar:
  - Schools (hidden)
  - Subscriptions (hidden)
  - Packages (hidden)
  - Feature Groups (hidden)
  - Permission Features (hidden)
- ❌ Accessing `/school` returns 403 or redirects
- ❌ Accessing `/package` returns 403 or redirects
- ✅ Dashboard shows only widgets for features in package
- ✅ Can access features in their package
- ❌ Cannot access features NOT in package

**Test Commands:**
```bash
# These should FAIL with 403:
/school/index
/package/index
/feature-groups/index

# These should WORK (if in package 1):
/homework/ (if Homework is in package)
/student/index (if Student Management is in package)
/attendance/ (if Attendance is in package)

# These should FAIL (not in package 1):
/library/books (Library not in package 1)
/online-examination/ (Online Exam not in package 1)
```

---

### TEST 3: Feature Middleware Protection (✅ PASS CRITERIA)
**Login as:** School admin WITHOUT homework feature

**Test:** Try to access `/homework`

**Expected:**
- ❌ 403 Forbidden OR redirect to dashboard
- ❌ "Feature Access Denied" message

**Test:** Try to access `/homework/create` directly

**Expected:**
- ❌ 403 Forbidden (FeatureAccessMiddleware blocks before PermissionCheck)

---

### TEST 4: Sidebar Dynamic Filtering (✅ PASS CRITERIA)
**Login as:** School admin with Package 1

**Expected Sidebar:**
```
✅ Dashboard (visible to all)
❌ Schools (hidden - admin only)
❌ Subscriptions (hidden - admin only)
❌ Packages (hidden - admin only)
❌ Feature Groups (hidden - admin only)
❌ Permission Features (hidden - admin only)
❌ Payment Reports (hidden - admin only)
❌ Languages (hidden - admin only)
❌ General Settings (hidden - admin only)
```

---

### TEST 5: Dashboard Widget Filtering (✅ PASS CRITERIA)
**Login as:** School admin with Package 1

**Check Dashboard Widgets:**
- ✅ Student counter (if student_management in package)
- ✅ Fees collection chart (if fees_management in package)
- ✅ Revenue chart (if accounts in package)
- ❌ Library widget (if library NOT in package)

---

### TEST 6: Direct URL Access (✅ PASS CRITERIA)
**Login as:** School admin WITHOUT library feature

**Test:**
```bash
# Try to access library routes directly
/library/books
/library/members
/library/issue-books
```

**Expected:**
- ❌ All requests return 403 Forbidden
- ❌ FeatureAccessMiddleware blocks access
- ❌ User redirected or sees error message

---

### TEST 7: Policy Authorization (✅ PASS CRITERIA)
**Login as:** School admin (role_id = 1, school_id = 1)

**Test:** Try to reject/delete cash transfer

**Expected:**
- ✅ Can reject if has `cash_transfer_reject` permission
- ❌ Cannot reject without permission (even with role_id = 1)
- ✅ System admin (school_id = NULL) can always reject

---

## Automated Testing Script

Create this test file: `tests/Feature/FeatureAccessTest.php`

```php
<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use Modules\MainApp\Entities\School;

class FeatureAccessTest extends TestCase
{
    /** @test */
    public function system_admin_can_access_all_features()
    {
        $systemAdmin = User::factory()->create([
            'role_id' => 1,
            'school_id' => null, // System admin
        ]);

        $response = $this->actingAs($systemAdmin)->get('/school');
        $response->assertStatus(200);

        $response = $this->actingAs($systemAdmin)->get('/package');
        $response->assertStatus(200);
    }

    /** @test */
    public function school_admin_cannot_access_admin_features()
    {
        $school = School::factory()->create(['package_id' => 1]);
        $schoolAdmin = User::factory()->create([
            'role_id' => 1,
            'school_id' => $school->id, // School admin
        ]);

        $response = $this->actingAs($schoolAdmin)->get('/school');
        $response->assertStatus(403);

        $response = $this->actingAs($schoolAdmin)->get('/package');
        $response->assertStatus(403);
    }

    /** @test */
    public function school_admin_restricted_by_package_features()
    {
        $school = School::factory()->create(['package_id' => 1]);
        $schoolAdmin = User::factory()->create([
            'role_id' => 1,
            'school_id' => $school->id,
        ]);

        // Assuming library is NOT in package 1
        $response = $this->actingAs($schoolAdmin)->get('/library/books');
        $response->assertStatus(403);
    }
}
```

Run tests:
```bash
php artisan test --filter=FeatureAccessTest
```

---

## Manual Testing Checklist

### School Admin Login Test
- [ ] Login as school admin (role_id=1, school_id=1)
- [ ] Check sidebar - admin features should be hidden
- [ ] Check dashboard - only package widgets visible
- [ ] Try accessing `/school` - should be blocked
- [ ] Try accessing `/package` - should be blocked
- [ ] Try accessing `/homework` - should work if in package
- [ ] Try accessing `/library` - should be blocked if not in package

### System Admin Login Test
- [ ] Login as system admin (school_id=NULL)
- [ ] Check sidebar - all features visible
- [ ] Check dashboard - all widgets visible
- [ ] Access `/school` - should work
- [ ] Access `/package` - should work
- [ ] Access any route - should work

### Feature Middleware Test
- [ ] School admin without "homework" feature
- [ ] Access `/homework` - should return 403
- [ ] Access `/homework/create` - should return 403
- [ ] Check error message - "Feature Access Denied"

---

## Troubleshooting

### Issue: School admin still sees all features
**Solution:**
1. Check `school_id` column: `SELECT school_id FROM users WHERE id = [user_id];`
2. If not NULL, check isSuperAdmin() helper returns false
3. Clear browser cache and Laravel cache
4. Check blade template uses `@if(isSuperAdmin())`

### Issue: 403 errors for everyone
**Solution:**
1. Check FeatureAccessMiddleware is registered in Kernel.php
2. Check package has assigned features
3. Check school has active subscription/package
4. Check feature cache: `Cache::get("school_features_{school_id}")`

### Issue: Dashboard widgets not filtering
**Solution:**
1. Check hasFeatureAccess() helper is used, not hasFeature()
2. Clear view cache: `php artisan view:clear`
3. Check feature names match permission_features.name column

---

## Success Metrics

### Security
✅ School admins cannot access admin features
✅ School admins cannot modify packages/features
✅ Feature restrictions enforced at multiple layers

### Functionality
✅ System admins retain full access
✅ Package features work correctly
✅ UI dynamically adapts to features

### Performance
✅ No N+1 queries introduced
✅ Feature checks cached properly
✅ Page load times unaffected

---

## Post-Deployment Monitoring

### Log Monitoring
```bash
# Watch for feature access denials
tail -f storage/logs/laravel.log | grep "Feature Access Attempt"

# Check for 403 errors
tail -f storage/logs/laravel.log | grep "403"
```

### Metrics to Track
- Number of 403 errors per day (should decrease)
- Feature access cache hit rate (should be >90%)
- User complaints about missing features (should be none for legitimate access)

---

## Report Template

```
# Feature Access Test Report

**Date:** [Date]
**Tester:** [Name]
**Environment:** [Staging/Production]

## Test Results

### System Admin Tests
- [ ] PASS: Can access all features
- [ ] PASS: Sidebar shows all menu items
- [ ] PASS: Dashboard shows all widgets

### School Admin Tests
- [ ] PASS: Cannot see admin features in sidebar
- [ ] PASS: Cannot access /school route
- [ ] PASS: Cannot access /package route
- [ ] PASS: Dashboard respects package restrictions
- [ ] PASS: Can access features in their package
- [ ] PASS: Cannot access features not in package

### Issues Found
[List any issues encountered]

### Notes
[Additional observations]

**Status:** ✅ PASS / ❌ FAIL
```
