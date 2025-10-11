<?php

namespace App\Http\Controllers\Report;

use App\Http\Controllers\Controller;
use App\Repositories\Report\BillingReportRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Barryvdh\DomPDF\Facade\Pdf;

class BillingReportController extends Controller
{
    protected $billingReportRepository;

    public function __construct(BillingReportRepository $billingReportRepository)
    {
        $this->billingReportRepository = $billingReportRepository;
    }

    /**
     * Display billing reports page with 5 collapsible sections
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $data['title'] = 'Billing Reports';

        // Load filter options for all report types
        $data['classes'] = \App\Models\Academic\Classes::where('status', 1)->get();
        $data['sections'] = \App\Models\Academic\Section::where('status', 1)->get();
        $data['genders'] = \App\Models\Gender::where('status', 1)->get();
        $data['sessions'] = \App\Models\Session::orderBy('id', 'desc')->get();
        $data['shifts'] = \App\Models\Academic\Shift::where('status', 1)->get();
        $data['grades'] = ['KG-1', 'KG-2', 'Grade1', 'Grade2', 'Grade3', 'Grade4', 'Grade5', 'Grade6', 'Grade7', 'Grade8', 'Form1', 'Form2', 'Form3', 'Form4'];

        return view('backend.report.billing-report', compact('data'));
    }

    /**
     * Search Paid Students Report
     * Validates and retrieves paid students data from repository
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function searchPaidStudents(Request $request)
    {
        // Validation
        $validator = Validator::make($request->all(), [
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'grade' => 'nullable|string',
            'class_id' => 'nullable|integer|exists:classes,id',
            'section_id' => 'nullable|integer|exists:sections,id',
            'gender_id' => 'nullable|integer|exists:genders,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        // Get results from repository
        $result = $this->billingReportRepository->getPaidStudents($request);

        return response()->json($result);
    }

    /**
     * Export Paid Students Report to PDF
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function exportPaidStudentsPDF(Request $request)
    {
        // Validation
        $validator = Validator::make($request->all(), [
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        // Get results from repository
        $result = $this->billingReportRepository->getPaidStudents($request);

        if (!$result['success']) {
            return redirect()->back()->with('error', 'Failed to generate PDF: ' . $result['error']);
        }

        // Prepare data for PDF
        $data = [
            'title' => 'Paid Students Report',
            'results' => $result['data'],
            'summary' => $result['summary'],
            'filters' => [
                'start_date' => $request->start_date,
                'end_date' => $request->end_date,
                'grade' => $request->grade ?? 'All',
                'class' => $request->class_id ? \App\Models\Academic\Classes::find($request->class_id)->name : 'All',
                'section' => $request->section_id ? \App\Models\Academic\Section::find($request->section_id)->name : 'All',
                'gender' => $request->gender_id ? \App\Models\Gender::find($request->gender_id)->name : 'All',
            ],
        ];

        // Generate PDF
        $pdf = Pdf::loadView('backend.report.paid-students-pdf', $data);
        $pdf->setPaper('A4', 'landscape');

        return $pdf->download('paid-students-report-' . date('Y-m-d-His') . '.pdf');
    }

    /**
     * Search Unpaid Students Report
     * Validates and retrieves unpaid students data from repository
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function searchUnpaidStudents(Request $request)
    {
        // Validation
        $validator = Validator::make($request->all(), [
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'grade' => 'nullable|string',
            'class_id' => 'nullable|integer|exists:classes,id',
            'section_id' => 'nullable|integer|exists:sections,id',
            'status' => 'nullable|integer|in:0,1',
            'shift_id' => 'nullable|integer|exists:shifts,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        // Get results from repository
        $result = $this->billingReportRepository->getUnpaidStudents($request);

        return response()->json($result);
    }

    /**
     * Export Unpaid Students Report to PDF
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function exportUnpaidStudentsPDF(Request $request)
    {
        // Validation
        $validator = Validator::make($request->all(), [
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        // Get results from repository
        $result = $this->billingReportRepository->getUnpaidStudents($request);

        if (!$result['success']) {
            return redirect()->back()->with('error', 'Failed to generate PDF: ' . $result['error']);
        }

        // Prepare data for PDF
        $data = [
            'title' => 'Unpaid Students Report',
            'results' => $result['data'],
            'summary' => $result['summary'],
            'filters' => [
                'start_date' => $request->start_date,
                'end_date' => $request->end_date,
                'grade' => $request->grade ?? 'All',
                'class' => $request->class_id ? \App\Models\Academic\Classes::find($request->class_id)->name : 'All',
                'section' => $request->section_id ? \App\Models\Academic\Section::find($request->section_id)->name : 'All',
                'status' => $request->status !== null ? ($request->status == 1 ? 'Active' : 'Inactive') : 'All',
                'shift' => $request->shift_id ? \App\Models\Academic\Shift::find($request->shift_id)->name : 'All',
            ],
        ];

        // Generate PDF
        $pdf = Pdf::loadView('backend.report.unpaid-students-pdf', $data);
        $pdf->setPaper('A4', 'portrait');

        return $pdf->download('unpaid-students-report-' . date('Y-m-d-His') . '.pdf');
    }

    // Future methods:
    // - searchDiscounts()
    // - searchFeeGeneration()
    // - searchReceipts()
}
