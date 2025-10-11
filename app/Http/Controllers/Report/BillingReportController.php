<?php

namespace App\Http\Controllers\Report;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class BillingReportController extends Controller
{
    /**
     * Display billing reports page with 5 collapsible sections
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $data['title'] = 'Billing Reports';

        // Placeholder data structure for UI rendering
        $data['classes'] = \App\Models\Academic\Classes::where('status', 1)->get();
        $data['sections'] = \App\Models\Academic\Section::where('status', 1)->get();
        $data['sessions'] = \App\Models\Session::orderBy('id', 'desc')->get();

        return view('backend.report.billing-report', compact('data'));
    }

    // Future methods will be added here:
    // - searchPaidStudents()
    // - searchUnpaidStudents()
    // - searchDiscounts()
    // - searchFeeGeneration()
    // - searchReceipts()
}
