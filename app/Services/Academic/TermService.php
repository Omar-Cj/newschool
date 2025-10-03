<?php

namespace App\Services\Academic;

use App\Models\Examination\Term;
use App\Models\Examination\TermDefinition;
use App\Repositories\Academic\TermRepository;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class TermService
{
    protected $termRepository;

    public function __construct(TermRepository $termRepository)
    {
        $this->termRepository = $termRepository;
    }

    /**
     * Validate term dates
     */
    public function validateTermDates($data, $excludeId = null)
    {
        $errors = [];

        // Check if start date is before end date
        if (Carbon::parse($data['start_date'])->greaterThanOrEqualTo(Carbon::parse($data['end_date']))) {
            $errors[] = 'Start date must be before end date';
        }

        // Check for overlaps
        if (Term::hasOverlap($data['session_id'], $data['start_date'], $data['end_date'], $excludeId)) {
            $errors[] = 'Term dates overlap with existing term in this session';
        }

        // Check if dates are within session year (assuming session year is the year value)
        $session = \App\Models\Session::find($data['session_id']);
        if ($session) {
            $startYear = Carbon::parse($data['start_date'])->year;
            $endYear = Carbon::parse($data['end_date'])->year;
            $sessionYear = intval($session->name);

            // Allow terms to span across year boundaries (e.g., Sep 2024 - Jan 2025)
            if (abs($startYear - $sessionYear) > 1 || abs($endYear - $sessionYear) > 1) {
                $errors[] = 'Term dates should be within the academic session year';
            }
        }

        return $errors;
    }

    /**
     * Check if term can be opened in sequence
     */
    public function validateTermSequence($termDefinitionId, $sessionId)
    {
        $definition = TermDefinition::findOrFail($termDefinitionId);
        $previousSequence = $definition->sequence - 1;

        if ($previousSequence > 0) {
            // Check if previous term exists and is not draft
            $previousTermExists = Term::whereHas('termDefinition', function($q) use ($previousSequence) {
                    $q->where('sequence', $previousSequence);
                })
                ->where('session_id', $sessionId)
                ->whereNotIn('status', ['draft'])
                ->exists();

            if (!$previousTermExists) {
                // Get previous term definition name
                $previousDefinition = TermDefinition::where('sequence', $previousSequence)->first();
                if ($previousDefinition) {
                    return 'Please open "' . $previousDefinition->name . '" before opening this term';
                }
            }
        }

        return null; // No sequence error
    }

    /**
     * Open a new term with validations
     */
    public function openTerm($data)
    {
        // Validate dates
        $dateErrors = $this->validateTermDates($data);
        if (!empty($dateErrors)) {
            throw new \Exception(implode(', ', $dateErrors));
        }

        // Validate sequence (optional - can be disabled)
        $sequenceError = $this->validateTermSequence($data['term_definition_id'], $data['session_id']);
        if ($sequenceError && !isset($data['force_sequence'])) {
            throw new \Exception($sequenceError);
        }

        return DB::transaction(function() use ($data) {
            // Create the term
            $term = $this->termRepository->openTerm($data);

            // Log the action
            Log::info('Term opened', [
                'term_id' => $term->id,
                'user_id' => auth()->id(),
                'term_name' => $term->termDefinition->name,
                'session' => $term->session->name,
            ]);

            return $term;
        });
    }

    /**
     * Bulk open all terms for a session
     */
    public function bulkOpenTerms($sessionId, $termData)
    {
        $createdTerms = [];
        $errors = [];

        DB::transaction(function() use ($sessionId, $termData, &$createdTerms, &$errors) {
            foreach ($termData as $data) {
                try {
                    $data['session_id'] = $sessionId;
                    $createdTerms[] = $this->openTerm($data);
                } catch (\Exception $e) {
                    $definition = TermDefinition::find($data['term_definition_id']);
                    $errors[] = $definition->name . ': ' . $e->getMessage();
                }
            }
        });

        return [
            'created' => $createdTerms,
            'errors' => $errors,
        ];
    }

    /**
     * Get term statistics
     */
    public function getTermStatistics($termId = null)
    {
        if ($termId) {
            $term = Term::findOrFail($termId);
        } else {
            $term = Term::current();
        }

        if (!$term) {
            return null;
        }

        return [
            'term_id' => $term->id,
            'name' => $term->getDisplayName(),
            'status' => $term->status,
            'progress_percentage' => $term->getProgressPercentage(),
            'current_week' => $term->getCurrentWeek(),
            'total_weeks' => $term->actual_weeks,
            'days_remaining' => $term->status === 'active'
                ? max(0, now()->diffInDays($term->end_date, false))
                : null,
            'start_date' => $term->start_date->format('d M Y'),
            'end_date' => $term->end_date->format('d M Y'),
        ];
    }

    /**
     * Get dashboard data
     */
    public function getDashboardData()
    {
        $activeTerm = Term::current();
        $upcomingTerms = $this->termRepository->getUpcomingTerms();
        $currentSession = \App\Models\Session::where('status', 1)->first();

        $recentTerms = Term::with(['termDefinition', 'session'])
            ->orderBy('start_date', 'desc')
            ->limit(5)
            ->get();

        return [
            'active_term' => $activeTerm ? $this->getTermStatistics($activeTerm->id) : null,
            'upcoming_terms' => $upcomingTerms,
            'recent_terms' => $recentTerms,
            'current_session' => $currentSession,
            'term_definitions' => TermDefinition::active()->ordered()->get(),
        ];
    }

    /**
     * Generate term calendar for a session
     */
    public function generateTermCalendar($sessionId)
    {
        $terms = Term::where('session_id', $sessionId)
            ->with('termDefinition')
            ->orderBy('start_date')
            ->get();

        $events = [];

        foreach ($terms as $term) {
            // Add term period
            $events[] = [
                'title' => $term->termDefinition->name,
                'start' => $term->start_date->toDateString(),
                'end' => $term->end_date->addDay()->toDateString(), // FullCalendar end is exclusive
                'color' => $this->getTermColor($term->status),
                'allDay' => true,
                'extendedProps' => [
                    'term_id' => $term->id,
                    'status' => $term->status,
                    'weeks' => $term->actual_weeks,
                ],
            ];

            // Add milestones
            if ($term->status === 'active' || $term->status === 'upcoming') {
                // Mid-term marker
                $midDate = $term->start_date->copy()->addWeeks(intval($term->actual_weeks / 2));
                $events[] = [
                    'title' => $term->termDefinition->name . ' - Midterm',
                    'start' => $midDate->toDateString(),
                    'color' => '#ffc107',
                    'allDay' => true,
                ];
            }
        }

        return $events;
    }

    /**
     * Get color for term status
     */
    private function getTermColor($status)
    {
        return match($status) {
            'active' => '#28a745',
            'upcoming' => '#17a2b8',
            'closed' => '#6c757d',
            'draft' => '#e0e0e0',
            default => '#6c757d',
        };
    }

    /**
     * Check and update term statuses (for cron job)
     */
    public function updateTermStatuses()
    {
        $this->termRepository->updateTermStatuses();

        // Send notifications for status changes
        $this->sendTermStatusNotifications();
    }

    /**
     * Send notifications for term status changes
     */
    private function sendTermStatusNotifications()
    {
        // Get terms that will end soon (7 days)
        $endingSoon = Term::active()
            ->whereDate('end_date', '<=', now()->addDays(7))
            ->whereDate('end_date', '>', now())
            ->get();

        foreach ($endingSoon as $term) {
            // Send notification logic here
            Log::info('Term ending soon notification', [
                'term' => $term->getDisplayName(),
                'end_date' => $term->end_date->format('d M Y'),
            ]);
        }
    }
}