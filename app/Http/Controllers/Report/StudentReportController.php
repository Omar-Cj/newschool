<?php

namespace App\Http\Controllers\Report;

use App\Http\Controllers\Controller;
use App\Repositories\Report\StudentReportRepository;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class StudentReportController extends Controller
{
    protected $repo;

    public function __construct(
        StudentReportRepository $repo
    ) {
        $this->repo = $repo;
    }

    /**
     * Display student reports page
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        // Load dropdown data
        $data['sessions'] = \App\Models\Session::orderBy('id', 'desc')->get();
        $data['classes'] = \App\Models\Academic\Classes::where('status', 1)->get();
        $data['grades'] = collect(\App\Models\StudentInfo\Student::getAllGrades())->map(function($grade) {
            return (object)['grade' => $grade];
        });
        $data['sections'] = \App\Models\Academic\Section::where('status', 1)->get();
        $data['shifts'] = \App\Models\Academic\Shift::where('status', 1)->get();
        $data['categories'] = \App\Models\StudentInfo\StudentCategory::where('status', 1)->get();
        $data['genders'] = \App\Models\Gender::where('status', 1)->get();

        return view('backend.report.student-report', compact('data'));
    }

    /**
     * Search student list report
     *
     * @param Request $request
     * @return \Illuminate\View\View
     */
    public function searchStudentList(Request $request)
    {
        // Validate request
        $request->validate([
            'session' => 'nullable|exists:sessions,id',
            'grade' => 'nullable|string',
            'class' => 'nullable|exists:classes,id',
            'section' => 'nullable|exists:sections,id',
            'shift' => 'nullable|exists:shifts,id',
            'category' => 'nullable|exists:student_categories,id',
            'status' => 'nullable|integer',
            'gender' => 'nullable|exists:genders,id'
        ]);

        // Get report data
        $data['reportData'] = $this->repo->getStudentList($request);

        // Reload dropdown data
        $data['sessions'] = \App\Models\Session::orderBy('id', 'desc')->get();
        $data['classes'] = \App\Models\Academic\Classes::where('status', 1)->get();
        $data['grades'] = collect(\App\Models\StudentInfo\Student::getAllGrades())->map(function($grade) {
            return (object)['grade' => $grade];
        });
        $data['sections'] = \App\Models\Academic\Section::where('status', 1)->get();
        $data['shifts'] = \App\Models\Academic\Shift::where('status', 1)->get();
        $data['categories'] = \App\Models\StudentInfo\StudentCategory::where('status', 1)->get();
        $data['genders'] = \App\Models\Gender::where('status', 1)->get();

        // Keep selected filters
        $data['selectedFilters'] = [
            'session' => $request->session,
            'grade' => $request->grade,
            'class' => $request->class,
            'section' => $request->section,
            'shift' => $request->shift,
            'category' => $request->category,
            'status' => $request->status,
            'gender' => $request->gender
        ];

        // Set report type for accordion state
        $data['report_type'] = 'list';

        return view('backend.report.student-report', compact('data'));
    }

    /**
     * Generate PDF for student list report
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function generateStudentListPDF(Request $request)
    {
        // Get report data
        $data['reportData'] = $this->repo->getStudentList($request);
        $data['filters'] = $request->all();

        // Generate PDF
        $pdf = PDF::loadView('backend.report.student-report-pdf', compact('data'));

        return $pdf->download('student-list-report-' . date('Y-m-d') . '.pdf');
    }

    /**
     * Search student registration report
     *
     * @param Request $request
     * @return \Illuminate\View\View
     */
    public function searchStudentRegistration(Request $request)
    {
        // Validate request
        $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'grade' => 'nullable|string',
            'class' => 'nullable|exists:classes,id',
            'section' => 'nullable|exists:sections,id',
            'shift' => 'nullable|exists:shifts,id',
            'status' => 'nullable|integer|in:0,1',
            'gender' => 'nullable|exists:genders,id'
        ]);

        // Get report data
        $data['registrationReportData'] = $this->repo->getStudentRegistration($request);

        // Reload dropdown data
        $data['sessions'] = \App\Models\Session::orderBy('id', 'desc')->get();
        $data['classes'] = \App\Models\Academic\Classes::where('status', 1)->get();
        $data['grades'] = collect(\App\Models\StudentInfo\Student::getAllGrades())->map(function($grade) {
            return (object)['grade' => $grade];
        });
        $data['sections'] = \App\Models\Academic\Section::where('status', 1)->get();
        $data['shifts'] = \App\Models\Academic\Shift::where('status', 1)->get();
        $data['categories'] = \App\Models\StudentInfo\StudentCategory::where('status', 1)->get();
        $data['genders'] = \App\Models\Gender::where('status', 1)->get();

        // Keep selected filters
        $data['registrationSelectedFilters'] = [
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'grade' => $request->grade,
            'class' => $request->class,
            'section' => $request->section,
            'shift' => $request->shift,
            'status' => $request->status,
            'gender' => $request->gender
        ];

        // Set report type for accordion state
        $data['report_type'] = 'registration';

        return view('backend.report.student-report', compact('data'));
    }

    /**
     * Generate PDF for student registration report
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function generateStudentRegistrationPDF(Request $request)
    {
        // Get report data
        $data['reportData'] = $this->repo->getStudentRegistration($request);
        $data['filters'] = $request->all();

        // Generate PDF
        $pdf = PDF::loadView('backend.report.student-registration-pdf', compact('data'));

        return $pdf->download('student-registration-report-' . date('Y-m-d') . '.pdf');
    }

    /**
     * Search guardian list report
     *
     * @param Request $request
     * @return \Illuminate\View\View
     */
    public function searchGuardianList(Request $request)
    {
        // Get report data (no validation needed - parameter-less procedure)
        $data['guardianReportData'] = $this->repo->getGuardianList();

        // Reload dropdown data for consistency with index method
        $data['sessions'] = \App\Models\Session::orderBy('id', 'desc')->get();
        $data['classes'] = \App\Models\Academic\Classes::where('status', 1)->get();
        $data['grades'] = collect(\App\Models\StudentInfo\Student::getAllGrades())->map(function($grade) {
            return (object)['grade' => $grade];
        });
        $data['sections'] = \App\Models\Academic\Section::where('status', 1)->get();
        $data['shifts'] = \App\Models\Academic\Shift::where('status', 1)->get();
        $data['categories'] = \App\Models\StudentInfo\StudentCategory::where('status', 1)->get();
        $data['genders'] = \App\Models\Gender::where('status', 1)->get();

        // Set report type for accordion state
        $data['report_type'] = 'guardian';

        return view('backend.report.student-report', compact('data'));
    }

    /**
     * Generate PDF for guardian list report
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function generateGuardianListPDF(Request $request)
    {
        // Get report data
        $data['reportData'] = $this->repo->getGuardianList();

        // Generate PDF
        $pdf = PDF::loadView('backend.report.guardian-list-pdf', compact('data'));

        return $pdf->download('guardian-list-report-' . date('Y-m-d') . '.pdf');
    }
}
