<?php

namespace App\Http\Controllers\Backend\Examination;

use App\Http\Controllers\Controller;
use App\Models\Session;
use App\Models\Examination\Term;
use App\Models\Examination\ExamType;
use App\Models\Examination\ExamEntry;
use App\Models\Academic\Classes;
use App\Models\Academic\Section;
use App\Models\Academic\Subject;
use App\Repositories\Examination\ExamEntryRepository;
use App\Repositories\Academic\ClassesRepository;
use App\Repositories\Academic\ClassSetupRepository;
use App\Services\ExamEntryService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class ExamEntryController extends Controller
{
    protected $examEntryRepository;
    protected $examEntryService;
    protected $classesRepository;
    protected $classSetupRepository;

    public function __construct(
        ExamEntryRepository $examEntryRepository,
        ExamEntryService $examEntryService,
        ClassesRepository $classesRepository,
        ClassSetupRepository $classSetupRepository
    )
    {
        $this->examEntryRepository = $examEntryRepository;
        $this->examEntryService = $examEntryService;
        $this->classesRepository = $classesRepository;
        $this->classSetupRepository = $classSetupRepository;
    }

    /**
     * Display the exam entry management page
     */
    public function index()
    {
        if (!hasPermission('exam_entry_read')) {
            abort(403, 'Unauthorized access');
        }

        $data['sessions'] = Session::where('status', 1)->orderBy('name', 'desc')->get();
        $data['examTypes'] = ExamType::where('status', 1)->orderBy('name', 'asc')->get();
        $data['classes'] = $this->classesRepository->all();

        return view('backend.examination.exam_entry.index', $data);
    }

    /**
     * Get exam entries data for DataTables (AJAX)
     */
    public function ajaxData(Request $request)
    {
        if (!$request->ajax()) {
            return response()->json(['error' => 'Invalid request'], 400);
        }

        if (!hasPermission('exam_entry_read')) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        return $this->examEntryRepository->getAjaxData($request);
    }

    /**
     * Show create exam entry page
     */
    public function create()
    {
        if (!hasPermission('exam_entry_create')) {
            abort(403, 'Unauthorized access');
        }

        $data['sessions'] = Session::where('status', 1)->orderBy('name', 'desc')->get();
        $data['examTypes'] = ExamType::where('status', 1)->orderBy('name', 'asc')->get();
        $data['classes'] = $this->classesRepository->all();

        // Grade options matching student registration pattern
        $data['grades'] = [
            'KG-1' => 'KG-1',
            'KG-2' => 'KG-2',
            'Grade1' => 'Grade 1',
            'Grade2' => 'Grade 2',
            'Grade3' => 'Grade 3',
            'Grade4' => 'Grade 4',
            'Grade5' => 'Grade 5',
            'Grade6' => 'Grade 6',
            'Grade7' => 'Grade 7',
            'Grade8' => 'Grade 8',
            'Form1' => 'Form 1',
            'Form2' => 'Form 2',
            'Form3' => 'Form 3',
            'Form4' => 'Form 4',
        ];

        return view('backend.examination.exam_entry.create', $data);
    }

    /**
     * Store new exam entry (AJAX)
     */
    public function store(Request $request)
    {
        if (!hasPermission('exam_entry_create')) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized access'
            ], 403);
        }

        $validator = Validator::make($request->all(), [
            'session_id' => 'required|exists:sessions,id',
            'term_id' => 'required|exists:terms,id',
            'grade' => 'nullable|string|max:50',
            'class_id' => 'required|exists:classes,id',
            'section_id' => 'required|exists:sections,id',
            'exam_type_id' => 'required|exists:exam_types,id',
            'subject_id' => 'required',
            'total_marks' => 'required|numeric|min:1|max:1000',
            'entry_method' => 'required|in:manual,excel',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            // Validate exam entry doesn't already exist
            $validation = $this->examEntryService->validateExamEntry($request->all());

            if (!$validation['valid']) {
                return response()->json([
                    'success' => false,
                    'message' => implode(', ', $validation['errors'])
                ], 400);
            }

            $result = $this->examEntryRepository->store($request);

            if ($result['status']) {
                return response()->json([
                    'success' => true,
                    'message' => $result['message'],
                    'data' => $result['data']
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => $result['message']
            ], 500);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Show exam entry details
     */
    public function show($id)
    {
        if (!hasPermission('exam_entry_read')) {
            abort(403, 'Unauthorized access');
        }

        try {
            $data['examEntry'] = $this->examEntryRepository->show($id);
            $data['statistics'] = $this->examEntryService->getStatistics($id);

            // Transform results from vertical to horizontal format
            $results = $data['examEntry']->results;

            // Get subjects from SubjectAssign (source of truth) instead of from results
            // This ensures ALL assigned subjects are shown, even if no marks exist
            if ($data['examEntry']->is_all_subjects) {
                // Get all subjects assigned to this class and section
                $subjectAssign = \App\Models\Academic\SubjectAssign::where('session_id', $data['examEntry']->session_id)
                    ->where('classes_id', $data['examEntry']->class_id)
                    ->where('section_id', $data['examEntry']->section_id)
                    ->first();

                if ($subjectAssign) {
                    $subjects = \App\Models\Academic\SubjectAssignChildren::where('subject_assign_id', $subjectAssign->id)
                        ->with('subject')
                        ->get()
                        ->pluck('subject')
                        ->filter(); // Remove any null values
                } else {
                    $subjects = collect();
                }
            } else {
                // Single subject exam
                $subjects = collect([$data['examEntry']->subject])->filter();
            }

            // Get unique students from results or from class enrollment
            $studentIds = $results->pluck('student_id')->unique();

            // If no results yet, get students from SessionClassStudent
            if ($studentIds->isEmpty()) {
                $sessionClassStudents = \App\Models\StudentInfo\SessionClassStudent::query()
                    ->where('session_id', $data['examEntry']->session_id)
                    ->where('classes_id', $data['examEntry']->class_id)
                    ->where('section_id', $data['examEntry']->section_id)
                    ->with('student')
                    ->get();

                $studentIds = $sessionClassStudents->pluck('student.id')->filter();
            }

            // Build student results structure
            $studentResults = $studentIds->map(function($studentId) use ($results, $subjects, $data) {
                // Get student info from results or load from database
                $firstResult = $results->where('student_id', $studentId)->first();

                if ($firstResult && $firstResult->student) {
                    $student = $firstResult->student;
                } else {
                    $student = \App\Models\StudentInfo\Student::find($studentId);
                }

                if (!$student) {
                    return null;
                }

                // Create student data structure
                $studentData = [
                    'id' => $student->id,
                    'name' => $student->full_name ?? $student->name,
                    'grade' => $student->grade ?? '-',
                    'class' => $data['examEntry']->class->name ?? '-',
                    'section' => $data['examEntry']->section->name ?? '-',
                    'marks' => []
                ];

                // Map marks by subject - include ALL subjects even if no marks
                foreach ($subjects as $subject) {
                    $result = $results->where('student_id', $studentId)
                                     ->where('subject_id', $subject->id)
                                     ->first();

                    $studentData['marks'][$subject->id] = [
                        'obtained_marks' => $result ? $result->obtained_marks : null,
                        'is_absent' => $result ? $result->is_absent : false,
                        'total_marks' => $data['examEntry']->total_marks
                    ];
                }

                return $studentData;
            })->filter()->values(); // Remove null entries and re-index

            $data['subjects'] = $subjects;
            $data['studentResults'] = $studentResults;

            return view('backend.examination.exam_entry.show', $data);
        } catch (\Exception $e) {
            abort(404, 'Exam entry not found');
        }
    }

    /**
     * Show edit exam entry page
     */
    public function edit($id)
    {
        if (!hasPermission('exam_entry_update')) {
            abort(403, 'Unauthorized access');
        }

        try {
            $examEntry = ExamEntry::with(['session', 'term', 'class', 'section', 'examType', 'subject', 'results'])->findOrFail($id);

            if (!in_array($examEntry->status, ['draft', 'completed'])) {
                return redirect()->route('exam-entry.index')
                    ->with('error', 'Only draft or completed entries can be edited');
            }

            $data['examEntry'] = $examEntry;
            $data['sessions'] = Session::where('status', 1)->orderBy('name', 'desc')->get();
            $data['examTypes'] = ExamType::where('status', 1)->orderBy('name', 'asc')->get();

            // Get students with subjects
            $studentsData = $this->examEntryRepository->getStudentsWithSubjects([
                'session_id' => $examEntry->session_id,
                'class_id' => $examEntry->class_id,
                'section_id' => $examEntry->section_id,
                'subject_id' => $examEntry->is_all_subjects ? 'all' : $examEntry->subject_id,
                'total_marks' => $examEntry->total_marks
            ]);

            $data['students'] = $studentsData['students'];
            $data['subjects'] = $studentsData['subjects'];

            return view('backend.examination.exam_entry.edit', $data);
        } catch (\Exception $e) {
            abort(404, 'Exam entry not found');
        }
    }

    /**
     * Update exam entry (AJAX)
     */
    public function update(Request $request, $id)
    {
        if (!hasPermission('exam_entry_update')) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized access'
            ], 403);
        }

        $validator = Validator::make($request->all(), [
            'total_marks' => 'nullable|numeric|min:1|max:1000',
            'status' => 'nullable|in:draft,completed,published',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $result = $this->examEntryRepository->update($request, $id);

            if ($result['status']) {
                return response()->json([
                    'success' => true,
                    'message' => $result['message']
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => $result['message']
            ], 500);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete exam entry (AJAX)
     * Only users with role_id 1 (Super Admin) or role_id 2 (Admin) can delete
     */
    public function destroy($id)
    {
        // Check if user has required role (1 or 2) AND exam_entry_delete permission
        $userRoleId = auth()->user()->role_id;

        if (!in_array($userRoleId, [1, 2])) {
            return response()->json([
                'success' => false,
                'message' => 'Only Super Admin and Admin can delete exam entries'
            ], 403);
        }

        if (!hasPermission('exam_entry_delete')) {
            return response()->json([
                'success' => false,
                'message' => 'You do not have permission to delete exam entries'
            ], 403);
        }

        try {
            $result = $this->examEntryRepository->destroy($id);

            if ($result['status']) {
                return response()->json([
                    'success' => true,
                    'message' => $result['message']
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => $result['message']
            ], 400);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Publish exam entry results (AJAX)
     * Only users with role_id 1 (Super Admin) or role_id 2 (Admin) can publish
     */
    public function publish($id)
    {
        Log::info('📝 PUBLISH REQUEST: Started', [
            'exam_entry_id' => $id,
            'user_id' => auth()->user()->id,
            'user_role' => auth()->user()->role_id,
            'user_name' => auth()->user()->name
        ]);

        // Check if user has required role (1 or 2) AND exam_entry_publish permission
        $userRoleId = auth()->user()->role_id;

        if (!in_array($userRoleId, [1, 2])) {
            Log::warning('❌ PUBLISH DENIED: Invalid role', [
                'user_role' => $userRoleId,
                'exam_entry_id' => $id
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Only Super Admin and Admin can publish exam entries'
            ], 403);
        }

        Log::info('✅ PUBLISH: Role check passed', ['role_id' => $userRoleId]);

        if (!hasPermission('exam_entry_publish')) {
            Log::warning('❌ PUBLISH DENIED: Permission check failed', [
                'user_role' => $userRoleId,
                'exam_entry_id' => $id,
                'has_permission' => hasPermission('exam_entry_publish')
            ]);
            return response()->json([
                'success' => false,
                'message' => 'You do not have permission to publish exam entries'
            ], 403);
        }

        Log::info('✅ PUBLISH: Permission check passed');

        try {
            Log::info('🔄 PUBLISH: Calling repository publish method', [
                'exam_entry_id' => $id
            ]);

            $result = $this->examEntryRepository->publish($id);

            if ($result['status']) {
                Log::info('✅ PUBLISH SUCCESS', [
                    'exam_entry_id' => $id,
                    'message' => $result['message']
                ]);
                return response()->json([
                    'success' => true,
                    'message' => $result['message']
                ]);
            }

            Log::warning('❌ PUBLISH FAILED: Repository returned failure', [
                'exam_entry_id' => $id,
                'message' => $result['message']
            ]);

            return response()->json([
                'success' => false,
                'message' => $result['message']
            ], 400);
        } catch (\Exception $e) {
            Log::error('💥 PUBLISH ERROR: Exception caught', [
                'exam_entry_id' => $id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get students with subjects for exam entry (AJAX)
     */
    public function getStudents(Request $request)
    {
        if (!$request->ajax()) {
            return response()->json(['error' => 'Invalid request'], 400);
        }

        $validator = Validator::make($request->all(), [
            'session_id' => 'required|exists:sessions,id',
            'class_id' => 'required|exists:classes,id',
            'section_id' => 'required|exists:sections,id',
            'subject_id' => 'required',
            'total_marks' => 'required|numeric|min:1',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $result = $this->examEntryRepository->getStudentsWithSubjects($request->all());

            return response()->json([
                'success' => true,
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
     * Download Excel template for exam entry (AJAX)
     */
    public function downloadTemplate(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'session_id' => 'required|exists:sessions,id',
            'class_id' => 'required|exists:classes,id',
            'section_id' => 'required|exists:sections,id',
            'subject_id' => 'required',
            'total_marks' => 'required|numeric|min:1',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            return $this->examEntryService->generateExcelTemplate($request->all());
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Upload and process Excel file for exam entry (AJAX)
     */
    public function uploadResults(Request $request)
    {
        if (!$request->ajax()) {
            return response()->json(['error' => 'Invalid request'], 400);
        }

        $validator = Validator::make($request->all(), [
            'exam_file' => 'required|file|mimes:xlsx,xls|max:5120', // 5MB max
            'session_id' => 'required|exists:sessions,id',
            'term_id' => 'required|exists:terms,id',
            'grade' => 'nullable|string|max:50',
            'class_id' => 'required|exists:classes,id',
            'section_id' => 'required|exists:sections,id',
            'exam_type_id' => 'required|exists:exam_types,id',
            'subject_id' => 'required',
            'total_marks' => 'required|numeric|min:1',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            // Validate exam entry doesn't already exist
            $validation = $this->examEntryService->validateExamEntry($request->all());

            if (!$validation['valid']) {
                return response()->json([
                    'success' => false,
                    'message' => implode(', ', $validation['errors'])
                ], 400);
            }

            // Process Excel upload
            $result = $this->examEntryService->processExcelUpload($request->file('exam_file'), $request->all());

            if (!$result['status']) {
                return response()->json([
                    'success' => false,
                    'message' => $result['message']
                ], 400);
            }

            // Store file path
            $filePath = $request->file('exam_file')->store('exam_entries', 'public');

            // Create exam entry
            $examEntry = ExamEntry::create([
                'session_id' => $request->session_id,
                'term_id' => $request->term_id,
                'grade' => $request->grade,
                'class_id' => $request->class_id,
                'section_id' => $request->section_id,
                'exam_type_id' => $request->exam_type_id,
                'subject_id' => $request->subject_id === 'all' ? null : $request->subject_id,
                'is_all_subjects' => $request->subject_id === 'all',
                'entry_method' => 'excel',
                'upload_file_path' => $filePath,
                'total_marks' => $request->total_marks,
                'status' => 'completed',
                'created_by' => auth()->id(),
            ]);

            // Store results
            $this->examEntryService->bulkStoreResults($examEntry->id, $result['results']->toArray());

            return response()->json([
                'success' => true,
                'message' => 'Exam results uploaded successfully',
                'data' => [
                    'exam_entry_id' => $examEntry->id,
                    'students_count' => $result['students']->count(),
                    'results_count' => $result['results']->count()
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get terms by session (AJAX) - for cascading dropdown
     */
    public function getTerms(Request $request)
    {
        if (!$request->ajax()) {
            return response()->json(['error' => 'Invalid request'], 400);
        }

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
            $terms = Term::where('session_id', $request->session_id)
                ->with('termDefinition')
                ->orderBy('start_date', 'asc')
                ->get()
                ->map(function($term) {
                    return [
                        'id' => $term->id,
                        'name' => $term->termDefinition->name ?? 'N/A'
                    ];
                });

            return response()->json([
                'success' => true,
                'data' => $terms
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get sections by class (AJAX) - for cascading dropdown
     */
    public function getSections(Request $request)
    {
        if (!$request->ajax()) {
            return response()->json(['error' => 'Invalid request'], 400);
        }

        $validator = Validator::make($request->all(), [
            'class_id' => 'required|exists:classes,id'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            // Use ClassSetupRepository to get sections for this class
            $sections = $this->classSetupRepository->getSections($request->class_id);

            // Map ClassSetupChildren to extract section data
            $sectionsData = $sections->map(function($item) {
                return [
                    'id' => $item->section->id,
                    'name' => $item->section->name
                ];
            });

            return response()->json([
                'success' => true,
                'data' => $sectionsData
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get subjects by class and section (AJAX) - for cascading dropdown
     */
    public function getSubjects(Request $request)
    {
        if (!$request->ajax()) {
            return response()->json(['error' => 'Invalid request'], 400);
        }

        $validator = Validator::make($request->all(), [
            'session_id' => 'required|exists:sessions,id',
            'class_id' => 'required|exists:classes,id',
            'section_id' => 'required|exists:sections,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            // Get assigned subjects for this class/section
            $subjectAssign = \App\Models\Academic\SubjectAssign::where('session_id', $request->session_id)
                ->where('classes_id', $request->class_id)
                ->where('section_id', $request->section_id)
                ->first();

            if (!$subjectAssign) {
                return response()->json([
                    'success' => true,
                    'data' => []
                ]);
            }

            $subjects = \App\Models\Academic\SubjectAssignChildren::where('subject_assign_id', $subjectAssign->id)
                ->with('subject')
                ->get()
                ->map(function($item) {
                    return [
                        'id' => $item->subject->id,
                        'name' => $item->subject->name
                    ];
                });

            // Add "All Subjects" option
            $subjects->prepend(['id' => 'all', 'name' => 'All Subjects']);

            return response()->json([
                'success' => true,
                'data' => $subjects
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Calculate and update grades (AJAX)
     */
    public function calculateGrades($id)
    {
        if (!hasPermission('exam_entry_update')) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized access'
            ], 403);
        }

        try {
            $result = $this->examEntryService->autoCalculateGrades($id);

            if ($result['status']) {
                return response()->json([
                    'success' => true,
                    'message' => $result['message']
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => $result['message']
            ], 500);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
