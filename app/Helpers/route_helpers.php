<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Log;

if (!function_exists('safeRoute')) {
    /**
     * Safely generate a route URL with fallback for undefined routes
     *
     * This helper prevents RouteNotFoundException errors by checking if a route exists
     * before attempting to generate its URL. Useful in multi-tenant environments where
     * routes may be conditionally registered.
     *
     * @param string $name Route name to generate
     * @param array $parameters Route parameters
     * @param bool $absolute Generate absolute URL
     * @return string Route URL or '#' if route doesn't exist
     */
    function safeRoute(string $name, array $parameters = [], bool $absolute = true): string
    {
        try {
            if (Route::has($name)) {
                return route($name, $parameters, $absolute);
            }

            // Log missing route for debugging
            Log::warning('Attempted to access undefined route', [
                'route_name' => $name,
                'user_id' => auth()->id() ?? 'guest',
                'role_id' => auth()->check() ? auth()->user()->role_id : null,
                'school_id' => auth()->check() ? auth()->user()->school_id : null,
                'parameters' => $parameters,
            ]);

            // Return hash as safe fallback
            return '#';

        } catch (\Throwable $th) {
            Log::error('safeRoute error', [
                'route_name' => $name,
                'error' => $th->getMessage(),
                'user_id' => auth()->id() ?? 'guest',
            ]);

            return '#';
        }
    }
}
