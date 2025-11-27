<?php

namespace App\Services;

use App\Models\Setting;
use App\Models\MainSetting;
use Illuminate\Support\Facades\Cache;

/**
 * Service to determine which settings context to use (main vs school)
 *
 * This service handles the logic for determining whether to use:
 * - Main settings (for system admins, login pages, public pages)
 * - School settings (for school-specific users and operations)
 */
class SettingContextService
{
    /**
     * Determine if main settings should be used instead of school settings
     *
     * Use main settings when:
     * 1. User is not authenticated (login page, public pages)
     * 2. User is System Admin (school_id = NULL OR role_id = 0)
     *
     * @return bool True if main settings should be used
     */
    public function shouldUseMainSettings(): bool
    {
        // Not authenticated - use main settings
        if (!auth()->check()) {
            return true;
        }

        $user = auth()->user();

        // System Admin: role_id = 0 OR school_id = NULL - use main settings
        if ($user->school_id === null || $user->role_id == 0) {
            return true;
        }

        return false;
    }

    /**
     * Get a setting value based on the current context
     *
     * Returns main settings for system admins and unauthenticated users,
     * school settings for school-bound users.
     *
     * @param string $name Setting name to retrieve
     * @return string|null Setting value or null if not found
     */
    public function getSetting(string $name): ?string
    {
        if ($this->shouldUseMainSettings()) {
            return MainSetting::where('name', $name)->first()?->value;
        }

        return Setting::where('name', $name)->first()?->value;
    }

    /**
     * Get cache key prefix based on current context
     *
     * Returns different prefixes for:
     * - Main context: "main"
     * - School context: "school_{id}"
     *
     * @return string Cache key prefix
     */
    public function getCacheKeyPrefix(): string
    {
        if ($this->shouldUseMainSettings()) {
            return 'main';
        }

        return 'school_' . auth()->user()->school_id;
    }

    /**
     * Build a complete cache key for a setting
     *
     * @param string $name Setting name
     * @return string Complete cache key
     */
    public function buildCacheKey(string $name): string
    {
        return "setting_{$name}_{$this->getCacheKeyPrefix()}";
    }

    /**
     * Get current context type
     *
     * @return string 'main' or 'school'
     */
    public function getContextType(): string
    {
        return $this->shouldUseMainSettings() ? 'main' : 'school';
    }

    /**
     * Get current school ID (null for main context)
     *
     * @return int|null School ID or null
     */
    public function getCurrentSchoolId(): ?int
    {
        if (!auth()->check() || auth()->user()->school_id === null) {
            return null;
        }

        return auth()->user()->school_id;
    }

    /**
     * Clear cached settings for current context
     *
     * @param string|null $name Specific setting name, or null to clear all
     * @return void
     */
    public function clearCache(?string $name = null): void
    {
        if ($name) {
            Cache::forget($this->buildCacheKey($name));
        } else {
            // Clear by pattern would require cache tagging
            // For now, individual clearing is supported
        }
    }
}
