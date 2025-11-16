<?php

namespace App\Models\Transportation;

use App\Models\BaseModel;
use App\Enums\Status;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Bus Model
 *
 * Represents a school bus with driver and capacity information.
 * Provides automatic school_id and branch_id scoping through BaseModel.
 *
 * @property int $id
 * @property string $area_name
 * @property string $bus_number
 * @property int $capacity
 * @property string $driver_name
 * @property string $driver_phone
 * @property string $license_plate
 * @property int $status
 * @property int $branch_id
 * @property int $school_id
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 *
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\StudentInfo\Student[] $students
 * @property-read \Modules\MultiBranch\Entities\Branch $branch
 * @property-read int $students_count
 */
class Bus extends BaseModel
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = [
        'area_name',
        'bus_number',
        'capacity',
        'driver_name',
        'driver_phone',
        'license_plate',
        'status',
        'branch_id',
        'school_id',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'status' => 'integer',
        'capacity' => 'integer',
    ];

    /**
     * Get all students assigned to this bus.
     *
     * @return HasMany
     */
    public function students(): HasMany
    {
        return $this->hasMany(\App\Models\StudentInfo\Student::class, 'bus_id', 'id');
    }

    /**
     * Get the branch that owns the bus.
     *
     * @return BelongsTo
     */
    public function branch(): BelongsTo
    {
        return $this->belongsTo(\Modules\MultiBranch\Entities\Branch::class);
    }

    /**
     * Scope a query to only include active buses.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeActive($query)
    {
        return $query->where('status', Status::ACTIVE);
    }

    /**
     * Check if the bus is active.
     *
     * @return bool
     */
    public function isActive(): bool
    {
        return $this->status === Status::ACTIVE;
    }

    /**
     * Get the count of students assigned to this bus.
     *
     * This is an accessor attribute that can be accessed as $bus->students_count
     *
     * @return int
     */
    public function getStudentsCountAttribute(): int
    {
        return $this->students()->count();
    }

    /**
     * Check if the bus is at or over capacity.
     *
     * Compares the number of assigned students to the bus capacity.
     *
     * @return bool True if student count >= capacity, false otherwise
     */
    public function isAtCapacity(): bool
    {
        return $this->students()->count() >= $this->capacity;
    }
}
