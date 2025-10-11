<?php

namespace App\Http\Controllers\Report;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Repositories\Academic\ClassesRepository;
use App\Repositories\Academic\ClassSetupRepository;
use App\Repositories\StudentInfo\StudentRepository;
use App\Repositories\Report\MarksheetRepository;
use App\Repositories\Report\ProgressCardRepository;
use App\Repositories\Examination\ExamAssignRepository;
use App\Http\Requests\Report\Marksheet\SearchRequest as MarksheetSearchRequest;
use App\Http\Requests\Report\ProgressCard\SearchRequest as ProgressCardSearchRequest;
use App\Models\MarkSheetApproval;
use PDF;

class ExaminationReportController extends Controller
{
    protected $classRepo;
    protected $classSetupRepo;
    protected $studentRepo;
    protected $marksheetRepo;
    protected $progressCardRepo;
    protected $examAssignRepo;

    public function __construct(
        ClassesRepository $classRepo,
        ClassSetupRepository $classSetupRepo,
        StudentRepository $studentRepo,
        MarksheetRepository $marksheetRepo,
        ProgressCardRepository $progressCardRepo,
        ExamAssignRepository $examAssignRepo
    ) {
        $this->classRepo = $classRepo;
        $this->classSetupRepo = $classSetupRepo;
        $this->studentRepo = $studentRepo;
        $this->marksheetRepo = $marksheetRepo;
        $this->progressCardRepo = $progressCardRepo;
        $this->examAssignRepo = $examAssignRepo;
    }

    /**
     * Display examination reports page with collapsibles
     */
    public function index()
    {
        $data['title'] = ___('settings.examination_reports');
        $data['sessions'] = \App\Models\Session::orderBy('id', 'desc')->get();
        $data['classes'] = $this->classRepo->assignedAll();
        $data['sections'] = [];
        $data['students'] = [];
        $data['terms'] = [];
        $data['report_type'] = 'none';

        return view('backend.report.examination-report', compact('data'));
    }

    /**
     * Search exam report (marksheet)
     */
    public function searchMarksheet(MarksheetSearchRequest $request)
    {
        try {
            // Reuse existing marksheet repository
            $data['student'] = $this->studentRepo->show($request->student);
            $data['resultData'] = $this->marksheetRepo->search($request);
            $data['request'] = $request;

            // Reload dropdown data
            $data['sessions'] = \App\Models\Session::orderBy('id', 'desc')->get();
            $data['terms'] = \App\Models\Examination\Term::where('session_id', $request->session)
                                ->whereIn('status', ['active', 'closed'])
                                ->with('termDefinition')
                                ->orderBy('id', 'asc')
                                ->get();
            $data['classes'] = $this->classRepo->assignedAll();
            $data['sections'] = $this->classSetupRepo->getSections($request->class);
            $data['students'] = $this->studentRepo->getStudents($request);

            // Get exam type for display
            $data['examType'] = \App\Models\Examination\ExamType::find($request->exam_type);

            // Check marksheet approval status
            $markSheetApproval = MarkSheetApproval::where([
                'student_id' => $request->student,
                'classes_id' => $request->class,
                'section_id' => $request->section,
                'exam_type_id' => $request->exam_type,
            ])->first();
            $data['markSheetApproval'] = $markSheetApproval;

            // Mark which collapsible to show
            $data['report_type'] = 'marksheet';
            $data['title'] = ___('settings.examination_reports');

            return view('backend.report.examination-report', compact('data'));
        } catch (\Exception $e) {
            return redirect()->back()->with('error', ___('common.something_went_wrong'));
        }
    }

    /**
     * Search progress card
     */
    public function searchProgressCard(ProgressCardSearchRequest $request)
    {
        try {
            // Reuse existing progress card repository
            $progressData = $this->progressCardRepo->search($request);
            $data = array_merge($progressData, [
                'student' => $this->studentRepo->show($request->student),
                'exam_types' => $this->examAssignRepo->assignedExamType(),
                'request' => $request,
            ]);

            // Reload dropdown data
            $data['sessions'] = \App\Models\Session::orderBy('id', 'desc')->get();
            $data['terms'] = \App\Models\Examination\Term::where('session_id', $request->session)
                                ->whereIn('status', ['active', 'closed'])
                                ->with('termDefinition')
                                ->orderBy('id', 'asc')
                                ->get();
            $data['classes'] = $this->classRepo->assignedAll();
            $data['sections'] = $this->classSetupRepo->getSections($request->class);
            $data['students'] = $this->studentRepo->getStudents($request);

            // Mark which collapsible to show
            $data['report_type'] = 'progress_card';
            $data['title'] = ___('settings.examination_reports');

            return view('backend.report.examination-report', compact('data'));
        } catch (\Exception $e) {
            return redirect()->back()->with('error', ___('common.something_went_wrong'));
        }
    }

    /**
     * Get students for AJAX calls
     */
    public function getStudents(Request $request)
    {
        try {
            $students = $this->studentRepo->getStudents($request);
            return response()->json($students, 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to load students'], 500);
        }
    }

    /**
     * Get terms for AJAX calls
     */
    public function getTerms(Request $request, $sessionId)
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
            return response()->json(['error' => 'Failed to load terms'], 500);
        }
    }
}
