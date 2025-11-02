<?php

namespace App\Http\Controllers\ParentPanel;

use App\Http\Controllers\Controller;
use App\Repositories\ParentPanel\AttendanceRepository;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\ParentPanel\AttendanceExport;

class AttendanceController extends Controller
{
    private $repo;

    function __construct(  AttendanceRepository $repo) 
    { 
        $this->repo = $repo;
    }

    public function index()
    {
        $data['title']              = ___('common.Attendance');
        
        $data                       = $this->repo->index();
        $data['results']            = [];
        return view('parent-panel.attendance', compact('data'));
    }

    public function search(Request $request)
    {
        // Validate that student is selected
        if (!$request->filled('student')) {
            return redirect()->back()->with('danger', ___('common.please_select_student'));
        }

        $data = $this->repo->search($request);

        // Handle repository error
        if ($data === false) {
            return redirect()->back()->with('danger', ___('alert.something_went_wrong_please_try_again'));
        }

        $data['title']   = ___('common.Attendance');
        $data['request'] = $request;

        return view('parent-panel.attendance', compact('data'));
    }

    public function exportExcel(Request $request)
    {
        // Validate that student is selected
        if (!$request->filled('student')) {
            return redirect()->back()->with('danger', ___('common.please_select_student'));
        }

        // Get the same data as search() method
        $data = $this->repo->search($request);

        // Handle repository error
        if ($data === false) {
            return redirect()->back()->with('danger', ___('alert.something_went_wrong_please_try_again'));
        }

        // Generate filename with current date
        $filename = 'parent-attendance-report-' . date('Y-m-d') . '.xlsx';

        // Return Excel download using AttendanceExport class
        return Excel::download(new AttendanceExport($data, $request), $filename);
    }

    public function print(Request $request)
    {
        // Validate that student is selected
        if (!$request->filled('student')) {
            return redirect()->back()->with('danger', ___('common.please_select_student'));
        }

        // Get the same data as search() method
        $data = $this->repo->search($request);

        // Handle repository error
        if ($data === false) {
            return redirect()->back()->with('danger', ___('alert.something_went_wrong_please_try_again'));
        }

        $data['title']   = ___('common.Attendance');
        $data['request'] = $request;

        // Return view for printing
        return view('parent-panel.attendance-print', compact('data'));
    }
}
