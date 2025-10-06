<?php

namespace App\Repositories\Academic;

use App\Models\Examination\Term;
use App\Models\Examination\TermDefinition;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class TermRepository
{
    /**
     * Get all term definitions for DataTables
     */
    public function getTermDefinitionsAjaxData($request)
    {
        // DataTables parameters
        $draw = intval($request->input('draw'));
        $start = intval($request->input('start'));
        $length = intval($request->input('length'));
        $searchValue = $request->input('search.value');
        $orderColumn = $request->input('order.0.column');
        $orderDir = $request->input('order.0.dir', 'asc');

        // Base query with relationship count
        $query = TermDefinition::withCount('terms');

        // Count total records
        $totalRecords = TermDefinition::count();

        // Apply search filter
        if (!empty($searchValue)) {
            $query->where(function($q) use ($searchValue) {
                $q->where('name', 'LIKE', "%{$searchValue}%")
                  ->orWhere('code', 'LIKE', "%{$searchValue}%")
                  ->orWhere('description', 'LIKE', "%{$searchValue}%");
            });
        }

        // Count filtered records
        $filteredRecords = $query->count();

        // Apply ordering
        $columns = ['id', 'name', 'code', 'sequence', 'typical_duration_weeks', 'is_active'];
        if (isset($columns[$orderColumn])) {
            $query->orderBy($columns[$orderColumn], $orderDir);
        } else {
            $query->orderBy('sequence', 'asc');
        }

        // Apply pagination
        $definitions = $query->offset($start)->limit($length)->get();

        // Format data for DataTables
        $data = [];
        $key = $start + 1;

        foreach ($definitions as $row) {
            // Status badge
            $badge = $row->is_active ? 'success' : 'danger';
            $text = $row->is_active ? 'Active' : 'Inactive';
            $statusBadge = '<span class="badge bg-'.$badge.'">'.$text.'</span>';

            // Terms count (use cached count from withCount)
            $termsCount = $row->terms_count ?? 0;

            // Actions
            $action = '<div class="btn-group" role="group">';
            $action .= '<button type="button" class="btn btn-sm btn-primary edit-definition" data-id="'.$row->id.'" title="Edit">
                        <i class="fas fa-edit"></i></button>';

            // Delete button - always shown, validation happens on click
            $action .= '<button type="button" class="btn btn-sm btn-danger delete-definition"
                        data-id="'.$row->id.'"
                        data-name="'.htmlspecialchars($row->name, ENT_QUOTES).'"
                        data-terms-count="'.$termsCount.'"
                        title="Delete">
                        <i class="fas fa-trash"></i></button>';

            $action .= '</div>';

            $data[] = [
                'DT_RowIndex' => $key++,
                'name' => $row->name,
                'code' => $row->code ?? '-',
                'sequence' => $row->sequence,
                'typical_duration_weeks' => $row->typical_duration_weeks,
                'typical_start_month' => $row->typical_start_month,
                'terms_count' => $termsCount,
                'status_badge' => $statusBadge,
                'action' => $action
            ];
        }

        return [
            'draw' => $draw,
            'recordsTotal' => $totalRecords,
            'recordsFiltered' => $filteredRecords,
            'data' => $data
        ];
    }

    /**
     * Get all terms for DataTables with filters
     */
    public function getTermsAjaxData($request)
    {
        // DataTables parameters
        $draw = intval($request->input('draw'));
        $start = intval($request->input('start'));
        $length = intval($request->input('length'));
        $searchValue = $request->input('search.value');
        $orderColumn = $request->input('order.0.column');
        $orderDir = $request->input('order.0.dir', 'asc');

        // Custom filter parameters
        $sessionFilter = $request->input('session_id');
        $statusFilter = $request->input('status');
        $termDefinitionFilter = $request->input('term_definition_id');

        // Base query with relationships
        $with = ['termDefinition', 'session', 'openedBy', 'examEntries'];

        // Add branch relationship if MultiBranch module is enabled
        if (hasModule('MultiBranch')) {
            $with[] = 'branch';
        }

        $query = Term::with($with);

        // Apply filters
        if (!empty($sessionFilter)) {
            $query->where('session_id', $sessionFilter);
        }

        if (!empty($statusFilter)) {
            $query->where('status', $statusFilter);
        }

        if (!empty($termDefinitionFilter)) {
            $query->where('term_definition_id', $termDefinitionFilter);
        }

        // Apply DataTables global search
        if (!empty($searchValue)) {
            $query->where(function($q) use ($searchValue) {
                $q->whereHas('termDefinition', function($q2) use ($searchValue) {
                    $q2->where('name', 'LIKE', "%{$searchValue}%");
                })
                ->orWhereHas('session', function($q2) use ($searchValue) {
                    $q2->where('name', 'LIKE', "%{$searchValue}%");
                })
                ->orWhere('status', 'LIKE', "%{$searchValue}%")
                ->orWhere('notes', 'LIKE', "%{$searchValue}%");
            });
        }

        // Count total and filtered records
        $totalQuery = Term::query();
        if (!empty($sessionFilter)) {
            $totalQuery->where('session_id', $sessionFilter);
        }
        if (!empty($statusFilter)) {
            $totalQuery->where('status', $statusFilter);
        }
        if (!empty($termDefinitionFilter)) {
            $totalQuery->where('term_definition_id', $termDefinitionFilter);
        }

        $totalRecords = $totalQuery->count();
        $filteredRecords = $query->count();

        // Apply ordering
        $columns = ['id', 'term_name', 'session_name', 'date_range', 'duration', 'progress', 'status_badge', 'action'];
        if ($orderColumn == 1 || $orderColumn == 2) {
            // For term_name and session_name, order by start_date
            $query->orderBy('start_date', $orderDir);
        } else if ($orderColumn == 3) {
            // For date_range, order by start_date
            $query->orderBy('start_date', $orderDir);
        } else if ($orderColumn == 6) {
            // For status
            $query->orderBy('status', $orderDir);
        } else {
            $query->orderBy('start_date', 'desc');
        }

        // Apply pagination
        $terms = $query->offset($start)->limit($length)->get();

        // Format data for DataTables
        $data = [];
        $key = $start + 1;

        foreach ($terms as $row) {
            // Term name
            $termName = $row->termDefinition ? $row->termDefinition->name : 'N/A';

            // Session name
            $sessionName = $row->session ? $row->session->name : 'N/A';

            // Branch name (if MultiBranch module is enabled)
            $branchName = 'N/A';
            if (hasModule('MultiBranch') && $row->branch) {
                $branchName = $row->branch->name;
            }

            // Date range
            if ($row->start_date && $row->end_date) {
                $dateRange = $row->start_date->format('d M Y') . ' - ' . $row->end_date->format('d M Y');
            } else {
                $dateRange = 'N/A';
            }

            // Duration
            $duration = $row->actual_weeks ? $row->actual_weeks . ' weeks' : 'N/A';

            // Progress
            $progress = '-';
            if ($row->status === 'active' && $row->start_date && $row->end_date) {
                try {
                    $percentage = $row->getProgressPercentage();
                    $week = $row->getCurrentWeek();
                    $progress = '
                        <div class="progress" style="height: 20px;">
                            <div class="progress-bar bg-primary" role="progressbar" style="width: '.$percentage.'%">'.$percentage.'%</div>
                        </div>
                        <small>Week '.$week.' of '.$row->actual_weeks.'</small>';
                } catch (\Exception $e) {
                    $progress = '-';
                }
            }

            // Status badge
            $badges = [
                'draft' => 'secondary',
                'upcoming' => 'info',
                'active' => 'success',
                'closed' => 'danger'
            ];
            $badge = $badges[$row->status] ?? 'secondary';
            $statusBadge = '<span class="badge bg-'.$badge.'">'.ucfirst($row->status).'</span>';

            // Actions
            $action = '<div class="btn-group" role="group">';

            // Edit button - always shown, validation happens on click
            $action .= '<button type="button" class="btn btn-sm btn-primary edit-term" data-id="'.$row->id.'" title="Edit">
                        <i class="fas fa-edit"></i></button>';

            if ($row->status === 'active') {
                $action .= '<button type="button" class="btn btn-sm btn-warning close-term" data-id="'.$row->id.'" title="Close Term">
                            <i class="fas fa-lock"></i></button>';
            }

            if ($row->status === 'upcoming' && $row->start_date && $row->end_date) {
                try {
                    if ($row->shouldActivate()) {
                        $action .= '<button type="button" class="btn btn-sm btn-success activate-term" data-id="'.$row->id.'" title="Activate">
                                    <i class="fas fa-play"></i></button>';
                    }
                } catch (\Exception $e) {
                    // Skip activate button if shouldActivate() fails
                }
            }

            $action .= '<button type="button" class="btn btn-sm btn-info view-term" data-id="'.$row->id.'" title="View Details">
                        <i class="fas fa-eye"></i></button>';

            // Delete button - always shown, validation happens on click
            $action .= '<button type="button" class="btn btn-sm btn-danger delete-term"
                        data-id="'.$row->id.'"
                        data-name="'.htmlspecialchars($termName, ENT_QUOTES).'"
                        data-session="'.htmlspecialchars($sessionName, ENT_QUOTES).'"
                        title="Delete Term">
                        <i class="fas fa-trash"></i></button>';

            $action .= '</div>';

            $data[] = [
                'DT_RowIndex' => $key++,
                'term_name' => $termName,
                'session_name' => $sessionName,
                'branch_name' => $branchName,
                'date_range' => $dateRange,
                'duration' => $duration,
                'progress' => $progress,
                'status_badge' => $statusBadge,
                'action' => $action
            ];
        }

        return [
            'draw' => $draw,
            'recordsTotal' => $totalRecords,
            'recordsFiltered' => $filteredRecords,
            'data' => $data
        ];
    }

    /**
     * Create a new term definition
     */
    public function createTermDefinition($data)
    {
        return TermDefinition::create($data);
    }

    /**
     * Update a term definition
     */
    public function updateTermDefinition($id, $data)
    {
        $definition = TermDefinition::findOrFail($id);
        $definition->update($data);
        return $definition;
    }

    /**
     * Delete a term definition
     */
    public function deleteTermDefinition($id)
    {
        $definition = TermDefinition::findOrFail($id);

        if (!$definition->canBeDeleted()) {
            $termsCount = $definition->terms()->count();
            throw new \Exception("Cannot delete term definition. {$termsCount} term(s) are created from this definition. Please delete those terms first.");
        }

        return $definition->delete();
    }

    /**
     * Delete a term
     *
     * @param int $id
     * @return bool
     * @throws \Exception
     */
    public function deleteTerm($id)
    {
        return DB::transaction(function () use ($id) {
            $term = Term::with(['examEntries'])->findOrFail($id);

            // Validate deletion eligibility
            $validation = $term->canBeDeleted();

            if (!$validation['can_delete']) {
                throw new \Exception($validation['reason']);
            }

            // Perform deletion
            $deleted = $term->delete();

            if (!$deleted) {
                throw new \Exception('Failed to delete term. Please try again.');
            }

            return true;
        });
    }

    /**
     * Open a new term
     */
    public function openTerm($data)
    {
        // Ensure branch_id is set (will be auto-set by BaseModel if not provided)
        if (!isset($data['branch_id']) && auth()->check()) {
            $data['branch_id'] = auth()->user()->branch_id ?? 1;
        }

        // Check for overlaps (automatically branch-scoped via BaseModel)
        if (Term::hasOverlap($data['session_id'], $data['start_date'], $data['end_date'])) {
            throw new \Exception('Term dates overlap with existing term in this session');
        }

        $startDate = Carbon::parse($data['start_date']);
        $endDate = Carbon::parse($data['end_date']);
        $now = now();

        // Determine initial status: manual draft override or auto-calculate
        if (isset($data['save_as_draft']) && $data['save_as_draft']) {
            // User explicitly chose to save as draft
            $data['status'] = 'draft';
        } else {
            // Auto-calculate status based on dates
            if ($now->lessThan($startDate)) {
                $data['status'] = 'upcoming';
            } elseif ($now->between($startDate, $endDate)) {
                $data['status'] = 'active';
                // Close any other active terms (branch-scoped automatically)
                Term::active()->update(['status' => 'closed']);
            } else {
                $data['status'] = 'closed';
            }
        }

        // Remove checkbox flag from data before saving to database
        unset($data['save_as_draft']);

        return Term::create($data);
    }

    /**
     * Update a term
     */
    public function updateTerm($id, $data)
    {
        $term = Term::findOrFail($id);

        // Check for overlaps if dates changed
        if (isset($data['start_date']) || isset($data['end_date'])) {
            $startDate = $data['start_date'] ?? $term->start_date;
            $endDate = $data['end_date'] ?? $term->end_date;

            if (Term::hasOverlap($term->session_id, $startDate, $endDate, $term->id)) {
                throw new \Exception('Term dates overlap with existing term in this session');
            }
        }

        $term->update($data);
        return $term;
    }

    /**
     * Close a term
     */
    public function closeTerm($id, $autoClose = false)
    {
        $term = Term::findOrFail($id);
        $term->close($autoClose);
        return $term;
    }

    /**
     * Activate a term
     */
    public function activateTerm($id)
    {
        $term = Term::findOrFail($id);
        $term->activate();
        return $term;
    }

    /**
     * Get active term
     */
    public function getActiveTerm()
    {
        return Term::current();
    }

    /**
     * Get upcoming terms
     */
    public function getUpcomingTerms()
    {
        return Term::upcoming()
            ->with(['termDefinition', 'session'])
            ->orderBy('start_date')
            ->get();
    }

    /**
     * Get term suggestions for a session
     */
    public function getTermSuggestions($sessionId)
    {
        $session = \App\Models\Session::findOrFail($sessionId);
        $definitions = TermDefinition::active()->ordered()->get();

        $suggestions = [];
        foreach ($definitions as $definition) {
            $suggestions[] = [
                'definition' => $definition,
                'suggested_dates' => $definition->suggestDates($session->session),
            ];
        }

        return $suggestions;
    }

    /**
     * Clone terms from previous session
     */
    public function cloneTermsFromSession($sourceSessionId, $targetSessionId)
    {
        $sourceTerms = Term::where('session_id', $sourceSessionId)->get();
        $clonedTerms = [];

        DB::transaction(function() use ($sourceTerms, $targetSessionId, &$clonedTerms) {
            foreach ($sourceTerms as $sourceTerm) {
                // Adjust dates by one year
                $startDate = $sourceTerm->start_date->addYear();
                $endDate = $sourceTerm->end_date->addYear();

                // Check for overlaps
                if (!Term::hasOverlap($targetSessionId, $startDate, $endDate)) {
                    $clonedTerms[] = Term::create([
                        'term_definition_id' => $sourceTerm->term_definition_id,
                        'session_id' => $targetSessionId,
                        'start_date' => $startDate,
                        'end_date' => $endDate,
                        'status' => 'upcoming',
                        'notes' => 'Cloned from session ' . $sourceTerm->session->session,
                    ]);
                }
            }
        });

        return $clonedTerms;
    }

    /**
     * Get term timeline for a session
     */
    public function getTermTimeline($sessionId)
    {
        $terms = Term::where('session_id', $sessionId)
            ->with('termDefinition')
            ->orderBy('start_date')
            ->get();

        return $terms->map(function($term) {
            return [
                'id' => $term->id,
                'title' => $term->termDefinition->name,
                'start' => $term->start_date->toDateString(),
                'end' => $term->end_date->toDateString(),
                'color' => $this->getStatusColor($term->status),
                'status' => $term->status,
            ];
        });
    }

    /**
     * Get color for term status
     */
    private function getStatusColor($status)
    {
        return match($status) {
            'active' => '#28a745',
            'upcoming' => '#17a2b8',
            'closed' => '#dc3545',
            'draft' => '#6c757d',
            default => '#6c757d',
        };
    }

    /**
     * Update term statuses automatically
     */
    public function updateTermStatuses()
    {
        // Activate terms that should be active
        Term::upcoming()
            ->get()
            ->filter(function($term) {
                return $term->shouldActivate();
            })
            ->each(function($term) {
                $term->activate();
            });

        // Close terms that should be closed
        Term::active()
            ->get()
            ->filter(function($term) {
                return $term->shouldAutoClose();
            })
            ->each(function($term) {
                $term->close(true);
            });
    }
}