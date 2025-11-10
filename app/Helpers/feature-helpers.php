<?php

declare(strict_types=1);

use App\Services\FeatureAccessService;
use Illuminate\Support\Facades\Auth;

/**
 * Feature Access Helper Functions
 *
 * Convenient wrapper functions for feature access checking
 * Integrates with FeatureAccessService for consistent behavior
 */

if (!function_exists('hasFeatureAccess')) {
    /**
     * Check if current user's school has access to a feature
     *
     * @param string $featureAttribute Feature attribute identifier
     * @return bool True if access granted
     */
    function hasFeatureAccess(string $featureAttribute): bool
    {
        if (!Auth::check()) {
            return false;
        }

        $service = app(FeatureAccessService::class);
        return $service->checkAccess(Auth::user(), $featureAttribute);
    }
}

if (!function_exists('schoolHasFeature')) {
    /**
     * Alias for hasFeatureAccess - check if school has feature
     *
     * @param string $featureAttribute Feature attribute identifier
     * @return bool True if school has feature
     */
    function schoolHasFeature(string $featureAttribute): bool
    {
        return hasFeatureAccess($featureAttribute);
    }
}

if (!function_exists('getSchoolFeatures')) {
    /**
     * Get all features available to current user's school
     *
     * @return array Array of feature attributes
     */
    function getSchoolFeatures(): array
    {
        if (!Auth::check() || !Auth::user()->school_id) {
            return [];
        }

        $service = app(FeatureAccessService::class);
        return $service->getSchoolFeatures(Auth::user()->school_id);
    }
}

if (!function_exists('getFeaturesByGroup')) {
    /**
     * Get features by feature group for current user's school
     *
     * @param string $groupSlug Feature group identifier
     * @return array Filtered features array
     */
    function getFeaturesByGroup(string $groupSlug): array
    {
        if (!Auth::check() || !Auth::user()->school_id) {
            return [];
        }

        $service = app(FeatureAccessService::class);
        return $service->getFeaturesByGroup(Auth::user()->school_id, $groupSlug);
    }
}

if (!function_exists('isFeaturePremium')) {
    /**
     * Check if a feature is marked as premium
     *
     * @param string $featureAttribute Feature attribute identifier
     * @return bool True if premium feature
     */
    function isFeaturePremium(string $featureAttribute): bool
    {
        $service = app(FeatureAccessService::class);
        return $service->isFeaturePremium($featureAttribute);
    }
}

if (!function_exists('featureAccessStats')) {
    /**
     * Get feature access statistics for current school
     *
     * @return array Statistics including package info, feature count, etc.
     */
    function featureAccessStats(): array
    {
        if (!Auth::check() || !Auth::user()->school_id) {
            return [
                'has_subscription' => false,
                'total_features' => 0,
                'active_features' => [],
                'package_name' => null,
            ];
        }

        $service = app(FeatureAccessService::class);
        return $service->getAccessStats(Auth::user()->school_id);
    }
}

if (!function_exists('clearFeatureCache')) {
    /**
     * Clear feature cache for current school
     *
     * @return void
     */
    function clearFeatureCache(): void
    {
        if (!Auth::check() || !Auth::user()->school_id) {
            return;
        }

        $service = app(FeatureAccessService::class);
        $service->clearSchoolCache(Auth::user()->school_id);
    }
}

if (!function_exists('hasAnyFeature')) {
    /**
     * Check if user has access to any of the specified features
     *
     * @param array $featureAttributes Array of feature attributes
     * @return bool True if user has at least one feature
     */
    function hasAnyFeature(array $featureAttributes): bool
    {
        foreach ($featureAttributes as $feature) {
            if (hasFeatureAccess($feature)) {
                return true;
            }
        }
        return false;
    }
}

if (!function_exists('hasAllFeatures')) {
    /**
     * Check if user has access to all specified features
     *
     * @param array $featureAttributes Array of feature attributes
     * @return bool True if user has all features
     */
    function hasAllFeatures(array $featureAttributes): bool
    {
        foreach ($featureAttributes as $feature) {
            if (!hasFeatureAccess($feature)) {
                return false;
            }
        }
        return true;
    }
}

if (!function_exists('isSuperAdmin')) {
    /**
     * Check if current user is super admin (role_id = 1)
     *
     * @return bool True if super admin
     */
    function isSuperAdmin(): bool
    {
        if (!Auth::check()) {
            return false;
        }

        return Auth::user()->role_id === 1;
    }
}

if (!function_exists('canAccessFeature')) {
    /**
     * Alias for hasFeatureAccess with more semantic naming
     *
     * @param string $featureAttribute Feature attribute identifier
     * @return bool True if can access
     */
    function canAccessFeature(string $featureAttribute): bool
    {
        return hasFeatureAccess($featureAttribute);
    }
}
