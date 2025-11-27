<?php

namespace App\Interfaces;

use Illuminate\Support\Collection;

interface MainSettingInterface
{
    /**
     * Get a setting value by name
     *
     * @param string $name
     * @return string|null
     */
    public function get(string $name): ?string;

    /**
     * Set a setting value by name
     *
     * @param string $name
     * @param mixed $value
     * @return bool
     */
    public function set(string $name, $value): bool;

    /**
     * Get all main settings
     *
     * @return Collection
     */
    public function all(): Collection;

    /**
     * Update general settings including file uploads
     *
     * @param mixed $request
     * @return bool
     */
    public function updateGeneralSettings($request): bool;
}
