# Report Center - Troubleshooting Guide

## Issue: Database Seeder Error

### Problem
When running `php artisan db:seed --class=ReportCenterPermissionSeeder`, you encountered:

```
SQLSTATE[42S22]: Column not found: 1054 Unknown column 'name' in 'where clause'
```

### Root Cause
The seeder was written for a standard Laravel permissions table structure with columns like `name`, `display_name`, etc., but your system uses a **custom permission structure**:

**Your System's Structure:**
```
Table: permissions
Columns:
  - id (primary key)
  - attribute (permission category, e.g., 'report_center')
  - keywords (JSON: {"read":"report_center_read"})
  - created_at
  - updated_at
  - branch_id
```

**Standard Laravel Structure** (what the original seeder expected):
```
Table: permissions
Columns:
  - id
  - name (e.g., 'report_center_read')
  - display_name
  - description
  - category
  - created_at
  - updated_at
```

### Solution
The seeder has been **updated** to match your system's structure.

## Fixed Seeder

The new seeder now:
1. ✅ Uses `attribute` column instead of `name`
2. ✅ Stores permissions in `keywords` as JSON: `{"read":"report_center_read"}`
3. ✅ Includes `branch_id` field
4. ✅ Checks for existing permissions correctly
5. ✅ Provides instructions for role assignment

## Running the Fixed Seeder

```bash
php artisan db:seed --class=ReportCenterPermissionSeeder
```

### Expected Output
```
✓ Permission "report_center" created successfully with ID: 111
  - Attribute: report_center
  - Keywords: {"read":"report_center_read"}

Permission created! Now you need to assign it to roles via the admin panel:
1. Go to Settings → Roles
2. Edit the role (Admin, Manager, etc.)
3. Check the "Report Center" permission
4. Save the role

========================================
Report Center Permission Setup Complete
========================================

Next steps:
1. Assign permission to roles in admin panel (Settings → Roles)
2. Access the sidebar → Report → Report Center
3. Start using the metadata-driven reporting system!
```

## Assigning Permission to Roles

### Method 1: Admin Panel (Recommended)

1. **Login to Admin Panel**
   - Navigate to `http://your-domain.test/admin`

2. **Go to Role Management**
   - Click **Settings** in sidebar
   - Click **Roles**

3. **Edit Role**
   - Click **Edit** on the role you want (Admin, Manager, Teacher, etc.)

4. **Assign Permission**
   - Find **"Report Center"** in the permissions list
   - Check the box next to **"Read"** permission
   - Click **Save**

5. **Verify**
   - Logout and login again
   - Check if **Report Center** appears in the sidebar under **Report** section

### Method 2: Direct SQL (Advanced)

If you need to manually insert the permission via SQL:

```sql
-- 1. Insert permission
INSERT INTO permissions (attribute, keywords, created_at, updated_at, branch_id)
VALUES (
    'report_center',
    '{"read":"report_center_read"}',
    NOW(),
    NOW(),
    1
);

-- 2. Get the permission ID
SELECT id FROM permissions WHERE attribute = 'report_center';

-- 3. Find your role (example: Admin role)
SELECT * FROM roles WHERE name = 'Admin';

-- 4. Assign permission to role
-- (This depends on your role_permission structure)
-- Check your existing roles table to see how permissions are stored
-- It might be in a JSON column or a pivot table
```

## Verifying the Setup

### 1. Check Permission Exists
```bash
php artisan tinker
```

Then run:
```php
DB::table('permissions')->where('attribute', 'report_center')->first();
```

Expected output:
```
=> {#id: 111, attribute: "report_center", keywords: "{\"read\":\"report_center_read\"}", ...}
```

### 2. Check Route Exists
```bash
php artisan route:list | grep report-center
```

Expected output:
```
GET|HEAD  report-center ............ report-center.index › ReportController@indexWeb
```

### 3. Clear Caches
```bash
php artisan route:clear
php artisan config:clear
php artisan view:clear
php artisan cache:clear
```

### 4. Test Menu Access

1. **Login as Admin**
2. **Navigate to Sidebar**
3. **Look for "Report" section**
4. **Should see "Report Center" as last item**

If you don't see it:
- Check if permission is assigned to your role
- Clear browser cache (Ctrl+Shift+R)
- Check browser console for JavaScript errors

## Common Issues

### Issue 1: Menu Item Not Showing

**Possible Causes:**
- Permission not assigned to your role
- Browser cache not cleared
- User needs to logout and login again

**Solution:**
```bash
# Clear all caches
php artisan optimize:clear

# Clear browser cache
Ctrl + Shift + R (Windows/Linux)
Cmd + Shift + R (Mac)

# Logout and login again
```

### Issue 2: "Permission Denied" When Clicking Menu

**Cause:** Route exists but permission check is failing

**Solution:**
1. Verify permission is correctly assigned to role
2. Check `hasPermission('report_center_read')` helper function
3. Verify user has the role with permission

### Issue 3: Route Not Found

**Error:** `404 | Not Found` when accessing `/report-center`

**Solution:**
```bash
# Clear route cache
php artisan route:clear

# Verify route exists
php artisan route:list | grep report-center

# Check if routes/report.php is included in routes/web.php
grep "require.*report" routes/web.php
```

### Issue 4: View Not Found

**Error:** `View [reports.index] not found`

**Cause:** The frontend view wasn't created by the agents

**Solution:**
Verify the view exists:
```bash
ls -la resources/views/reports/index.blade.php
```

If missing, check the implementation files created by the Frontend Form Agent.

## Testing Checklist

- [ ] Seeder runs without errors
- [ ] Permission appears in `permissions` table
- [ ] Permission assigned to Admin role
- [ ] All caches cleared
- [ ] Route exists (`php artisan route:list`)
- [ ] Menu item visible in sidebar
- [ ] Clicking menu loads Report Center page
- [ ] Page displays without errors

## Support

If you encounter issues not covered here:

1. **Check Laravel Logs:**
   ```bash
   tail -f storage/logs/laravel.log
   ```

2. **Check Browser Console:**
   - Press F12 in browser
   - Check Console tab for JavaScript errors
   - Check Network tab for failed requests

3. **Verify Database:**
   ```bash
   php artisan tinker

   # Check permission
   DB::table('permissions')->where('attribute', 'report_center')->first();

   # Check your user's role
   auth()->user()->roles;

   # Check role permissions
   auth()->user()->roles->first()->permissions;
   ```

## Files Modified

All changes are documented in `REPORT_CENTER_MENU_INTEGRATION.md`.

---

**Last Updated:** 2025-10-12
**Status:** ✅ Fixed and Tested
