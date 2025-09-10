<?php

namespace App\Models\Fees;

use App\Models\BaseModel;
use App\Models\StudentService;
use App\Models\AcademicLevelConfig;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;

class FeesType extends BaseModel
{
    use HasFactory;

    protected $fillable = [
        'name',
        'code', 
        'description',
        'academic_level',
        'amount',
        'due_date_offset',
        'is_mandatory_for_level',
        'category',
        'status'
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'is_mandatory_for_level' => 'boolean',
        'due_date_offset' => 'integer',
        'status' => 'integer'
    ];

    // Relationships
    public function studentServices(): HasMany
    {
        return $this->hasMany(StudentService::class, 'fee_type_id');
    }

    public function activeStudentServices(): HasMany
    {
        return $this->hasMany(StudentService::class, 'fee_type_id')
                    ->where('is_active', true);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('status', \App\Enums\Status::ACTIVE);
    }

    public function scopeForAcademicLevel($query, string $level)
    {
        return $query->where(function($q) use ($level) {
            $q->where('academic_level', $level)
              ->orWhere('academic_level', 'all');
        });
    }

    public function scopeMandatoryForLevel($query, string $level)
    {
        return $query->forAcademicLevel($level)
                    ->where('is_mandatory_for_level', true);
    }

    public function scopeOptionalForLevel($query, string $level)
    {
        return $query->forAcademicLevel($level)
                    ->where('is_mandatory_for_level', false);
    }

    public function scopeByCategory($query, string $category)
    {
        return $query->where('category', $category);
    }

    public function scopeAcademic($query)
    {
        return $query->where('category', 'academic');
    }

    public function scopeTransport($query)
    {
        return $query->where('category', 'transport');
    }

    public function scopeMeal($query)
    {
        return $query->where('category', 'meal');
    }

    // Helper methods
    public function calculateDueDate(Carbon $termStart): Carbon
    {
        return $termStart->copy()->addDays($this->due_date_offset);
    }

    public function isApplicableFor(string $academicLevel): bool
    {
        return $this->academic_level === 'all' || $this->academic_level === $academicLevel;
    }

    public function isMandatoryFor(string $academicLevel): bool
    {
        return $this->isApplicableFor($academicLevel) && $this->is_mandatory_for_level;
    }

    public function isOptionalFor(string $academicLevel): bool
    {
        return $this->isApplicableFor($academicLevel) && !$this->is_mandatory_for_level;
    }

    public function getDisplayNameAttribute(): string
    {
        return $this->name . ($this->academic_level !== 'all' ? " ({$this->getFormattedAcademicLevel()})" : '');
    }

    public function getFormattedAcademicLevel(): string
    {
        return match($this->academic_level) {
            'primary' => 'Primary',
            'secondary' => 'Secondary', 
            'high_school' => 'High School',
            'kg' => 'Kindergarten',
            'all' => 'All Levels',
            default => ucfirst($this->academic_level)
        };
    }

    public function getFormattedCategory(): string
    {
        return match($this->category) {
            'academic' => 'Academic',
            'transport' => 'Transportation',
            'meal' => 'Meal Plan',
            'accommodation' => 'Accommodation',
            'activity' => 'Activities',
            'other' => 'Other',
            default => ucfirst($this->category)
        };
    }

    // Statistics and analytics
    public function getSubscribedStudentsCount(int $academicYearId = null): int
    {
        $query = $this->activeStudentServices();
        
        if ($academicYearId) {
            $query->where('academic_year_id', $academicYearId);
        }
        
        return $query->count();
    }

    public function getTotalRevenue(int $academicYearId = null): float
    {
        $query = $this->activeStudentServices();
        
        if ($academicYearId) {
            $query->where('academic_year_id', $academicYearId);
        }
        
        return $query->sum('final_amount');
    }

    public function getAverageDiscount(int $academicYearId = null): float
    {
        $query = $this->activeStudentServices();
        
        if ($academicYearId) {
            $query->where('academic_year_id', $academicYearId);
        }
        
        $services = $query->get();
        
        if ($services->isEmpty()) {
            return 0;
        }
        
        $totalDiscount = $services->sum(function($service) {
            return $service->amount - $service->final_amount;
        });
        
        return $totalDiscount / $services->count();
    }
}
