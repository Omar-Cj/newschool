<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * ReportCategory Model
 *
 * Organizes reports into logical categories for better navigation and organization.
 *
 * @property int $id
 * @property string $name
 * @property string|null $module
 * @property string|null $icon
 * @property int $display_order
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 *
 * @property-read \Illuminate\Database\Eloquent\Collection<ReportCenter> $reports
 */
class ReportCategory extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'report_category';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = [
        'name',
        'module',
        'icon',
        'display_order',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'display_order' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get all reports belonging to this category.
     *
     * @return HasMany
     */
    public function reports(): HasMany
    {
        return $this->hasMany(ReportCenter::class, 'category_id', 'id');
    }

    /**
     * Scope to order categories by display_order.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('display_order', 'asc')->orderBy('name', 'asc');
    }

    /**
     * Scope to filter categories by module.
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
     * Get active reports count for this category.
     *
     * @return int
     */
    public function getActiveReportsCountAttribute(): int
    {
        return $this->reports()->active()->count();
    }
}
