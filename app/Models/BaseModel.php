<?php

namespace App\Models;

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use App\Scopes\SchoolScope;

class BaseModel extends Model
{
    protected static function boot()
    {
        parent::boot();

        // Apply school scope globally
        static::addGlobalScope(new SchoolScope());

        // Auto-set school_id from authenticated user's school
        static::creating(function ($model) {
            if (Schema::hasColumn($model->getTable(), 'school_id') && !$model->school_id) {
                $model->school_id = auth()->user()?->school_id;
            }
        });

        // Handle MultiBranch module if enabled
        if (hasModule('MultiBranch')) {
            static::addGlobalScope('branch_id', function (Builder $builder) {
                $table = $builder->getQuery()->from;
                $branchId = auth()->user()->branch_id ?? null;

                if ($branchId && Schema::hasColumn($table, 'branch_id')) {
                    $builder->where("{$table}.branch_id", $branchId);
                }
            });

            static::creating(function ($model) {
                $branchId = auth()->user()->branch_id ?? null;

                if (
                    $branchId &&
                    Schema::hasColumn($model->getTable(), 'branch_id')
                ) {
                    $model->branch_id = $branchId;
                }
            });
        }
    }
}
