<?php

namespace App\Http\Controllers\Academic;

use App\Http\Controllers\Controller;
use App\Models\Examination\Term;
use App\Models\Examination\TermDefinition;
use App\Models\Session;
use App\Repositories\Academic\TermRepository;
use App\Services\Academic\TermService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class TermController extends Controller
{
    protected $termRepository;
    protected $termService;

    public function __construct(TermRepository $termRepository, TermService $termService)
    {
        $this->termRepository = $termRepository;
        $this->termService = $termService;
    }

    /**
     * Display the terms management page
     */
    public function index()
    {
        $data['sessions'] = Session::where('status', 1)->orderBy('name', 'desc')->get();
        $data['termDefinitions'] = TermDefinition::active()->ordered()->get();
        $data['dashboardData'] = $this->termService->getDashboardData();

        return view('backend.examination.terms.index', $data);
    }

    /**
     * Get terms data for DataTables (AJAX)
     */
    public function ajaxData(Request $request)
    {
        if ($request->ajax()) {
            return $this->termRepository->getTermsAjaxData($request);
        }
        return response()->json(['error' => 'Invalid request'], 400);
    }

    /**
     * Display term definitions management
     */
    public function definitions()
    {
        return view('backend.examination.terms.definitions');
    }

    /**
     * Get term definitions data for DataTables (AJAX)
     */
    public function definitionsAjaxData(Request $request)
    {
        if ($request->ajax()) {
            return $this->termRepository->getTermDefinitionsAjaxData($request);
        }
        return response()->json(['error' => 'Invalid request'], 400);
    }

    /**
     * Store new term definition (AJAX)
     */
    public function storeDefinition(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:100',
            'code' => 'nullable|string|max:20|unique:term_definitions,code',
            'sequence' => 'required|integer|min:1',
            'typical_duration_weeks' => 'required|integer|min:1|max:52',
            'typical_start_month' => 'nullable|integer|min:1|max:12',
            'description' => 'nullable|string|max:500',
            'is_active' => 'boolean',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $definition = $this->termRepository->createTermDefinition($request->all());
            return response()->json([
                'success' => true,
                'message' => 'Term definition created successfully',
                'data' => $definition
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get term definition for editing (AJAX)
     */
    public function editDefinition($id)
    {
        try {
            $definition = TermDefinition::findOrFail($id);
            return response()->json([
                'success' => true,
                'data' => $definition
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Term definition not found'
            ], 404);
        }
    }

    /**
     * Update term definition (AJAX)
     */
    public function updateDefinition(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:100',
            'code' => 'nullable|string|max:20|unique:term_definitions,code,' . $id,
            'sequence' => 'required|integer|min:1',
            'typical_duration_weeks' => 'required|integer|min:1|max:52',
            'typical_start_month' => 'nullable|integer|min:1|max:12',
            'description' => 'nullable|string|max:500',
            'is_active' => 'boolean',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $definition = $this->termRepository->updateTermDefinition($id, $request->all());
            return response()->json([
                'success' => true,
                'message' => 'Term definition updated successfully',
                'data' => $definition
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete term definition (AJAX)
     */
    public function deleteDefinition($id)
    {
        try {
            $this->termRepository->deleteTermDefinition($id);
            return response()->json([
                'success' => true,
                'message' => 'Term definition deleted successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Open new term modal data (AJAX)
     */
    public function create(Request $request)
    {
        $sessionId = $request->session_id;
        $termDefinitionId = $request->term_definition_id;

        if ($termDefinitionId && $sessionId) {
            $definition = TermDefinition::find($termDefinitionId);
            $session = Session::find($sessionId);

            if ($definition && $session) {
                $suggestedDates = $definition->suggestDates($session->session);
                return response()->json([
                    'success' => true,
                    'suggested_dates' => $suggestedDates
                ]);
            }
        }

        return response()->json([
            'success' => true,
            'suggested_dates' => null
        ]);
    }

    /**
     * Store new term (AJAX)
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'term_definition_id' => 'required|exists:term_definitions,id',
            'session_id' => 'required|exists:sessions,id',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'notes' => 'nullable|string|max:1000',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $term = $this->termService->openTerm($request->all());
            return response()->json([
                'success' => true,
                'message' => 'Term opened successfully',
                'data' => $term->load(['termDefinition', 'session'])
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get term for editing (AJAX)
     */
    public function edit($id)
    {
        try {
            $term = Term::with(['termDefinition', 'session'])->findOrFail($id);

            if (!in_array($term->status, ['draft', 'upcoming'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'Only draft or upcoming terms can be edited'
                ], 400);
            }

            return response()->json([
                'success' => true,
                'data' => $term
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Term not found'
            ], 404);
        }
    }

    /**
     * Update term (AJAX)
     */
    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'notes' => 'nullable|string|max:1000',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $term = $this->termRepository->updateTerm($id, $request->all());
            return response()->json([
                'success' => true,
                'message' => 'Term updated successfully',
                'data' => $term->load(['termDefinition', 'session'])
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Close a term (AJAX)
     */
    public function close($id)
    {
        try {
            $term = $this->termRepository->closeTerm($id);
            return response()->json([
                'success' => true,
                'message' => 'Term closed successfully',
                'data' => $term
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Activate a term (AJAX)
     */
    public function activate($id)
    {
        try {
            $term = $this->termRepository->activateTerm($id);
            return response()->json([
                'success' => true,
                'message' => 'Term activated successfully',
                'data' => $term
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get term suggestions for a session (AJAX)
     */
    public function suggestions(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'session_id' => 'required|exists:sessions,id'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $suggestions = $this->termRepository->getTermSuggestions($request->session_id);
            return response()->json([
                'success' => true,
                'data' => $suggestions
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Bulk open terms (AJAX)
     */
    public function bulkOpen(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'session_id' => 'required|exists:sessions,id',
            'terms' => 'required|array',
            'terms.*.term_definition_id' => 'required|exists:term_definitions,id',
            'terms.*.start_date' => 'required|date',
            'terms.*.end_date' => 'required|date|after:terms.*.start_date',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $result = $this->termService->bulkOpenTerms($request->session_id, $request->terms);

            return response()->json([
                'success' => true,
                'message' => count($result['created']) . ' terms opened successfully',
                'data' => $result
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Clone terms from another session (AJAX)
     */
    public function cloneTerms(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'source_session_id' => 'required|exists:sessions,id',
            'target_session_id' => 'required|exists:sessions,id|different:source_session_id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $clonedTerms = $this->termRepository->cloneTermsFromSession(
                $request->source_session_id,
                $request->target_session_id
            );

            return response()->json([
                'success' => true,
                'message' => count($clonedTerms) . ' terms cloned successfully',
                'data' => $clonedTerms
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get term timeline for calendar view (AJAX)
     */
    public function timeline(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'session_id' => 'required|exists:sessions,id'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $timeline = $this->termRepository->getTermTimeline($request->session_id);
            return response()->json([
                'success' => true,
                'data' => $timeline
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get current term statistics (AJAX)
     */
    public function statistics()
    {
        try {
            $stats = $this->termService->getTermStatistics();
            return response()->json([
                'success' => true,
                'data' => $stats
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Validate term dates before submission (AJAX)
     */
    public function validateTermDates(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'term_definition_id' => 'required|exists:term_definitions,id',
            'session_id' => 'required|exists:sessions,id',
            'start_date' => 'required|date',
            'end_date' => 'required|date',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $errors = $this->termService->validateTermDates($request->all(), $request->term_id);

            if (!empty($errors)) {
                return response()->json([
                    'success' => false,
                    'message' => implode(', ', $errors)
                ], 400);
            }

            // Check sequence if needed
            if (!$request->has('ignore_sequence')) {
                $sequenceError = $this->termService->validateTermSequence(
                    $request->term_definition_id,
                    $request->session_id
                );

                if ($sequenceError) {
                    return response()->json([
                        'success' => false,
                        'message' => $sequenceError,
                        'sequence_warning' => true
                    ], 400);
                }
            }

            return response()->json([
                'success' => true,
                'message' => 'Validation passed'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Check if term can be deleted (AJAX validation)
     */
    public function checkDeletion($id)
    {
        try {
            $term = Term::with(['examEntries', 'termDefinition', 'session'])->findOrFail($id);
            $validation = $term->canBeDeleted();

            return response()->json([
                'success' => true,
                'can_delete' => $validation['can_delete'],
                'message' => $validation['reason'],
                'exam_entries_count' => $validation['exam_entries_count'] ?? 0,
                'results_count' => $validation['results_count'] ?? 0,
                'term_name' => $term->termDefinition->name ?? 'Unknown',
                'session_name' => $term->session->name ?? 'Unknown',
                'warning' => $term->getDeletionWarning()
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Term not found'
            ], 404);
        }
    }

    /**
     * Delete a term (AJAX)
     */
    public function destroy($id)
    {
        try {
            $this->termRepository->deleteTerm($id);

            return response()->json([
                'success' => true,
                'message' => 'Term deleted successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }
}