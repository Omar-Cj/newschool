<?php

namespace App\Models\Examination;

use App\Models\BaseModel;
use App\Models\Session;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Term extends BaseModel
{
    use HasFactory;

    protected $fillable = [
        'term_definition_id',
        'session_id',
        'branch_id',
        'start_date',
        'end_date',
        'status',
        'opened_by',
        'opened_at',
        'closed_at',
        'auto_closed',
        'holiday_count',
        'actual_weeks',
        'notes',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'opened_at' => 'datetime',
        'closed_at' => 'datetime',
        'auto_closed' => 'boolean',
        'holiday_count' => 'integer',
        'actual_weeks' => 'integer',
    ];

    /**
     * Boot the model
     */
    protected static function boot()
    {
        parent::boot();

        // Set opened_at when creating
        static::creating(function ($term) {
            if (!$term->opened_at) {
                $term->opened_at = now();
            }
            if (!$term->opened_by) {
                $term->opened_by = auth()->id();
            }
            // Calculate actual weeks
            $term->actual_weeks = $term->calculateWeeks();
        });

        // Update actual weeks when dates change
        static::updating(function ($term) {
            if ($term->isDirty(['start_date', 'end_date'])) {
                $term->actual_weeks = $term->calculateWeeks();
            }
        });
    }

    /**
     * Get the term definition
     */
    public function termDefinition()
    {
        return $this->belongsTo(TermDefinition::class);
    }

    /**
     * Get the academic session
     */
    public function session()
    {
        return $this->belongsTo(Session::class);
    }

    /**
     * Get the user who opened the term
     */
    public function openedBy()
    {
        return $this->belongsTo(User::class, 'opened_by');
    }

    /**
     * Get the branch this term belongs to
     */
    public function branch()
    {
        if (!hasModule('MultiBranch')) {
            return null;
        }
        return $this->belongsTo(\Modules\MultiBranch\Entities\Branch::class);
    }

    /**
     * Scope for active terms
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Scope for upcoming terms
     */
    public function scopeUpcoming($query)
    {
        return $query->where('status', 'upcoming');
    }

    /**
     * Scope for closed terms
     */
    public function scopeClosed($query)
    {
        return $query->where('status', 'closed');
    }

    /**
     * Scope for current session
     */
    public function scopeCurrentSession($query)
    {
        return $query->whereHas('session', function($q) {
            $q->where('status', 1); // Assuming active session has status 1
        });
    }

    /**
     * Get the current active term
     */
    public static function current()
    {
        return static::active()->currentSession()->first();
    }

    /**
     * Calculate number of weeks
     */
    public function calculateWeeks()
    {
        if (!$this->start_date || !$this->end_date) {
            return 0;
        }
        return max(1, $this->start_date->diffInWeeks($this->end_date));
    }

    /**
     * Get progress percentage
     */
    public function getProgressPercentage()
    {
        if ($this->status !== 'active') {
            return $this->status === 'closed' ? 100 : 0;
        }

        if (!$this->start_date || !$this->end_date) {
            return 0;
        }

        $totalDays = $this->start_date->diffInDays($this->end_date);
        $elapsedDays = $this->start_date->diffInDays(now());

        if ($elapsedDays >= $totalDays) {
            return 100;
        }

        return round(($elapsedDays / $totalDays) * 100);
    }

    /**
     * Get current week number
     */
    public function getCurrentWeek()
    {
        if ($this->status !== 'active') {
            return null;
        }

        if (!$this->start_date) {
            return null;
        }

        $weeksElapsed = $this->start_date->diffInWeeks(now());
        return min($weeksElapsed + 1, $this->actual_weeks ?: 1);
    }

    /**
     * Check if term should be auto-closed
     */
    public function shouldAutoClose()
    {
        if (!$this->end_date) {
            return false;
        }
        return $this->status === 'active' && now()->greaterThan($this->end_date);
    }

    /**
     * Check if term should be activated
     */
    public function shouldActivate()
    {
        if (!$this->start_date || !$this->end_date) {
            return false;
        }
        return $this->status === 'upcoming'
            && now()->greaterThanOrEqualTo($this->start_date)
            && now()->lessThanOrEqualTo($this->end_date);
    }

    /**
     * Close the term
     */
    public function close($autoClose = false)
    {
        $this->update([
            'status' => 'closed',
            'closed_at' => now(),
            'auto_closed' => $autoClose,
        ]);
    }

    /**
     * Activate the term
     */
    public function activate()
    {
        // Close any other active terms
        static::active()->where('id', '!=', $this->id)->update(['status' => 'closed']);

        $this->update(['status' => 'active']);
    }

    /**
     * Get display name with session
     */
    public function getDisplayName()
    {
        return $this->termDefinition->name . ' ' . $this->session->name;
    }

    /**
     * Check for overlapping terms
     */
    public static function hasOverlap($sessionId, $startDate, $endDate, $excludeId = null)
    {
        $query = static::where('session_id', $sessionId)
            ->where(function($q) use ($startDate, $endDate) {
                $q->whereBetween('start_date', [$startDate, $endDate])
                  ->orWhereBetween('end_date', [$startDate, $endDate])
                  ->orWhere(function($q2) use ($startDate, $endDate) {
                      $q2->where('start_date', '<=', $startDate)
                         ->where('end_date', '>=', $endDate);
                  });
            });

        if ($excludeId) {
            $query->where('id', '!=', $excludeId);
        }

        return $query->exists();
    }
}