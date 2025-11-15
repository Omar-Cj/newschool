<?php

namespace App\Models\Examination;

use App\Models\BaseModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * TermDefinition - School-specific term templates
 *
 * Each school defines its own term structure (e.g., 2-term, 3-term, 4-term year).
 * Term definitions are school-scoped but shared across all branches within a school.
 * This allows consistent academic calendar structure across the school's branches.
 */
class TermDefinition extends BaseModel
{
    use HasFactory;

    protected $fillable = [
        'school_id',
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