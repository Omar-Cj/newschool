<?php

namespace App\Http\Controllers\Report;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Repositories\Academic\ClassesRepository;
use App\Repositories\Report\MarksheetRepository;
use App\Repositories\Academic\ClassSetupRepository;
use App\Repositories\StudentInfo\StudentRepository;
use App\Http\Requests\Report\ProgressCard\SearchRequest;
use App\Repositories\Examination\ExamAssignRepository;
use App\Repositories\Report\ProgressCardRepository;
use PDF;

class ProgressCardController extends Controller
{
    private $repo;
    private $examAssignRepo;
    private $classRepo;
    private $classSetupRepo;
    private $studentRepo;

    function __construct(
        ProgressCardRepository    $repo,
        ExamAssignRepository   $examAssignRepo,
        ClassesRepository      $classRepo,
        ClassSetupRepository   $classSetupRepo,
        StudentRepository      $studentRepo,
    ) 
    {
        $this->repo               = $repo;
        $this->examAssignRepo     = $examAssignRepo;
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
        return view('backend.report.progress-card', compact('data'));
    }

    public function getStudents(Request $request){
        return $this->studentRepo->getStudents($request);
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
        $data                 = $this->repo->search($request);
        $data['student']      = $this->studentRepo->show($request->student);
        $data['exam_types']   = $this->examAssignRepo->assignedExamType();
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

        // dd($data);
        return view('backend.report.progress-card', compact('data'));
    }
    
    public function generatePDF($session, $term, $class, $section, $student)
    {
        $request = new Request([
            'session'   => $session,
            'term'      => $term,
            'class'     => $class,
            'section'   => $section,
            'student'   => $student,
        ]);

        $data                 = $this->repo->search($request);
        $data['student']      = $this->studentRepo->show($request->student);

        $pdf = PDF::loadView('backend.report.progress-cardPDF', compact('data'));
        return $pdf->download('progress_card'.'_'.date('d_m_Y').'_'.@$data['student']->first_name .'_'. @$data['student']->last_name .'.pdf');
    }
}
