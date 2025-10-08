<?php

namespace App\Http\Controllers\Report;

use App\Models\MarkSheetApproval;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Repositories\Academic\ClassesRepository;
use App\Repositories\Report\MarksheetRepository;
use App\Repositories\Academic\ClassSetupRepository;
use App\Repositories\StudentInfo\StudentRepository;
use App\Http\Requests\Report\Marksheet\SearchRequest;
use PDF;

class MarksheetController extends Controller
{
    private $repo;
    private $classRepo;
    private $classSetupRepo;
    private $studentRepo;

    function __construct(
        MarksheetRepository    $repo,
        ClassesRepository      $classRepo,
        ClassSetupRepository   $classSetupRepo,
        StudentRepository      $studentRepo,
    ) {
        $this->repo               = $repo;
        $this->classRepo          = $classRepo;
        $this->classSetupRepo     = $classSetupRepo;
        $this->studentRepo        = $studentRepo;
    }

    public function index()
    {
        $data['sessions']           = \App\Models\Session::orderBy('id', 'desc')->get();
        $data['terms']              = [];
        $data['classes']            = $this->classRepo->assignedAll();
        $data['sections']           = [];
        $data['students']           = [];
        return view('backend.report.marksheet', compact('data'));
    }

    public function getStudents(Request $request)
    {
        $students = $this->studentRepo->getStudents($request);
        return response()->json($students, 200);
    }

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

    public function search(SearchRequest $request)
    {
        $data['student']      = $this->studentRepo->show($request->student);
        $data['resultData']   = $this->repo->search($request);
        $data['request']      = $request;
        $data['sessions']     = \App\Models\Session::orderBy('id', 'desc')->get();
        $data['terms']        = \App\Models\Examination\Term::where('session_id', $request->session)
                                    ->whereIn('status', ['active', 'closed'])
                                    ->with('termDefinition')
                                    ->orderBy('id', 'asc')
                                    ->get();
        $data['classes']      = $this->classRepo->assignedAll();
        $data['sections']     = $this->classSetupRepo->getSections($request->class);
        $data['students']     = $this->studentRepo->getStudents($request);

        $markSheetApproval = MarkSheetApproval::where([
            'student_id' => $request->student,
            'classes_id' => $request->class,
            'section_id' => $request->section,
            'exam_type_id' => $request->exam_type,
        ])->first();

        $data['markSheetApproval'] = $markSheetApproval;

        // Load exam type for display
        $data['examType'] = \App\Models\Examination\ExamType::find($request->exam_type);

        return view('backend.report.marksheet', compact('data'));
    }

    public function generatePDF($id, $type, $class, $section, $session, $term)
    {
        $request = new Request([
            'student'   => $id,
            'exam_type' => $type,
            'class'     => $class,
            'section'   => $section,
            'session'   => $session,
            'term'      => $term,
        ]);

        $data['student']      = $this->studentRepo->show($request->student);
        $data['resultData']   = $this->repo->search($request);

        // Load exam type for display
        $data['examType'] = \App\Models\Examination\ExamType::find($type);

        $pdf = PDF::loadView('backend.report.marksheetPDF', compact('data'));
        return $pdf->download('marksheet' . '_' . date('d_m_Y') . '_' . @$data['student']->first_name . '_' . @$data['student']->last_name . '.pdf');
    }
}
