<?php

namespace App\Models\Examination;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * TermDefinition - Shared templates across all branches
 * Note: Does not extend BaseModel to avoid branch filtering
 * Templates are institution-wide and reusable across all branches
 */
class TermDefinition extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'code',
        'sequence',
        'typical_duration_weeks',
        'typical_start_month',
        'description',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'sequence' => 'integer',
        'typical_duration_weeks' => 'integer',
        'typical_start_month' => 'integer',
    ];

    /**
     * Get all term instances created from this definition
     */
    public function terms()
    {
        return $this->hasMany(Term::class);
    }

    /**
     * Get active term instances
     */
    public function activeTerms()
    {
        return $this->hasMany(Term::class)->where('status', 'active');
    }

    /**
     * Scope for active definitions
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope for ordered by sequence
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('sequence', 'asc');
    }

    /**
     * Get the latest term instance for this definition
     */
    public function latestTerm()
    {
        return $this->hasOne(Term::class)->latest('start_date');
    }

    /**
     * Check if this definition can be deleted
     */
    public function canBeDeleted()
    {
        return $this->terms()->count() == 0;
    }

    /**
     * Generate suggested dates for a new term
     */
    public function suggestDates($sessionYear)
    {
        // Check if there's a previous year's term
        $lastYearTerm = $this->terms()
            ->whereHas('session', function($query) use ($sessionYear) {
                $query->where('session', $sessionYear - 1);
            })
            ->first();

        if ($lastYearTerm) {
            return [
                'start_date' => $lastYearTerm->start_date->addYear()->toDateString(),
                'end_date' => $lastYearTerm->end_date->addYear()->toDateString(),
            ];
        }

        // Fallback to typical dates
        $startMonth = $this->typical_start_month ?: ($this->sequence * 3); // Default spacing
        $startDate = now()->year($sessionYear)->month($startMonth)->startOfMonth();
        $endDate = $startDate->copy()->addWeeks($this->typical_duration_weeks);

        return [
            'start_date' => $startDate->toDateString(),
            'end_date' => $endDate->toDateString(),
        ];
    }
}