<?php

declare(strict_types=1);

namespace Modules\MainApp\Entities;

use App\Enums\Status;
use App\Models\PermissionFeature;
use Illuminate\Database\Eloquent\Model;
use Modules\MainApp\Entities\PackageChild;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;

class Package extends Model
{
    use HasFactory;

    protected $fillable = [];

    /**
     * Get package children.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function packageChilds(): HasMany
    {
        return $this->hasMany(PackageChild::class, 'package_id', 'id');
    }

    /**
     * Get permission features assigned to this package.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function permissionFeatures(): BelongsToMany
    {
        return $this->belongsToMany(
            PermissionFeature::class,
            'package_permission_features',
            'package_id',
            'permission_feature_id'
        )->withTimestamps();
    }

    /**
     * Scope a query to only include active packages.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('status', Status::ACTIVE);
    }

    /**
     * Get all allowed permission keywords for this package.
     *
     * @return \Illuminate\Support\Collection
     */
    public function getAllowedPermissions(): Collection
    {
        $cacheKey = "package_allowed_permissions_{$this->id}";

        return Cache::remember($cacheKey, now()->addHours(24), function () {
            return $this->permissionFeatures()
                ->active()
                ->with('permission')
                ->get()
                ->pluck('permission.attribute')
                ->filter()
                ->unique()
                ->values();
        });
    }

    /**
     * Check if package has a specific feature.
     *
     * @param string $permissionAttribute
     * @return bool
     */
    public function hasFeature(string $permissionAttribute): bool
    {
        $allowedPermissions = $this->getAllowedPermissions();
        return $allowedPermissions->contains($permissionAttribute);
    }

    /**
     * Sync features with cache clearing.
     *
     * @param array<int> $featureIds
     * @return void
     */
    public function syncFeatures(array $featureIds): void
    {
        $this->permissionFeatures()->sync($featureIds);

        // Clear caches
        Cache::forget("package_allowed_permissions_{$this->id}");
        Cache::forget("package_features_organized_{$this->id}");
        Cache::forget("package_features_{$this->id}");

        // Clear school caches for schools using this package
        $schools = School::where('package_id', $this->id)->get();
        foreach ($schools as $school) {
            if (method_exists($school, 'clearFeatureCache')) {
                $school->clearFeatureCache();
            }
        }
    }
}
