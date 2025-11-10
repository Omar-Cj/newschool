<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\User;
use App\Models\Subscription;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Modules\MainApp\Entities\Package;
use Modules\MainApp\Entities\Feature;

/**
 * Feature Access Service
 *
 * Handles all feature access control logic with zero-trust security model
 * - Deny by default
 * - Super admin bypass (role_id = 1)
 * - School isolation for multi-tenant safety
 * - Comprehensive audit logging
 */
class FeatureAccessService
{
    /**
     * Cache duration for feature access data (1 hour)
     */
    private const CACHE_TTL = 3600;

    /**
     * Super admin role ID with full access bypass
     */
    private const SUPER_ADMIN_ROLE_ID = 1;

    /**
     * Check if user has access to specific feature
     *
     * @param User $user User to check access for
     * @param string $featureAttribute Feature attribute identifier
     * @return bool True if access granted
     */
    public function checkAccess(User $user, string $featureAttribute): bool
    {
        // Super admin bypass - full access
        if ($this->isSuperAdmin($user)) {
            $this->logAccessAttempt($user, $featureAttribute, true, 'super_admin_bypass');
            return true;
        }

        // Non-SaaS mode - all features enabled
        if (!env('APP_SAAS', false)) {
            return true;
        }

        // Check if school has active subscription with feature
        $hasAccess = $this->schoolHasFeature($user, $featureAttribute);

        // Audit logging
        $this->logAccessAttempt($user, $featureAttribute, $hasAccess);

        return $hasAccess;
    }

    /**
     * Get all accessible routes for a school
     *
     * @param int $schoolId School ID
     * @return array Array of accessible route names
     */
    public function getAccessibleRoutes(int $schoolId): array
    {
        $cacheKey = "accessible_routes_school_{$schoolId}";

        return Cache::remember($cacheKey, self::CACHE_TTL, function () use ($schoolId) {
            $features = $this->getSchoolFeatures($schoolId);
            $routes = [];

            foreach ($features as $feature) {
                // Get routes mapped to this feature from config
                $featureRoutes = config("features.feature_routes.{$feature}", []);
                $routes = array_merge($routes, $featureRoutes);
            }

            return array_unique($routes);
        });
    }

    /**
     * Get features blocked for school compared to target package
     *
     * @param int $schoolId School ID
     * @param int $targetPackageId Target package to compare
     * @return array Array of blocked feature attributes
     */
    public function getBlockedFeatures(int $schoolId, int $targetPackageId): array
    {
        $currentFeatures = $this->getSchoolFeatures($schoolId);
        $targetFeatures = $this->getPackageFeatures($targetPackageId);

        return array_diff($targetFeatures, $currentFeatures);
    }

    /**
     * Log access attempt for audit trail
     *
     * @param User $user User attempting access
     * @param string $featureAttribute Feature being accessed
     * @param bool $granted Whether access was granted
     * @param string|null $reason Reason for decision
     * @return void
     */
    public function logAccessAttempt(
        User $user,
        string $featureAttribute,
        bool $granted,
        ?string $reason = null
    ): void {
        // Only log denials and super admin access for security audits
        if (!$granted || $reason === 'super_admin_bypass') {
            Log::channel('daily')->info('Feature Access Attempt', [
                'user_id' => $user->id,
                'school_id' => $user->school_id,
                'role_id' => $user->role_id,
                'feature' => $featureAttribute,
                'granted' => $granted,
                'reason' => $reason,
                'ip' => request()->ip(),
                'user_agent' => request()->userAgent(),
                'timestamp' => now()->toIso8601String(),
            ]);
        }
    }

    /**
     * Get access statistics for a school
     *
     * @param int $schoolId School ID
     * @return array Statistics array
     */
    public function getAccessStats(int $schoolId): array
    {
        $subscription = $this->getActiveSubscription($schoolId);

        if (!$subscription) {
            return [
                'has_subscription' => false,
                'total_features' => 0,
                'active_features' => [],
                'package_name' => null,
            ];
        }

        $features = $subscription->features ?? [];
        $package = Package::find($subscription->package_id);

        return [
            'has_subscription' => true,
            'total_features' => count($features),
            'active_features' => $features,
            'package_name' => $package->name ?? 'Unknown',
            'subscription_status' => $subscription->status,
            'expires_at' => $subscription->expires_at ?? null,
        ];
    }

    /**
     * Get all features for a school
     *
     * @param int $schoolId School ID
     * @return array Array of feature attributes
     */
    public function getSchoolFeatures(int $schoolId): array
    {
        $cacheKey = "school_features_{$schoolId}";

        return Cache::remember($cacheKey, self::CACHE_TTL, function () use ($schoolId) {
            $subscription = $this->getActiveSubscription($schoolId);

            if (!$subscription) {
                return [];
            }

            // Return features array from subscription
            return $subscription->features ?? [];
        });
    }

    /**
     * Get features by feature group
     *
     * @param int $schoolId School ID
     * @param string $groupSlug Feature group identifier
     * @return array Filtered features array
     */
    public function getFeaturesByGroup(int $schoolId, string $groupSlug): array
    {
        $allFeatures = $this->getSchoolFeatures($schoolId);
        $groupFeatures = config("features.feature_groups.{$groupSlug}", []);

        return array_intersect($allFeatures, $groupFeatures);
    }

    /**
     * Check if feature is premium
     *
     * @param string $featureAttribute Feature attribute
     * @return bool True if premium feature
     */
    public function isFeaturePremium(string $featureAttribute): bool
    {
        $premiumFeatures = config('features.premium_features', []);
        return in_array($featureAttribute, $premiumFeatures);
    }

    /**
     * Clear feature cache for school
     *
     * @param int $schoolId School ID
     * @return void
     */
    public function clearSchoolCache(int $schoolId): void
    {
        Cache::forget("school_features_{$schoolId}");
        Cache::forget("accessible_routes_school_{$schoolId}");
        Cache::forget("active_subscription_school_{$schoolId}");
    }

    /**
     * Clear all feature caches
     *
     * @return void
     */
    public function clearAllCaches(): void
    {
        Cache::tags(['features', 'subscriptions'])->flush();
    }

    /**
     * Check if user is super admin
     *
     * @param User $user User to check
     * @return bool True if super admin
     */
    private function isSuperAdmin(User $user): bool
    {
        return $user->role_id === self::SUPER_ADMIN_ROLE_ID;
    }

    /**
     * Check if school has specific feature
     *
     * @param User $user User with school context
     * @param string $featureAttribute Feature to check
     * @return bool True if school has feature
     */
    private function schoolHasFeature(User $user, string $featureAttribute): bool
    {
        if (!$user->school_id) {
            return false;
        }

        $features = $this->getSchoolFeatures($user->school_id);
        return in_array($featureAttribute, $features);
    }

    /**
     * Get active subscription for school
     *
     * @param int $schoolId School ID
     * @return Subscription|null Active subscription or null
     */
    private function getActiveSubscription(int $schoolId): ?Subscription
    {
        $cacheKey = "active_subscription_school_{$schoolId}";

        return Cache::remember($cacheKey, self::CACHE_TTL, function () use ($schoolId) {
            return Subscription::where('school_id', $schoolId)
                ->active()
                ->first();
        });
    }

    /**
     * Get features for a package
     *
     * @param int $packageId Package ID
     * @return array Array of feature attributes
     */
    private function getPackageFeatures(int $packageId): array
    {
        $cacheKey = "package_features_{$packageId}";

        return Cache::remember($cacheKey, self::CACHE_TTL, function () use ($packageId) {
            $package = Package::with('packageChilds.feature')->find($packageId);

            if (!$package) {
                return [];
            }

            return $package->packageChilds
                ->pluck('feature.attribute')
                ->filter()
                ->values()
                ->toArray();
        });
    }
}
