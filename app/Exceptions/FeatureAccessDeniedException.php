<?php

declare(strict_types=1);

namespace App\Exceptions;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;

/**
 * Feature Access Denied Exception
 *
 * Thrown when user attempts to access a feature not included in their school's package
 * Provides user-friendly error pages with upgrade prompts
 */
class FeatureAccessDeniedException extends Exception
{
    /**
     * Feature attribute that was denied
     */
    private string $featureAttribute;

    /**
     * Current package name
     */
    private ?string $currentPackage;

    /**
     * School ID for context
     */
    private ?int $schoolId;

    /**
     * Create a new exception instance
     *
     * @param string $featureAttribute Feature that was denied
     * @param string|null $currentPackage Current package name
     * @param int|null $schoolId School ID
     * @param string|null $message Custom message
     */
    public function __construct(
        string $featureAttribute,
        ?string $currentPackage = null,
        ?int $schoolId = null,
        ?string $message = null
    ) {
        $this->featureAttribute = $featureAttribute;
        $this->currentPackage = $currentPackage;
        $this->schoolId = $schoolId;

        $defaultMessage = $message ?? 'This feature is not available in your current package.';

        parent::__construct($defaultMessage, 403);
    }

    /**
     * Get feature attribute
     *
     * @return string
     */
    public function getFeatureAttribute(): string
    {
        return $this->featureAttribute;
    }

    /**
     * Get current package
     *
     * @return string|null
     */
    public function getCurrentPackage(): ?string
    {
        return $this->currentPackage;
    }

    /**
     * Get school ID
     *
     * @return int|null
     */
    public function getSchoolId(): ?int
    {
        return $this->schoolId;
    }

    /**
     * Render the exception as an HTTP response
     *
     * @param Request $request
     * @return Response
     */
    public function render(Request $request): Response
    {
        // Log the access denial for security monitoring
        Log::warning('Feature Access Denied', [
            'user_id' => auth()->id(),
            'school_id' => $this->schoolId,
            'feature' => $this->featureAttribute,
            'current_package' => $this->currentPackage,
            'url' => $request->fullUrl(),
            'ip' => $request->ip(),
        ]);

        // API requests get JSON response
        if ($request->expectsJson() || $request->is('api/*')) {
            return response()->json([
                'success' => false,
                'message' => $this->getMessage(),
                'error' => 'feature_access_denied',
                'feature_required' => config('features.display_feature_names', false)
                    ? $this->featureAttribute
                    : null,
                'current_package' => $this->currentPackage,
                'upgrade_url' => route('subscription.upgrade', ['school' => $this->schoolId]),
            ], 403);
        }

        // Web requests get custom error page
        return response()->view('errors.feature-access-denied', [
            'message' => $this->getMessage(),
            'featureAttribute' => $this->featureAttribute,
            'currentPackage' => $this->currentPackage,
            'schoolId' => $this->schoolId,
            'upgradeUrl' => route('subscription.upgrade', ['school' => $this->schoolId]),
            'contactUrl' => route('contact.support'),
        ], 403);
    }

    /**
     * Report the exception
     *
     * @return bool|null
     */
    public function report(): ?bool
    {
        // Don't report to error tracking services (expected business exception)
        // Already logged in render() method
        return false;
    }
}
