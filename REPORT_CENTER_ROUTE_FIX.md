# Report Center - Route Fix

## Issue: Route [api.reports.index] not defined

### Problem
When accessing the Report Center page (`/report-center`), you encountered:

```
Symfony\Component\Routing\Exception\RouteNotFoundException
Route [api.reports.index] not defined.

At: resources/views/reports/index.blade.php:264
```

### Root Cause

The blade template was trying to use a **named route** that doesn't exist:

```blade
apiBaseUrl: '{{ route('api.reports.index') }}',  ❌ Named route doesn't exist
```

### Actual Route Structure

The API routes created by the backend agent are defined **without names** in `routes/api.php`:

```php
Route::group(['prefix' => 'teacher'], function () {
    // ...
    Route::group(['prefix' => 'reports'], function () {
        Route::get('/', [ReportController::class, 'index']);              // ✅ /api/teacher/reports
        Route::get('/categories', [ReportController::class, 'categories']); // ✅ /api/teacher/reports/categories
        Route::get('/{reportId}', [ReportController::class, 'show']);      // ✅ /api/teacher/reports/{id}
        // ... more routes
    });
});
```

**Full API URL:** `/api/teacher/reports`

The routes don't have `->name()` declarations, so you can't use `route('api.reports.index')`.

### Solution Applied

Changed the blade template to use a **direct URL** instead of a named route:

```blade
apiBaseUrl: '{{ url('/api/teacher/reports') }}',  ✅ Direct URL
```

### File Modified

**File:** `resources/views/reports/index.blade.php` (line 264)

**Before:**
```javascript
window.ReportConfig = {
    apiBaseUrl: '{{ route('api.reports.index') }}',  // ❌ Error
    // ...
};
```

**After:**
```javascript
window.ReportConfig = {
    apiBaseUrl: '{{ url('/api/teacher/reports') }}',  // ✅ Works
    // ...
};
```

### How It Works Now

1. **User visits:** `http://your-domain.test/report-center`
2. **Blade renders:** JavaScript config with `apiBaseUrl: '/api/teacher/reports'`
3. **Frontend JavaScript:** Makes API calls to `/api/teacher/reports`
4. **Laravel routes:** Handles the API requests via `ReportController`

### Available API Endpoints

All endpoints are now accessible via direct URLs:

| Method | Endpoint | Description |
|--------|----------|-------------|
| `GET` | `/api/teacher/reports` | List all reports grouped by category |
| `GET` | `/api/teacher/reports/categories` | Get all report categories |
| `GET` | `/api/teacher/reports/{reportId}` | Get specific report details |
| `GET` | `/api/teacher/reports/{reportId}/parameters` | Get report parameters |
| `POST` | `/api/teacher/reports/parameters/{parameterId}/dependent-values` | Get dependent values |
| `POST` | `/api/teacher/reports/{reportId}/execute` | Execute report |
| `POST` | `/api/teacher/reports/{reportId}/export/{format}` | Export report (Excel/PDF/CSV) |
| `GET` | `/api/teacher/reports/{reportId}/statistics` | Get execution statistics |

### Why This Approach

**Pros:**
- ✅ Simple and direct
- ✅ No need to add route names
- ✅ Works immediately
- ✅ Follows the pattern used in the rest of the API routes

**Alternative (if you prefer named routes):**

You could add names to the API routes in `routes/api.php`:

```php
Route::group(['prefix' => 'reports', 'as' => 'api.reports.'], function () {
    Route::get('/', [ReportController::class, 'index'])->name('index');
    Route::get('/categories', [ReportController::class, 'categories'])->name('categories');
    // ... etc
});
```

Then use: `{{ route('api.reports.index') }}`

However, most API routes in this project don't use named routes, so the direct URL approach is more consistent.

### Testing

1. **Clear caches:**
   ```bash
   php artisan view:clear
   php artisan route:clear
   php artisan config:clear
   ```

2. **Access the page:**
   - Navigate to `http://your-domain.test/report-center`
   - Page should load without route errors

3. **Check browser console:**
   - Open browser DevTools (F12)
   - Go to Console tab
   - Look for `window.ReportConfig`
   - Should see: `apiBaseUrl: "http://your-domain.test/api/teacher/reports"`

4. **Test API calls:**
   - The JavaScript should now make API calls to `/api/teacher/reports`
   - Check Network tab in DevTools
   - Should see successful API requests

### Other Route Issues?

If you encounter similar route errors in other files, check:

1. **For named routes:** Look in `routes/api.php` or `routes/web.php` for `->name('...')`
2. **If route has no name:** Use `url('/path/to/endpoint')` instead of `route('name')`
3. **Check route exists:** Run `php artisan route:list | grep pattern`

### Common Patterns in This Project

The project uses **different approaches** for web vs API routes:

**Web Routes** (with names):
```php
Route::get('/report-center', [ReportController::class, 'indexWeb'])
    ->name('report-center.index');  // ✅ Has name

// Usage: route('report-center.index')
```

**API Routes** (usually without names):
```php
Route::get('/api/teacher/reports', [ReportController::class, 'index']);
// ❌ No name

// Usage: url('/api/teacher/reports')
```

### Prevention

To avoid this issue in the future:

1. **Check if route has a name** before using `route()` helper
2. **Use `url()` helper** for unnamed routes
3. **Add route names** if you prefer using the `route()` helper
4. **Run `php artisan route:list`** to verify route exists and check its name

---

**Fixed File:** `resources/views/reports/index.blade.php`
**Status:** ✅ Fixed and Ready to Use
**Date:** 2025-10-12
