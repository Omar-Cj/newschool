<?php

namespace App\Http\Controllers\ParentPanel;

use App\Http\Controllers\Controller;
use App\Models\MarkSheetApproval;
use Illuminate\Http\Request;
use App\Models\StudentInfo\SessionClassStudent;
use App\Models\StudentInfo\Student;
use App\Models\StudentInfo\ParentGuardian;
use App\Models\Examination\ExamEntry;
use App\Repositories\Report\ExamRoutineRepository;
use App\Repositories\ParentPanel\MarksheetRepository;
use App\Repositories\Report\MarksheetRepository as ReportMarksheetRepository;
use Illuminate\Support\Facades\Auth;
use App\Repositories\Examination\ExamAssignRepository;
use App\Repositories\StudentInfo\StudentRepository;
use Illuminate\Support\Facades\Session;
use PDF;

class MarksheetController extends Controller
{
    private $repo;
    private $typeRepo;
    private $reportMarksheetRepo;
    private $studentRepo;

    function __construct(
        MarksheetRepository $repo,
        ExamAssignRepository $typeRepo,
        ReportMarksheetRepository $reportMarksheetRepo,
        StudentRepository $studentRepo,
    )
    {
        $this->repo = $repo;
        $this->typeRepo = $typeRepo;
        $this->reportMarksheetRepo = $reportMarksheetRepo;
        $this->studentRepo = $studentRepo;
    }

    public function getExamTypes(Request $request)
    {
        return $this->typeRepo->getExamType($this->repo->studentInfo($request->id)); // student id
    }

    /**
     * Get published exam types for a specific session and term
     * Filters exam_entries by session, term, class, section and published status
     */
    public function getExamTypesByTerm(Request $request)
    {
        try {
            $sessionId = $request->session_id;
            $termId = $request->term_id;
            $studentId = $request->student_id;

            // Validate required parameters
            if (!$sessionId || !$termId || !$studentId) {
                return response()->json([], 200);
            }

            // Get student's class and section for the selected session
            $classSection = SessionClassStudent::where('session_id', $sessionId)
                ->where('student_id', $studentId)
                ->first();

            if (!$classSection) {
                return response()->json([], 200);
            }

            // Query exam_entries for published exams only
            $examTypes = ExamEntry::where('session_id', $sessionId)
                ->where('term_id', $termId)
                ->where('class_id', $classSection->classes_id)
                ->where('section_id', $classSection->section_id)
                ->where('status', 'published')  // Critical: Only show published exams
                ->whereNotNull('published_at')  // Additional safety check
                ->with('examType')  // Eager load exam type relationship
                ->select('exam_type_id')
                ->distinct()
                ->get()
                ->map(function($entry) {
                    return [
                        'id' => $entry->exam_type_id,
                        'name' => $entry->examType->name ?? 'Unknown Exam Type',
                    ];
                })
                ->filter(function($examType) {
                    return $examType['id'] !== null; // Remove null exam types
                })
                ->values(); // Reset array keys

            return response()->json($examTypes, 200);

        } catch (\Exception $e) {
            \Log::error('Error fetching exam types for parent panel: ' . $e->getMessage());
            return response()->json([], 200);
        }
    }

    /**
     * Get all sessions for parent panel dropdown
     */
    public function getSessions(Request $request)
    {
        try {
            $sessions = \App\Models\Session::orderBy('id', 'desc')->get()->map(function($session) {
                return [
                    'id' => $session->id,
                    'name' => $session->name,
                ];
            });

            return response()->json($sessions, 200);

        } catch (\Exception $e) {
            \Log::error('Error fetching sessions for parent panel: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to load sessions'], 500);
        }
    }

    /**
     * Get terms for a specific session
     * Only returns active or closed terms for parent visibility
     */
    public function getTermsBySession(Request $request, $sessionId)
    {
        try {
            $terms = \App\Models\Examination\Term::where('session_id', $sessionId)
                ->whereIn('status', ['active', 'closed'])
                ->with('termDefinition')
                ->orderBy('id', 'asc')
                ->get()
                ->map(function($term) {
                    return [
                        'id' => $term->id,
                        'name' => $term->termDefinition->name ?? 'Term ' . $term->id,
                    ];
                });

            return response()->json($terms, 200);

        } catch (\Exception $e) {
            \Log::error('Error fetching terms for parent panel: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to load terms'], 500);
        }
    }

    public function index()
    {
        $data                 = $this->repo->index();
        return view('parent-panel.marksheet', compact('data'));
    }

    public function search(Request $request)
    {
        // Validate required fields
        $request->validate([
            'session' => 'required|exists:sessions,id',
            'term' => 'required|exists:terms,id',
            'student' => 'required|exists:students,id',
            'exam_type' => 'required|exists:exam_types,id',
        ]);

        // Get student's class and section
        $classSection = SessionClassStudent::where('session_id', $request->session)
            ->where('student_id', $request->student)
            ->first();

        if (!$classSection) {
            return redirect()->back()->with('danger', 'Student class information not found');
        }

        // Verify exam entry is published - CRITICAL SECURITY CHECK
        $examEntry = ExamEntry::where('session_id', $request->session)
            ->where('term_id', $request->term)
            ->where('class_id', $classSection->classes_id)
            ->where('section_id', $classSection->section_id)
            ->where('exam_type_id', $request->exam_type)
            ->where('status', 'published')
            ->whereNotNull('published_at')
            ->first();

        if (!$examEntry) {
            return redirect()->back()->with('danger', 'Selected exam is not available or not yet published');
        }

        $data               = $this->repo->search($request);

        // Handle repository failure gracefully
        if ($data === false || !is_array($data)) {
            // Log error for debugging
            \Log::error('Marksheet repository search failed', [
                'student_id' => $request->student,
                'session_id' => $request->session,
                'term_id' => $request->term,
                'exam_type_id' => $request->exam_type,
                'class_id' => $classSection->classes_id,
                'section_id' => $classSection->section_id
            ]);

            // Reload students list for form display
            $parent = ParentGuardian::where('user_id', Auth::user()->id)->first();
            $data = [];
            $data['students'] = Student::where('parent_guardian_id', $parent->id)->get();

            return redirect()->back()
                ->with('danger', 'Failed to load marksheet. Please check if results have been entered for this exam.')
                ->withInput();
        }

        // Ensure students array is always available for form display
        if (!isset($data['students'])) {
            $parent = ParentGuardian::where('user_id', Auth::user()->id)->first();
            $data['students'] = Student::where('parent_guardian_id', $parent->id)->get();
        }

        $data['request']    = $request;

        // Load exam type for display
        $data['examType'] = \App\Models\Examination\ExamType::find($request->exam_type);

        return view('parent-panel.marksheet', compact('data','request'));
    }

    public function generatePDF($student, $type)
    {
        $student        = Student::where('id', $student)->first();
        $classSection   = SessionClassStudent::where('session_id', setting('session'))
                        ->where('student_id', @$student->id)
                        ->first();

        $request = new Request([
            'student'   => @$student->id,
            'exam_type' => $type,
            'class'     => $classSection->classes_id,
            'section'   => $classSection->section_id,
        ]);

        $data['student']      = $this->studentRepo->show(@$student->id);
        $data['resultData']   = $this->reportMarksheetRepo->search($request);

        // Load exam type for display
        $data['examType'] = \App\Models\Examination\ExamType::find($type);

        $pdf = PDF::loadView('backend.report.marksheetPDF', compact('data'));
        return $pdf->download('marksheet'.'_'.date('d_m_Y').'_'.@$data['student']->first_name .'_'. @$data['student']->last_name .'.pdf');
    }
}
