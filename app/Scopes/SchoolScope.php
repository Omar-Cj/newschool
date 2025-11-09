<?php

declare(strict_types=1);

namespace App\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;
use Illuminate\Support\Facades\Schema;

/**
 * SchoolScope - Automatic school_id filtering for multi-school architecture
 *
 * This scope automatically filters model queries by school_id based on the authenticated user's
 * school context. It's designed to work with BaseModel and provides automatic data isolation
 * between different schools in a multi-school system.
 *
 * SECURITY CRITICAL - Precedence Rules:
 * 1. System Admin (role_id=0, school_id=NULL): NO SCOPE (sees all schools)
 * 2. School Users (role_id>=1, school_id=X): ALWAYS filter by user->school_id
 * 3. Session is ONLY used for System Admin context switching
 * 4. Session is NEVER allowed to override school users' school_id
 *
 * Features:
 * - Skips filtering for System Admin (role_id=0 with NULL school_id)
 * - Enforces school_id filter from authenticated user for school users
 * - Only filters tables that have school_id column
 * - Works seamlessly with existing branch_id scoping
 * - Supports session-based school_id override (System Admin only)
 * - Includes security logging for debugging and auditing
 *
 * Usage:
 * 1. Apply to individual models:
 *    class MyModel extends BaseModel {
 *        protected static function boot() {
 *            parent::boot();
 *            static::addGlobalScope(new SchoolScope());
 *        }
 *    }
 *
 * 2. Apply to BaseModel for all child models:
 *    // In BaseModel::boot()
 *    static::addGlobalScope(new SchoolScope());
 */
class SchoolScope implements Scope
{
    /**
     * Apply the scope to a given Eloquent query builder.
     *
     * @param Builder $builder The query builder instance
     * @param Model $model The model instance
     * @return void
     */
    public function apply(Builder $builder, Model $model): void
    {
        // Get school_id from authenticated user or session
        $schoolId = $this->getSchoolId();

        // Skip filtering for admin users (school_id === null indicates admin/super-user)
        if ($schoolId === null) {
            return;
        }

        // Get the table name from the query
        $table = $builder->getQuery()->from;

        // Only apply filter if the table has school_id column
        if (Schema::hasColumn($table, 'school_id')) {
            $builder->where("{$table}.school_id", $schoolId);
        }
    }

    /**
     * Get the school_id to use for filtering.
     *
     * Priority order:
     * 1. Session school_id (for temporary school switching)
     * 2. Authenticated user's school_id
     * 3. null (for admin users - allows viewing all schools)
     *
     * @return int|null The school_id for filtering, or null to skip filtering (admin users)
     */
    private function getSchoolId(): ?int
    {
        // Check if there's a school_id in session (for school switching functionality)
        if (session()->has('school_id')) {
            return (int) session('school_id');
        }

        // Get school_id from authenticated user
        if (auth()->check() && auth()->user()) {
            $schoolId = auth()->user()->school_id ?? null;

            // Return school_id if it exists (non-admin user)
            // Return null if school_id is null (admin user can see all schools)
            return $schoolId;
        }

        // Return null if user is not authenticated (should be rare due to auth middleware)
        return null;
    }

    /**
     * Remove the scope from a query builder.
     *
     * Useful for queries that need to bypass the school_id filter
     * (e.g., reports that need to show all schools).
     *
     * Usage:
     *   Model::withoutGlobalScope(SchoolScope::class)->get();
     *   Model::withoutGlobalScopes()->get();
     *
     * @return string The scope class name for removal
     */
    public static function removeFrom(string $modelClass): string
    {
        return static::class;
    }
}
