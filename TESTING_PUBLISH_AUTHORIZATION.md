# Quick Testing Guide: Exam Entry Publish Authorization

## Prerequisites

Before testing, ensure you have:
- ✅ Run the migration: `php artisan migrate --path=database/migrations/tenant`
- ✅ Cleared caches: `php artisan cache:clear && php artisan config:clear && php artisan route:clear`
- ✅ Test users with role_id 1, 2, and 3+ available

## Test Scenarios

### Scenario 1: Super Admin (role_id = 1) ✅

**Login as**: User with `role_id = 1` (Super Admin)

**Test Steps**:
1. Navigate to Exam Entry management page (`/exam-entry`)
2. Look for exam entries with status "Completed"
3. **Expected**: See green "Publish" button (paper plane icon)
4. Click the publish button
5. **Expected**: See SweetAlert2 confirmation dialog with exam details
6. Click "Yes, Publish"
7. **Expected**: Success message "Results published successfully"
8. **Expected**: Entry status changes to "Published"
9. **Expected**: Publish button disappears (entry now published)

**Result**: ✅ PASS if all steps succeed

---

### Scenario 2: Admin (role_id = 2) ✅

**Login as**: User with `role_id = 2` (Admin)

**Test Steps**:
1. Navigate to Exam Entry management page (`/exam-entry`)
2. Look for exam entries with status "Completed"
3. **Expected**: See green "Publish" button (paper plane icon)
4. Click the publish button
5. **Expected**: See SweetAlert2 confirmation dialog with exam details
6. Click "Yes, Publish"
7. **Expected**: Success message "Results published successfully"
8. **Expected**: Entry status changes to "Published"
9. **Expected**: Publish button disappears (entry now published)

**Result**: ✅ PASS if all steps succeed

---

### Scenario 3: Staff/Teacher (role_id ≥ 3) ❌

**Login as**: User with `role_id = 3` or higher (Staff, Teacher, etc.)

**Test Steps**:
1. Navigate to Exam Entry management page (`/exam-entry`)
   - *If user lacks `exam_entry_read` permission, they won't see this page*
2. Look for exam entries with status "Completed"
3. **Expected**: NO publish button visible
4. Attempt direct API call (using browser console or Postman):
   ```javascript
   fetch('/exam-entry/publish/1', {
       method: 'PUT',
       headers: {
           'Content-Type': 'application/json',
           'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
       }
   })
   .then(res => res.json())
   .then(data => console.log(data));
   ```
5. **Expected**: HTTP 403 Forbidden response
6. **Expected**: Error message: "Only Super Admin and Admin can publish exam entries"

**Result**: ✅ PASS if publish button is hidden and API call is blocked

---

## Security Testing

### Test 1: Direct API Access (Unauthorized Role)

**Setup**: Login as user with role_id ≥ 3

**Test**:
```bash
# Using cURL
curl -X PUT http://your-domain.test/exam-entry/publish/1 \
  -H "Content-Type: application/json" \
  -H "Cookie: your-session-cookie" \
  -d '{"_token":"your-csrf-token"}'
```

**Expected Response**:
```json
{
  "error": "Only Super Admin and Admin can publish exam entries"
}
```

**Result**: ✅ PASS if 403 Forbidden with correct message

---

### Test 2: Permission Manipulation Attempt

**Setup**: Login as user with role_id ≥ 3, manually add permission

**Test**:
```php
// In tinker
$user = Auth::user();
$permissions = $user->role->permissions;
$permissions[] = 'exam_entry_publish';
$user->role->permissions = $permissions;
$user->role->save();
```

**Then try to publish**:
- Navigate to exam entry page
- **Expected**: Still NO publish button (frontend check blocks)
- Try direct API call
- **Expected**: Still 403 Forbidden (controller checks role_id)

**Result**: ✅ PASS if blocked despite having permission

---

### Test 3: Middleware Bypass Attempt

**Setup**: Try to bypass middleware using direct controller access

**Test**: Not applicable (middleware is enforced by Laravel routing)

**Expected**: Middleware always executes before controller

**Result**: ✅ PASS (architectural enforcement)

---

## Automated Testing Commands

### Check Permission Assignment

```bash
php artisan tinker
```

```php
// Check if exam_entry permission exists
Permission::where('attribute', 'exam_entry')->first()->keywords

// Check Super Admin permissions
Role::find(1)->permissions

// Check Admin permissions
Role::find(2)->permissions

// Check if specific user can publish
$user = User::find(YOUR_USER_ID);
echo "Role ID: " . $user->role_id . "\n";
echo "Has Permission: " . (hasPermission('exam_entry_publish') ? 'YES' : 'NO') . "\n";
```

### Test Helper Function

```bash
php artisan tinker
```

```php
// Test with different roles
Auth::loginUsingId(1); // Super Admin
echo hasPermission('exam_entry_publish') ? 'ALLOWED' : 'DENIED';

Auth::loginUsingId(2); // Admin
echo hasPermission('exam_entry_publish') ? 'ALLOWED' : 'DENIED';

Auth::loginUsingId(3); // Staff
echo hasPermission('exam_entry_publish') ? 'ALLOWED' : 'DENIED';
```

---

## Visual Testing Checklist

### Frontend UI Checks

| User Role | Status Badge | View Button | Edit Button | Publish Button | Delete Button |
|-----------|-------------|-------------|-------------|----------------|---------------|
| Super Admin (1) - Draft | Warning (Yellow) | ✅ | ✅ | ❌ | ✅ |
| Super Admin (1) - Completed | Info (Blue) | ✅ | ✅ | ✅ | ✅ |
| Super Admin (1) - Published | Success (Green) | ✅ | ❌ | ❌ | ✅ |
| Admin (2) - Draft | Warning (Yellow) | ✅ | ✅ | ❌ | ✅ |
| Admin (2) - Completed | Info (Blue) | ✅ | ✅ | ✅ | ✅ |
| Admin (2) - Published | Success (Green) | ✅ | ❌ | ❌ | ✅ |
| Staff (3+) - Draft | Warning (Yellow) | ✅ | ✅ | ❌ | ✅ |
| Staff (3+) - Completed | Info (Blue) | ✅ | ✅ | ❌ | ✅ |
| Staff (3+) - Published | Success (Green) | ✅ | ❌ | ❌ | ✅ |

---

## Edge Cases to Test

### Edge Case 1: Published Entry

**Test**: Try to publish an already published entry
**Expected**: Publish button not visible
**Result**: ✅ PASS

### Edge Case 2: Draft Entry

**Test**: Try to publish a draft entry
**Expected**: Publish button not visible (only for completed)
**Result**: ✅ PASS

### Edge Case 3: Deleted Entry

**Test**: Try to access deleted exam entry
**Expected**: 404 Not Found
**Result**: ✅ PASS

### Edge Case 4: Non-existent Entry

**Test**: Try to publish non-existent entry ID
```bash
curl -X PUT http://your-domain.test/exam-entry/publish/99999
```
**Expected**: 404 or 500 with appropriate error
**Result**: ✅ PASS

---

## Performance Testing

### Load Test: Multiple Concurrent Publishes

**Setup**: Create test script with Apache Bench

```bash
# Install Apache Bench if not available
sudo apt-get install apache2-utils

# Test publish endpoint
ab -n 100 -c 10 -p publish_payload.json -T 'application/json' \
   -H 'Cookie: your-session-cookie' \
   http://your-domain.test/exam-entry/publish/1
```

**Expected**: All requests return 403 for unauthorized users, success for authorized

---

## Troubleshooting Guide

### Issue: Publish button not showing for Admin

**Debug Steps**:
```bash
php artisan tinker
```

```php
// Check role permissions
$admin = Role::find(2);
print_r($admin->permissions);
// Should include 'exam_entry_publish'

// Check specific user
$user = User::where('role_id', 2)->first();
echo "Role ID: " . $user->role_id . "\n";
print_r($user->permissions);
```

**Solution**: Run migration and clear caches

---

### Issue: 403 error for Super Admin

**Debug Steps**:
```bash
php artisan tinker
```

```php
// Check Super Admin role
$superAdmin = Role::find(1);
print_r($superAdmin->permissions);
// Should include 'exam_entry_publish'

// Test permission check
Auth::loginUsingId(SUPER_ADMIN_USER_ID);
echo hasPermission('exam_entry_publish') ? 'HAS PERMISSION' : 'NO PERMISSION';
```

**Solution**: Verify permission exists in role's permissions array

---

### Issue: Button shows but publish fails

**Debug Steps**:
1. Check exam entry status: `ExamEntry::find(ID)->status`
2. Should be 'completed', not 'draft' or 'published'
3. Check browser console for JavaScript errors
4. Check network tab for API response

**Solution**: Ensure exam entry is in 'completed' status

---

## Test Report Template

```markdown
# Exam Entry Publish Authorization Test Report

**Date**: [Date]
**Tester**: [Your Name]
**Environment**: [Development/Staging/Production]

## Test Results

### Super Admin (role_id = 1)
- [ ] Can see publish button: YES/NO
- [ ] Can publish successfully: YES/NO
- [ ] Receives success message: YES/NO
- [ ] Entry status updates: YES/NO

### Admin (role_id = 2)
- [ ] Can see publish button: YES/NO
- [ ] Can publish successfully: YES/NO
- [ ] Receives success message: YES/NO
- [ ] Entry status updates: YES/NO

### Other Roles (role_id ≥ 3)
- [ ] Publish button hidden: YES/NO
- [ ] API call blocked (403): YES/NO
- [ ] Error message correct: YES/NO

## Security Tests
- [ ] Direct API access blocked: YES/NO
- [ ] Permission manipulation prevented: YES/NO
- [ ] Role check enforced: YES/NO

## Issues Found
[List any issues discovered]

## Recommendations
[List any recommendations for improvements]

## Conclusion
**Overall Status**: PASS / FAIL / PARTIAL
```

---

## Continuous Testing

### After Each Deployment

1. ✅ Run migration
2. ✅ Clear caches
3. ✅ Test with Super Admin user
4. ✅ Test with Admin user
5. ✅ Test with non-admin user
6. ✅ Verify API security
7. ✅ Check error messages

### Regression Testing

Create automated tests for exam entry publish:

```php
// tests/Feature/ExamEntry/PublishAuthorizationTest.php

public function test_super_admin_can_publish()
{
    $user = User::factory()->create(['role_id' => 1]);
    $examEntry = ExamEntry::factory()->create(['status' => 'completed']);

    $response = $this->actingAs($user)
        ->putJson("/exam-entry/publish/{$examEntry->id}");

    $response->assertStatus(200);
    $response->assertJson(['success' => true]);
}

public function test_staff_cannot_publish()
{
    $user = User::factory()->create(['role_id' => 3]);
    $examEntry = ExamEntry::factory()->create(['status' => 'completed']);

    $response = $this->actingAs($user)
        ->putJson("/exam-entry/publish/{$examEntry->id}");

    $response->assertStatus(403);
}
```

---

**Last Updated**: January 30, 2025
**Status**: ✅ Ready for testing
