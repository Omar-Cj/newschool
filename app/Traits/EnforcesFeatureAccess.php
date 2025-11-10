<?php

declare(strict_types=1);

namespace App\Traits;

use App\Services\FeatureAccessService;
use App\Exceptions\FeatureAccessDeniedException;
use Illuminate\Support\Facades\Auth;

/**
 * Enforces Feature Access Trait
 *
 * Provides feature access control methods for controllers
 * Use this trait in controllers that need feature-based access control
 */
trait EnforcesFeatureAccess
{
    /**
     * Authorize access to a feature or throw exception
     *
     * @param string $featureAttribute Feature to authorize
     * @throws FeatureAccessDeniedException If access denied
     * @return void
     */
    protected function authorizeFeature(string $featureAttribute): void
    {
        if (!$this->checkFeatureAccess($featureAttribute)) {
            $user = Auth::user();
            $service = app(FeatureAccessService::class);
            $stats = $service->getAccessStats($user->school_id);

            throw new FeatureAccessDeniedException(
                $featureAttribute,
                $stats['package_name'] ?? null,
                $user->school_id
            );
        }
    }

    /**
     * Check if current user has feature access
     *
     * @param string $featureAttribute Feature to check
     * @return bool True if access granted
     */
    protected function checkFeatureAccess(string $featureAttribute): bool
    {
        if (!Auth::check()) {
            return false;
        }

        $service = app(FeatureAccessService::class);
        return $service->checkAccess(Auth::user(), $featureAttribute);
    }

    /**
     * Get required features for this controller
     * Override this method in child controllers to specify required features
     *
     * @return array Array of required feature attributes
     */
    protected function getRequiredFeatures(): array
    {
        return [];
    }

    /**
     * Redirect to upgrade page if feature not available
     *
     * @param string $featureAttribute Feature to check
     * @param string|null $redirectRoute Route to redirect to if no upgrade
     * @return \Illuminate\Http\RedirectResponse|null Redirect or null if access granted
     */
    protected function redirectIfNoFeature(
        string $featureAttribute,
        ?string $redirectRoute = null
    ): ?\Illuminate\Http\RedirectResponse {
        if ($this->checkFeatureAccess($featureAttribute)) {
            return null;
        }

        $user = Auth::user();
        $upgradeRoute = route('subscription.upgrade', ['school' => $user->school_id]);

        return redirect($upgradeRoute)
            ->with('warning', 'This feature is not available in your current package.')
            ->with('upgrade_prompt', true);
    }

    /**
     * Authorize multiple features at once
     *
     * @param array $featureAttributes Features to authorize
     * @param bool $requireAll If true, all features required; if false, any feature grants access
     * @throws FeatureAccessDeniedException If access denied
     * @return void
     */
    protected function authorizeFeatures(array $featureAttributes, bool $requireAll = true): void
    {
        if ($requireAll) {
            foreach ($featureAttributes as $feature) {
                $this->authorizeFeature($feature);
            }
        } else {
            // At least one feature required
            $hasAnyAccess = false;
            foreach ($featureAttributes as $feature) {
                if ($this->checkFeatureAccess($feature)) {
                    $hasAnyAccess = true;
                    break;
                }
            }

            if (!$hasAnyAccess) {
                $this->authorizeFeature($featureAttributes[0]); // Throws exception
            }
        }
    }

    /**
     * Get feature access statistics for view
     *
     * @return array Feature access stats
     */
    protected function getFeatureStats(): array
    {
        if (!Auth::check() || !Auth::user()->school_id) {
            return [];
        }

        $service = app(FeatureAccessService::class);
        return $service->getAccessStats(Auth::user()->school_id);
    }

    /**
     * Share feature access data with all views
     *
     * @return void
     */
    protected function shareFeatureDataWithViews(): void
    {
        if (!Auth::check() || !Auth::user()->school_id) {
            return;
        }

        $service = app(FeatureAccessService::class);
        $features = $service->getSchoolFeatures(Auth::user()->school_id);

        view()->share('schoolFeatures', $features);
        view()->share('featureStats', $this->getFeatureStats());
    }
}
