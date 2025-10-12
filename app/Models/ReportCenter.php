<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * ReportCenter Model
 *
 * Master registry for all reports in the system. Each report is linked to a stored procedure
 * and can have multiple dynamic parameters.
 *
 * @property int $id
 * @property string $name
 * @property string|null $description
 * @property string|null $module
 * @property int|null $category_id
 * @property int $status
 * @property string $procedure_name
 * @property string $report_type
 * @property int $export_enabled
 * @property array|null $roles
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property \Carbon\Carbon|null $deleted_at
 *
 * @property-read ReportCategory|null $category
 * @property-read \Illuminate\Database\Eloquent\Collection<ReportParameter> $parameters
 */
class ReportCenter extends Model
{
    use SoftDeletes;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'report_center';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = [
        'name',
        'description',
        'module',
        'category_id',
        'status',
        'procedure_name',
        'report_type',
        'export_enabled',
        'roles',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'status' => 'integer',
        'category_id' => 'integer',
        'export_enabled' => 'integer',
        'roles' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array<string>
     */
    protected $hidden = [
        'deleted_at',
    ];

    /**
     * Get the category that owns the report.
     *
     * @return BelongsTo
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(ReportCategory::class, 'category_id', 'id');
    }

    /**
     * Get all parameters for this report.
     *
     * @return HasMany
     */
    public function parameters(): HasMany
    {
        return $this->hasMany(ReportParameter::class, 'report_id', 'id');
    }

    /**
     * Scope to get only active reports.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeActive($query)
    {
        return $query->where('status', 1);
    }

    /**
     * Scope to filter reports by module.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string $module
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeByModule($query, string $module)
    {
        return $query->where('module', $module);
    }

    /**
     * Scope to filter reports by category.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param int $categoryId
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeByCategory($query, int $categoryId)
    {
        return $query->where('category_id', $categoryId);
    }

    /**
     * Scope to filter reports by report type.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string $reportType
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeByType($query, string $reportType)
    {
        return $query->where('report_type', $reportType);
    }

    /**
     * Check if report is active.
     *
     * @return bool
     */
    public function isActive(): bool
    {
        return $this->status === 1;
    }

    /**
     * Check if export is enabled for this report.
     *
     * @return bool
     */
    public function isExportEnabled(): bool
    {
        return $this->export_enabled === 1;
    }

    /**
     * Check if a specific role can access this report.
     *
     * @param string $role
     * @return bool
     */
    public function canAccessByRole(string $role): bool
    {
        if (empty($this->roles)) {
            return true; // No restrictions
        }

        return in_array($role, $this->roles, true);
    }

    /**
     * Get ordered parameters for this report.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getOrderedParameters()
    {
        return $this->parameters()->ordered()->get();
    }

    /**
     * Get required parameters only.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getRequiredParameters()
    {
        return $this->parameters()->where('is_required', 1)->ordered()->get();
    }

    /**
     * Get optional parameters only.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getOptionalParameters()
    {
        return $this->parameters()->where('is_required', 0)->ordered()->get();
    }
}
