# Feature Access Control System - Implementation Guide

## Overview

Comprehensive access control system for feature-based permissions with zero-trust security model.

## Security Architecture

### Core Principles
- **Zero-trust**: Deny by default, explicit grants only
- **Super admin bypass**: role_id = 1 has full access
- **Multi-tenant isolation**: School-scoped feature access
- **Audit logging**: All access denials logged for security monitoring
- **No information leakage**: Generic error messages in production

## Files Created

### 1. Core Services
- `app/Services/FeatureAccessService.php` - Central business logic
- `app/Services/MenuGeneratorService.php` - Dynamic menu generation

### 2. Middleware
- `app/Http/Middleware/FeatureAccessMiddleware.php` - Route protection

### 3. Exception Handling
- `app/Exceptions/FeatureAccessDeniedException.php` - Custom exception

### 4. Helper Functions
- `app/Helpers/feature-helpers.php` - Convenience functions

### 5. Trait
- `app/Traits/EnforcesFeatureAccess.php` - Controller integration

### 6. Configuration
- `config/features.php` - Feature mappings and settings

### 7. Views
- `resources/views/errors/feature-access-denied.blade.php` - Error page

### 8. Artisan Commands
- `app/Console/Commands/ClearFeatureCacheCommand.php` - Cache management

### 9. Updates
- `app/Http/Kernel.php` - Middleware registration
- `composer.json` - Autoload helpers

## Usage Examples

### 1. Route Protection (Recommended)

```php
// Single feature requirement
Route::middleware(['auth', 'permission', 'feature.access:student_management'])
    ->group(function () {
        Route::get('/students', [StudentController::class, 'index'])->name('student.index');
        Route::post('/students', [StudentController::class, 'store'])->name('student.store');
    });

// Multiple routes with same feature
Route::middleware(['auth', 'feature.access:attendance'])->group(function () {
    Route::get('/attendance', [AttendanceController::class, 'index']);
    Route::post('/attendance/mark', [AttendanceController::class, 'mark']);
});

// Combine with existing middleware
Route::middleware(['auth', 'permission:student.view', 'feature.access:student_management'])
    ->get('/students', [StudentController::class, 'index']);
```

### 2. Controller-Based Protection

```php
use App\Traits\EnforcesFeatureAccess;

class ExaminationController extends Controller
{
    use EnforcesFeatureAccess;

    public function index()
    {
        // Check feature access and throw exception if denied
        $this->authorizeFeature('examination');

        // Your controller logic here
    }

    public function gradeEntry()
    {
        // Alternative: check and redirect if no access
        if ($redirect = $this->redirectIfNoFeature('examination')) {
            return $redirect;
        }

        // Your controller logic here
    }

    public function advancedReport()
    {
        // Check multiple features (all required)
        $this->authorizeFeatures(['examination', 'advanced_reporting'], requireAll: true);

        // Your controller logic here
    }

    public function onlineExam()
    {
        // Check multiple features (any one grants access)
        $this->authorizeFeatures(['online_examination', 'examination'], requireAll: false);

        // Your controller logic here
    }
}
```

### 3. View-Level Checks

```blade
{{-- Check feature access in Blade views --}}
@if(hasFeatureAccess('student_management'))
    <a href="{{ route('student.create') }}" class="btn btn-primary">
        Add Student
    </a>
@endif

{{-- Check multiple features --}}
@if(hasAnyFeature(['examination', 'online_examination']))
    <div class="exam-section">
        <!-- Exam content here -->
    </div>
@endif

{{-- Show upgrade prompt for premium features --}}
@if(isFeaturePremium('vehicle_tracking') && !hasFeatureAccess('vehicle_tracking'))
    <div class="alert alert-warning">
        <strong>Premium Feature:</strong> Vehicle tracking is available in higher packages.
        <a href="{{ route('subscription.upgrade') }}">Upgrade Now</a>
    </div>
@endif

{{-- Display current package features --}}
@php
    $stats = featureAccessStats();
@endphp

<div class="package-info">
    <strong>Package:</strong> {{ $stats['package_name'] }}
    <br>
    <strong>Features:</strong> {{ $stats['total_features'] }} active
</div>
```

### 4. Helper Function Usage

```php
// Check if current user's school has feature
if (hasFeatureAccess('library')) {
    // Feature is available
}

// Alias for semantic clarity
if (schoolHasFeature('fees_management')) {
    // Process fees
}

// Get all features for current school
$features = getSchoolFeatures();
// Returns: ['student_management', 'attendance', 'examination', ...]

// Get features by group
$academicFeatures = getFeaturesByGroup('academic');
// Returns: ['student_management', 'teacher_management', ...]

// Check if feature is premium
if (isFeaturePremium('vehicle_tracking')) {
    // Show premium badge or upgrade prompt
}

// Get detailed statistics
$stats = featureAccessStats();
/* Returns:
[
    'has_subscription' => true,
    'total_features' => 8,
    'active_features' => ['student_management', 'attendance', ...],
    'package_name' => 'Standard Package',
    'subscription_status' => 'active',
    'expires_at' => '2025-12-31',
]
*/

// Check if user is super admin
if (isSuperAdmin()) {
    // Super admin operations
}
```

### 5. Service Layer Usage

```php
use App\Services\FeatureAccessService;

class SchoolService
{
    private FeatureAccessService $featureService;

    public function __construct(FeatureAccessService $featureService)
    {
        $this->featureService = $featureService;
    }

    public function canAccessFeature(User $user, string $feature): bool
    {
        return $this->featureService->checkAccess($user, $feature);
    }

    public function getAccessibleRoutes(int $schoolId): array
    {
        return $this->featureService->getAccessibleRoutes($schoolId);
    }

    public function getBlockedFeatures(int $schoolId, int $targetPackageId): array
    {
        return $this->featureService->getBlockedFeatures($schoolId, $targetPackageId);
    }

    public function clearCache(int $schoolId): void
    {
        $this->featureService->clearSchoolCache($schoolId);
    }
}
```

## Route Examples by Module

### Academic Module Routes
```php
// routes/academic.php
Route::middleware(['auth', 'permission', 'feature.access:student_management'])->group(function () {
    Route::resource('students', StudentController::class);
    Route::post('students/promote', [StudentController::class, 'promote'])->name('student.promote');
});

Route::middleware(['auth', 'permission', 'feature.access:teacher_management'])->group(function () {
    Route::resource('teachers', TeacherController::class);
});
```

### Attendance Routes
```php
// routes/attendance.php
Route::middleware(['auth', 'feature.access:attendance'])->prefix('attendance')->group(function () {
    Route::get('/', [AttendanceController::class, 'index'])->name('attendance.index');
    Route::post('/mark', [AttendanceController::class, 'mark'])->name('attendance.mark');
    Route::get('/report', [AttendanceController::class, 'report'])->name('attendance.report');
});
```

### Examination Routes
```php
// routes/examination.php
Route::middleware(['auth', 'feature.access:examination'])->prefix('examination')->group(function () {
    Route::get('/', [ExaminationController::class, 'index'])->name('examination.index');
    Route::post('/grade', [ExaminationController::class, 'gradeEntry'])->name('examination.grade');
    Route::post('/publish', [ExaminationController::class, 'publish'])->name('examination.publish');
});
```

### Online Examination Routes
```php
// routes/online-examination.php
Route::middleware(['auth', 'feature.access:online_examination'])->prefix('online-exam')->group(function () {
    Route::get('/', [OnlineExamController::class, 'index'])->name('online-exam.index');
    Route::post('/create', [OnlineExamController::class, 'create'])->name('online-exam.create');
    Route::get('/start/{exam}', [OnlineExamController::class, 'start'])->name('online-exam.start');
});
```

### Fees Management Routes
```php
// routes/fees.php
Route::middleware(['auth', 'feature.access:fees_management'])->prefix('fees')->group(function () {
    Route::get('/', [FeesController::class, 'index'])->name('fees.index');
    Route::post('/collect', [FeesController::class, 'collect'])->name('fees.collect');
    Route::get('/report', [FeesController::class, 'report'])->name('fees.report');
});
```

### Library Routes
```php
// routes/library.php
Route::middleware(['auth', 'feature.access:library'])->prefix('library')->group(function () {
    Route::get('/books', [LibraryController::class, 'books'])->name('library.books');
    Route::post('/issue', [LibraryController::class, 'issue'])->name('library.issue');
    Route::post('/return', [LibraryController::class, 'return'])->name('library.return');
});
```

## Cache Management

### Artisan Commands

```bash
# Clear cache for specific school
php artisan features:clear-cache --school=1

# Clear cache for all schools
php artisan features:clear-cache --all

# Clear only menu caches
php artisan features:clear-cache --all --menus

# Clear only feature caches
php artisan features:clear-cache --all --features
```

### Programmatic Cache Clearing

```php
use App\Services\FeatureAccessService;
use App\Services\MenuGeneratorService;

// Clear feature cache for school
$featureService = app(FeatureAccessService::class);
$featureService->clearSchoolCache($schoolId);

// Clear all feature caches
$featureService->clearAllCaches();

// Clear menu cache for school
$menuService = app(MenuGeneratorService::class);
$menuService->clearMenuCache($schoolId);

// Clear menu cache for specific user
$menuService->clearMenuCache($schoolId, $userId);

// Helper function
clearFeatureCache(); // Clears cache for current authenticated user's school
```

### When to Clear Cache

Cache should be cleared when:
1. School's subscription is upgraded/downgraded
2. Package features are modified
3. Feature configuration changes
4. User permissions are updated
5. Manual cache clear requested by admin

**Example: Clear cache after subscription update**
```php
public function upgradeSubscription(Subscription $subscription, Package $newPackage)
{
    $subscription->update([
        'package_id' => $newPackage->id,
        'features' => $newPackage->packageChilds->pluck('feature.attribute')->toArray(),
    ]);

    // Clear feature and menu caches
    $featureService = app(FeatureAccessService::class);
    $featureService->clearSchoolCache($subscription->school_id);

    $menuService = app(MenuGeneratorService::class);
    $menuService->clearMenuCache($subscription->school_id);
}
```

## Configuration

### Feature Groups (config/features.php)

```php
'feature_groups' => [
    'academic' => [
        'student_management',
        'teacher_management',
        'class_management',
    ],
    'assessment' => [
        'attendance',
        'examination',
        'online_examination',
    ],
    'financial' => [
        'fees_management',
        'payment_gateway',
    ],
    'advanced' => [
        'multi_branch',
        'vehicle_tracking',
        'api_access',
    ],
],
```

### Premium Features

```php
'premium_features' => [
    'multi_branch',
    'vehicle_tracking',
    'advanced_reporting',
    'api_access',
    'custom_branding',
],
```

## Security Best Practices

### 1. Always Validate on Server Side
Never rely solely on frontend checks. Always protect routes with middleware or controller checks.

### 2. Use Super Admin Bypass Carefully
Super admin (role_id = 1) bypasses all feature checks. Ensure this role is only assigned to trusted administrators.

### 3. Audit Logging
All access denials are automatically logged. Review logs regularly:
```bash
tail -f storage/logs/laravel.log | grep "Feature Access Attempt"
```

### 4. Information Leakage Prevention
In production (`APP_DEBUG=false`), feature names are hidden from error messages to prevent information disclosure.

### 5. Cache Security
Feature caches use school-specific keys to prevent cross-tenant data leakage.

## Troubleshooting

### Issue: Features not working after implementation
**Solution:**
```bash
# Run composer dump-autoload to load new helpers
composer dump-autoload

# Clear all caches
php artisan cache:clear
php artisan config:clear
php artisan route:clear

# Clear feature caches
php artisan features:clear-cache --all
```

### Issue: Super admin still blocked
**Check:**
1. Verify `role_id = 1` in database
2. Check if user is authenticated
3. Review FeatureAccessService::isSuperAdmin() logic

### Issue: Feature check returns false incorrectly
**Debug:**
```php
// Check subscription status
$subscription = Subscription::where('school_id', $schoolId)->active()->first();
dd($subscription->features);

// Check feature access
$service = app(FeatureAccessService::class);
dd($service->getSchoolFeatures($schoolId));

// Check cache
dd(Cache::get("school_features_{$schoolId}"));
```

### Issue: Menu not filtering correctly
**Solution:**
```bash
# Clear menu cache
php artisan features:clear-cache --school=1 --menus

# Or programmatically
$menuService = app(MenuGeneratorService::class);
$menuService->clearMenuCache($schoolId);
```

## Testing

### Unit Test Example
```php
use Tests\TestCase;
use App\Services\FeatureAccessService;

class FeatureAccessServiceTest extends TestCase
{
    public function test_super_admin_has_full_access()
    {
        $superAdmin = User::factory()->create(['role_id' => 1]);
        $service = app(FeatureAccessService::class);

        $this->assertTrue($service->checkAccess($superAdmin, 'any_feature'));
    }

    public function test_user_with_feature_has_access()
    {
        $school = School::factory()->create();
        $subscription = Subscription::factory()->create([
            'school_id' => $school->id,
            'features' => ['student_management', 'attendance'],
        ]);

        $user = User::factory()->create(['school_id' => $school->id]);
        $service = app(FeatureAccessService::class);

        $this->assertTrue($service->checkAccess($user, 'student_management'));
        $this->assertFalse($service->checkAccess($user, 'vehicle_tracking'));
    }
}
```

## Migration Guide

### From Old FeatureCheck to New System

**Old Code:**
```php
Route::middleware(['FeatureCheck:student'])->group(function () {
    // routes
});
```

**New Code:**
```php
Route::middleware(['feature.access:student_management'])->group(function () {
    // routes
});
```

**Helper Function Migration:**
```php
// Old
if (hasFeature('student')) { }

// New (both work, use new for consistency)
if (hasFeature('student')) { } // Still works via existing helper
if (hasFeatureAccess('student_management')) { } // New comprehensive system
```

## Performance Optimization

- **Caching**: All feature checks are cached for 1 hour (configurable)
- **Database Queries**: Subscription data cached per school
- **Menu Generation**: Menu cached per school per user
- **Cache Tags**: Use Laravel cache tags for efficient bulk clearing

## Support

For issues or questions:
- Check logs: `storage/logs/laravel.log`
- Review configuration: `config/features.php`
- Run diagnostics: `php artisan features:clear-cache --school=1`
- Contact development team with error logs and reproduction steps
