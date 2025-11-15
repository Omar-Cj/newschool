<?php

declare(strict_types=1);

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Auth;

/**
 * Menu Generator Service
 *
 * Dynamically generates and filters menu items based on:
 * - User permissions
 * - School features (package-based access)
 * - User roles
 */
class MenuGeneratorService
{
    /**
     * Feature access service
     */
    private FeatureAccessService $featureAccessService;

    /**
     * Cache duration for menus (1 hour)
     */
    private const CACHE_TTL = 3600;

    /**
     * Create a new service instance
     *
     * @param FeatureAccessService $featureAccessService
     */
    public function __construct(FeatureAccessService $featureAccessService)
    {
        $this->featureAccessService = $featureAccessService;
    }

    /**
     * Generate school menu filtered by features and permissions
     *
     * @param int $schoolId School ID
     * @param int $userId User ID
     * @return array Filtered menu structure
     */
    public function generateSchoolMenu(int $schoolId, int $userId): array
    {
        $cacheKey = "school_menu_{$schoolId}_{$userId}";

        return Cache::remember($cacheKey, self::CACHE_TTL, function () use ($schoolId, $userId) {
            $user = Auth::user();
            $features = $this->featureAccessService->getSchoolFeatures($schoolId);

            // Load menu configuration
            $menuConfig = config('features.menu_structure', []);

            // Filter menu by features and permissions
            return $this->filterMenuByFeatures($menuConfig, $features, $user);
        });
    }

    /**
     * Filter menu items based on features and permissions
     *
     * @param array $menuItems Menu configuration
     * @param array $features Available features
     * @param mixed $user User instance
     * @return array Filtered menu
     */
    public function filterMenuByFeatures(array $menuItems, array $features, $user): array
    {
        $filteredMenu = [];

        foreach ($menuItems as $menuItem) {
            // Check if menu item requires specific feature
            if (isset($menuItem['feature_required'])) {
                if (!in_array($menuItem['feature_required'], $features)) {
                    // Feature not available - skip this menu item
                    continue;
                }
            }

            // Check if menu item requires specific permission
            if (isset($menuItem['permission_required'])) {
                if (!hasPermission($menuItem['permission_required'])) {
                    // Permission not available - skip this menu item
                    continue;
                }
            }

            // Check role requirements
            if (isset($menuItem['role_required'])) {
                $allowedRoles = is_array($menuItem['role_required'])
                    ? $menuItem['role_required']
                    : [$menuItem['role_required']];

                if (!in_array($user->role_id, $allowedRoles)) {
                    continue;
                }
            }

            // Build menu item
            $item = $this->buildMenuItem($menuItem, $features, $user);

            if ($item) {
                $filteredMenu[] = $item;
            }
        }

        return $filteredMenu;
    }

    /**
     * Build individual menu item with recursive children filtering
     *
     * @param array $config Menu item configuration
     * @param array $features Available features
     * @param mixed $user User instance
     * @return array|null Menu item or null if filtered out
     */
    public function buildMenuItem(array $config, array $features, $user): ?array
    {
        $menuItem = [
            'title' => $config['title'] ?? '',
            'url' => $config['url'] ?? '#',
            'icon' => $config['icon'] ?? 'fa-circle',
            'badge' => $config['badge'] ?? null,
            'badge_class' => $config['badge_class'] ?? 'badge-info',
        ];

        // Handle children recursively
        if (isset($config['children']) && is_array($config['children'])) {
            $children = $this->filterMenuByFeatures($config['children'], $features, $user);

            // If no children pass the filter, hide parent too
            if (empty($children) && ($config['hide_if_empty'] ?? false)) {
                return null;
            }

            $menuItem['children'] = $children;
        }

        // Add premium badge if feature is premium
        if (isset($config['feature_required']) && $this->featureAccessService->isFeaturePremium($config['feature_required'])) {
            $menuItem['is_premium'] = true;
        }

        return $menuItem;
    }

    /**
     * Clear menu cache for school
     *
     * @param int $schoolId School ID
     * @param int|null $userId Specific user or all users
     * @return void
     */
    public function clearMenuCache(int $schoolId, ?int $userId = null): void
    {
        if ($userId) {
            Cache::forget("school_menu_{$schoolId}_{$userId}");
        } else {
            // Clear all menu caches for school (requires cache tagging)
            Cache::tags(['menus', "school_{$schoolId}"])->flush();
        }
    }

    /**
     * Get upgrade prompts for locked features in menu
     *
     * @param int $schoolId School ID
     * @return array Array of locked features with upgrade prompts
     */
    public function getLockedFeatures(int $schoolId): array
    {
        $features = $this->featureAccessService->getSchoolFeatures($schoolId);
        $menuConfig = config('features.menu_structure', []);

        $lockedFeatures = [];

        foreach ($menuConfig as $menuItem) {
            if (isset($menuItem['feature_required'])) {
                if (!in_array($menuItem['feature_required'], $features)) {
                    $lockedFeatures[] = [
                        'title' => $menuItem['title'],
                        'feature' => $menuItem['feature_required'],
                        'description' => $menuItem['description'] ?? '',
                        'is_premium' => $this->featureAccessService->isFeaturePremium(
                            $menuItem['feature_required']
                        ),
                    ];
                }
            }

            // Check children recursively
            if (isset($menuItem['children'])) {
                foreach ($menuItem['children'] as $child) {
                    if (isset($child['feature_required'])) {
                        if (!in_array($child['feature_required'], $features)) {
                            $lockedFeatures[] = [
                                'title' => $child['title'],
                                'feature' => $child['feature_required'],
                                'description' => $child['description'] ?? '',
                                'is_premium' => $this->featureAccessService->isFeaturePremium(
                                    $child['feature_required']
                                ),
                            ];
                        }
                    }
                }
            }
        }

        return $lockedFeatures;
    }
}
