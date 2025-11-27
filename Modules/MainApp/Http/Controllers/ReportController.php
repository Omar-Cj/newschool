<?php

declare(strict_types=1);

namespace Modules\MainApp\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Maatwebsite\Excel\Facades\Excel;
use Modules\MainApp\Entities\School;
use Modules\MultiBranch\Entities\Branch;
use App\Models\StudentInfo\Student;
use Modules\MainApp\Http\Repositories\ReportRepository;
use Modules\MainApp\Exports\PaymentCollectionExport;
use Modules\MainApp\Exports\SchoolGrowthExport;
use Modules\MainApp\Exports\OutstandingPaymentsExport;

class ReportController extends Controller
{
    private $repo;

    function __construct(ReportRepository $repo)
    {

        $this->repo        = $repo;
    }

    public function index()
    {
        $data['title'] = ___('settings.Payment Report');
        return view('mainapp::reports.payment-report', compact('data'));
    }

    public function search(Request $request)
    {
        $result = $this->repo->search($request);


        if($result['status']){

            $data['title'] = ___('settings.Payment Report');
            $data['subscriptions']          = $result['data']['subscriptions'];
            $data['dates']                  = $result['data']['dates'];
            $data['total']                  = $result['data']['total'];
            return view('mainapp::reports.payment-report', compact('data'));
        }
        return back()->with('danger', $result['message']);
    }

    /**
     * Payment collection report view
     *
     * @param Request $request
     * @return \Illuminate\View\View
     */
    public function paymentCollection(Request $request)
    {
        $validated = $request->validate([
            'date_from' => 'nullable|date',
            'date_to' => 'nullable|date|after_or_equal:date_from',
            'school_id' => 'nullable|exists:schools,id',
            'status' => 'nullable|in:0,1,2',
        ]);

        $dateFrom = $validated['date_from'] ?? Carbon::now()->startOfMonth()->format('Y-m-d');
        $dateTo = $validated['date_to'] ?? Carbon::now()->format('Y-m-d');
        $schoolId = $validated['school_id'] ?? null;
        $statusFilter = $validated['status'] ?? null;

        try {
            // Fetch report data using stored procedure
            $payments = DB::select(
                'CALL sp_get_payment_collection_report(?, ?, ?)',
                [$dateFrom, $dateTo, $schoolId]
            );

            // Apply status filter if provided
            if ($statusFilter !== null) {
                $payments = array_filter($payments, function ($payment) use ($statusFilter) {
                    return $payment->status_code == $statusFilter;
                });
            }

            // Prepare all data for the view in $data array
            $data['title'] = ___('mainapp_dashboard.Payment Collection Report');
            $data['schools'] = School::select('id', 'name')->orderBy('name')->get();
            $data['statusOptions'] = [
                0 => ___('mainapp_subscriptions.Pending'),
                1 => ___('mainapp_subscriptions.Approved'),
                2 => ___('mainapp_subscriptions.Rejected'),
            ];
            $data['paymentMethods'] = ['cash', 'bank_transfer', 'stripe', 'paypal'];
            $data['payments'] = $payments;

            // Calculate summary statistics
            $data['summary'] = [
                'total_payments' => count($payments),
                'total_amount' => array_sum(array_column($payments, 'amount')),
                'approved_count' => count(array_filter($payments, fn($p) => $p->status_code == 1)),
                'pending_count' => count(array_filter($payments, fn($p) => $p->status_code == 0)),
                'rejected_count' => count(array_filter($payments, fn($p) => $p->status_code == 2)),
            ];

            // Store filter parameters
            $data['dateFrom'] = $dateFrom;
            $data['dateTo'] = $dateTo;
            $data['schoolId'] = $schoolId;
            $data['statusFilter'] = $statusFilter;

            return view('mainapp::reports.payment-collection', compact('data'));
        } catch (\Exception $e) {
            Log::error('Error fetching payment collection report', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return back()->with('danger', 'Failed to load payment collection report');
        }
    }

    /**
     * School growth report view
     *
     * @param Request $request
     * @return \Illuminate\View\View
     */
    public function schoolGrowth(Request $request)
    {
        $validated = $request->validate([
            'date_from' => 'nullable|date',
            'date_to' => 'nullable|date|after_or_equal:date_from',
        ]);

        $dateFrom = $validated['date_from'] ?? Carbon::now()->subMonths(12)->startOfMonth()->format('Y-m-d');
        $dateTo = $validated['date_to'] ?? Carbon::now()->format('Y-m-d');

        try {
            // Fetch growth data using stored procedure
            $rawGrowthData = DB::select(
                'CALL sp_get_school_growth_report(?, ?)',
                [$dateFrom, $dateTo]
            );

            // Transform growth data to match blade expectations
            $growthData = array_map(function ($row) {
                return [
                    'period' => $row->period_label ?? '',
                    'new_schools' => (int) ($row->new_schools ?? 0),
                    'new_branches' => (int) ($row->new_branches ?? 0),
                    'new_students' => (int) ($row->new_students ?? 0),
                    'growth_rate' => (float) ($row->growth_percentage ?? 0),
                    'cumulative_schools' => (int) ($row->cumulative_schools ?? 0),
                ];
            }, $rawGrowthData);

            // Calculate totals from Schools table
            $totalSchools = School::count();
            $newSchoolsThisMonth = School::whereMonth('created_at', Carbon::now()->month)
                ->whereYear('created_at', Carbon::now()->year)
                ->count();

            // Prepare chart data matching blade expectations
            $chartData = [
                'periods' => array_column($growthData, 'period'),
                'schools' => array_column($growthData, 'new_schools'),
                'branches' => array_column($growthData, 'new_branches'),
                'students' => array_column($growthData, 'new_students'),
            ];

            // Query actual branch and student counts from database
            $totalBranches = Branch::count();
            $totalStudents = Student::count();

            // Build complete data array for view
            $data = [
                'title' => ___('mainapp_dashboard.School Growth Report'),
                'growthData' => $growthData,
                'chartData' => $chartData,
                'totalSchools' => $totalSchools,
                'newSchoolsThisMonth' => $newSchoolsThisMonth,
                'totalBranches' => $totalBranches,
                'totalStudents' => $totalStudents,
                'schoolsGrowthRate' => count($growthData) > 0
                    ? array_sum(array_column($growthData, 'growth_rate')) / count($growthData)
                    : 0,
            ];

            return view('mainapp::reports.school-growth', compact('data'));
        } catch (\Exception $e) {
            Log::error('Error fetching school growth report', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return back()->with('danger', 'Failed to load school growth report');
        }
    }

    /**
     * Outstanding payments report view
     *
     * @param Request $request
     * @return \Illuminate\View\View
     */
    public function outstandingPayments(Request $request)
    {
        $validated = $request->validate([
            'grace_exceeded' => 'nullable|boolean',
            'urgency' => 'nullable|in:critical,grace,expiring',
            'school_id' => 'nullable|exists:schools,id',
            'sort_by' => 'nullable|in:urgency,amount,expiry_date,school_name',
        ]);

        $graceExceeded = $validated['grace_exceeded'] ?? false;
        $urgencyFilter = $validated['urgency'] ?? null;
        $schoolIdFilter = $validated['school_id'] ?? null;
        $sortBy = $validated['sort_by'] ?? 'urgency';

        try {
            // Fetch outstanding payments using stored procedure
            $outstandingPayments = DB::select(
                'CALL sp_get_outstanding_payments_report(?)',
                [$graceExceeded ? 1 : 0]
            );

            // Apply urgency filter
            if ($urgencyFilter) {
                $urgencyMap = [
                    'critical' => 'Critical',
                    'grace' => 'In Grace Period',
                    'expiring' => 'Expiring Soon',
                ];
                $outstandingPayments = array_filter($outstandingPayments, fn($p) => $p->urgency_level === $urgencyMap[$urgencyFilter]);
                $outstandingPayments = array_values($outstandingPayments);
            }

            // Apply school filter
            if ($schoolIdFilter) {
                $outstandingPayments = array_filter($outstandingPayments, fn($p) => $p->school_id == $schoolIdFilter);
                $outstandingPayments = array_values($outstandingPayments);
            }

            // Apply sorting
            usort($outstandingPayments, function($a, $b) use ($sortBy) {
                return match($sortBy) {
                    'amount' => ($b->outstanding_amount ?? 0) <=> ($a->outstanding_amount ?? 0),
                    'expiry_date' => strtotime($a->expiry_date ?? '9999-12-31') <=> strtotime($b->expiry_date ?? '9999-12-31'),
                    'school_name' => strcasecmp($a->school_name ?? '', $b->school_name ?? ''),
                    default => 0, // Keep original order for urgency (already sorted by stored procedure)
                };
            });

            // Prepare all data for the view in $data array
            $data['title'] = ___('mainapp_dashboard.Outstanding Payments Report');
            $data['urgencyLevels'] = ['critical', 'grace', 'expiring'];
            $data['allSchools'] = School::select('id', 'name')->orderBy('name')->get();

            // Group by urgency level
            $data['byUrgency'] = [
                'critical' => array_filter($outstandingPayments, fn($p) => $p->urgency_level === 'Critical'),
                'grace_period' => array_filter($outstandingPayments, fn($p) => $p->urgency_level === 'In Grace Period'),
                'expiring_soon' => array_filter($outstandingPayments, fn($p) => $p->urgency_level === 'Expiring Soon'),
            ];

            // Store outstandingPayments as schools for blade template consistency
            $data['schools'] = $outstandingPayments;

            // Calculate summary statistics
            $data['totalOutstanding'] = array_sum(array_column($outstandingPayments, 'outstanding_amount'));
            $data['overdueCount'] = count($data['byUrgency']['critical']);
            $data['graceCount'] = count($data['byUrgency']['grace_period']);
            $data['expiringCount'] = count($data['byUrgency']['expiring_soon']);

            $data['summary'] = [
                'total_schools' => count($outstandingPayments),
                'total_outstanding' => $data['totalOutstanding'],
                'critical_count' => $data['overdueCount'],
                'grace_period_count' => $data['graceCount'],
                'expiring_soon_count' => $data['expiringCount'],
            ];

            $data['graceExceeded'] = $graceExceeded;

            return view('mainapp::reports.outstanding-payments', compact('data'));
        } catch (\Exception $e) {
            Log::error('Error fetching outstanding payments report', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return back()->with('danger', 'Failed to load outstanding payments report');
        }
    }

    /**
     * Export payment collection report
     *
     * @param Request $request
     * @param string $format 'excel' or 'pdf'
     * @return \Illuminate\Http\Response
     */
    public function exportPaymentCollection(Request $request, string $format)
    {
        $validated = $request->validate([
            'date_from' => 'required|date',
            'date_to' => 'required|date|after_or_equal:date_from',
            'school_id' => 'nullable|exists:schools,id',
            'status' => 'nullable|in:0,1,2',
            'payment_method' => 'nullable|string',
        ]);

        $dateFrom = $validated['date_from'];
        $dateTo = $validated['date_to'];
        $schoolId = $validated['school_id'] ?? null;
        $statusFilter = $validated['status'] ?? null;

        try {
            if ($format === 'excel') {
                $export = new PaymentCollectionExport($dateFrom, $dateTo, $schoolId);
                $filename = 'payment_collection_' . date('Y-m-d_His');
                return Excel::download($export, "{$filename}.xlsx");
            }

            // PDF export with custom view
            $payments = DB::select(
                'CALL sp_get_payment_collection_report(?, ?, ?)',
                [$dateFrom, $dateTo, $schoolId]
            );

            // Apply status filter if provided
            if ($statusFilter !== null) {
                $payments = array_filter($payments, function ($payment) use ($statusFilter) {
                    return $payment->status_code == $statusFilter;
                });
            }

            // Safely get school name with null check
            $schoolFilter = null;
            if ($schoolId) {
                $school = School::find($schoolId);
                $schoolFilter = $school ? $school->name : "Unknown School (ID: {$schoolId})";
            }

            $data = [
                'payments' => $payments,
                'dateFrom' => $dateFrom,
                'dateTo' => $dateTo,
                'schoolFilter' => $schoolFilter,
                'summary' => [
                    'total_payments' => count($payments),
                    'total_amount' => array_sum(array_column($payments, 'amount')),
                    'approved_count' => count(array_filter($payments, fn($p) => $p->status_code == 1)),
                    'pending_count' => count(array_filter($payments, fn($p) => $p->status_code == 0)),
                    'rejected_count' => count(array_filter($payments, fn($p) => $p->status_code == 2)),
                ],
            ];

            // Generate PDF with proper error handling
            try {
                $pdf = \PDF::loadView('mainapp::reports.pdf.payment-collection', $data);
                $pdf->setPaper('a4', 'landscape');
                return $pdf->download('payment_collection_' . date('Y-m-d_His') . '.pdf');
            } catch (\Exception $pdfException) {
                Log::error('PDF generation failed for payment collection report', [
                    'error' => $pdfException->getMessage(),
                    'trace' => $pdfException->getTraceAsString(),
                    'data_summary' => [
                        'payment_count' => count($payments),
                        'date_range' => "{$dateFrom} to {$dateTo}",
                        'school_filter' => $schoolFilter,
                    ]
                ]);
                throw $pdfException;
            }
        } catch (\Exception $e) {
            Log::error('Error exporting payment collection report', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'format' => $format,
                'filters' => [
                    'date_from' => $dateFrom ?? null,
                    'date_to' => $dateTo ?? null,
                    'school_id' => $schoolId ?? null,
                    'status' => $statusFilter ?? null,
                ]
            ]);

            return back()->with('danger', 'Failed to export report: ' . $e->getMessage());
        }
    }

    /**
     * Export school growth report
     *
     * @param Request $request
     * @param string $format 'excel' or 'pdf'
     * @return \Illuminate\Http\Response
     */
    public function exportSchoolGrowth(Request $request, string $format)
    {
        $validated = $request->validate([
            'date_from' => 'nullable|date',
            'date_to' => 'nullable|date|after_or_equal:date_from',
            'date_range' => 'nullable|string',
            'view_type' => 'nullable|in:monthly,yearly',
        ]);

        // Parse date_range if provided (format: "YYYY-MM-DD - YYYY-MM-DD")
        if (!empty($validated['date_range'])) {
            $dates = explode(' - ', $validated['date_range']);
            if (count($dates) === 2) {
                $dateFrom = trim($dates[0]);
                $dateTo = trim($dates[1]);
            } else {
                $dateFrom = Carbon::now()->subMonths(12)->startOfMonth()->format('Y-m-d');
                $dateTo = Carbon::now()->format('Y-m-d');
            }
        } else {
            $dateFrom = $validated['date_from'] ?? Carbon::now()->subMonths(12)->startOfMonth()->format('Y-m-d');
            $dateTo = $validated['date_to'] ?? Carbon::now()->format('Y-m-d');
        }

        try {
            if ($format === 'excel') {
                $export = new SchoolGrowthExport($dateFrom, $dateTo);
                $filename = 'school_growth_' . date('Y-m-d_His');
                return Excel::download($export, "{$filename}.xlsx");
            }

            // PDF export with custom view
            $rawGrowthData = DB::select(
                'CALL sp_get_school_growth_report(?, ?)',
                [$dateFrom, $dateTo]
            );

            $growthData = array_map(function ($row) {
                return [
                    'period' => $row->period_label ?? '',
                    'new_schools' => (int) ($row->new_schools ?? 0),
                    'new_branches' => (int) ($row->new_branches ?? 0),
                    'new_students' => (int) ($row->new_students ?? 0),
                    'growth_rate' => (float) ($row->growth_percentage ?? 0),
                    'cumulative_schools' => (int) ($row->cumulative_schools ?? 0),
                ];
            }, $rawGrowthData);

            $data = [
                'growthData' => $growthData,
                'dateFrom' => $dateFrom,
                'dateTo' => $dateTo,
                'totalSchools' => School::count(),
                'newSchoolsThisMonth' => School::whereMonth('created_at', Carbon::now()->month)
                    ->whereYear('created_at', Carbon::now()->year)
                    ->count(),
                'totalBranches' => Branch::count(),
                'totalStudents' => Student::count(),
                'schoolsGrowthRate' => count($growthData) > 0
                    ? array_sum(array_column($growthData, 'growth_rate')) / count($growthData)
                    : 0,
            ];

            $pdf = \PDF::loadView('mainapp::reports.pdf.school-growth', $data);
            $pdf->setPaper('a4', 'landscape');
            return $pdf->download('school_growth_' . date('Y-m-d_His') . '.pdf');
        } catch (\Exception $e) {
            Log::error('Error exporting school growth report', [
                'error' => $e->getMessage(),
                'format' => $format
            ]);

            return back()->with('danger', 'Failed to export report');
        }
    }

    /**
     * Export outstanding payments report
     *
     * @param Request $request
     * @param string $format 'excel' or 'pdf'
     * @return \Illuminate\Http\Response
     */
    public function exportOutstanding(Request $request, string $format)
    {
        $validated = $request->validate([
            'grace_exceeded' => 'nullable|boolean',
            'urgency' => 'nullable|in:critical,grace,expiring',
            'school_id' => 'nullable|exists:schools,id',
        ]);

        $graceExceeded = $validated['grace_exceeded'] ?? false;
        $urgencyFilter = $validated['urgency'] ?? null;
        $schoolIdFilter = $validated['school_id'] ?? null;

        try {
            if ($format === 'excel') {
                $export = new OutstandingPaymentsExport($graceExceeded);
                $filename = 'outstanding_payments_' . date('Y-m-d_His');
                return Excel::download($export, "{$filename}.xlsx");
            }

            // PDF export with custom view
            $outstandingPayments = DB::select(
                'CALL sp_get_outstanding_payments_report(?)',
                [$graceExceeded ? 1 : 0]
            );

            // Apply urgency filter
            if ($urgencyFilter) {
                $urgencyMap = [
                    'critical' => 'Critical',
                    'grace' => 'In Grace Period',
                    'expiring' => 'Expiring Soon',
                ];
                $outstandingPayments = array_filter($outstandingPayments, fn($p) => $p->urgency_level === $urgencyMap[$urgencyFilter]);
                $outstandingPayments = array_values($outstandingPayments);
            }

            // Apply school filter
            if ($schoolIdFilter) {
                $outstandingPayments = array_filter($outstandingPayments, fn($p) => $p->school_id == $schoolIdFilter);
                $outstandingPayments = array_values($outstandingPayments);
            }

            $data = [
                'schools' => $outstandingPayments,
                'totalOutstanding' => array_sum(array_column($outstandingPayments, 'outstanding_amount')),
                'overdueCount' => count(array_filter($outstandingPayments, fn($p) => $p->urgency_level === 'Critical')),
                'graceCount' => count(array_filter($outstandingPayments, fn($p) => $p->urgency_level === 'In Grace Period')),
                'expiringCount' => count(array_filter($outstandingPayments, fn($p) => $p->urgency_level === 'Expiring Soon')),
                'filters' => $urgencyFilter || $schoolIdFilter,
            ];

            $pdf = \PDF::loadView('mainapp::reports.pdf.outstanding-payments', $data);
            $pdf->setPaper('a4', 'landscape');
            return $pdf->download('outstanding_payments_' . date('Y-m-d_His') . '.pdf');
        } catch (\Exception $e) {
            Log::error('Error exporting outstanding payments report', [
                'error' => $e->getMessage(),
                'format' => $format
            ]);

            return back()->with('danger', 'Failed to export report');
        }
    }

    /**
     * AJAX endpoint for dashboard chart data
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function chartData(Request $request)
    {
        $validated = $request->validate([
            'chart_type' => 'required|in:revenue,package_distribution,growth',
            'period' => 'nullable|in:monthly,yearly',
            'year' => 'nullable|integer|min:2020|max:2099',
        ]);

        try {
            $dashboardService = app(\Modules\MainApp\Services\DashboardMetricsService::class);

            switch ($validated['chart_type']) {
                case 'revenue':
                    $period = $validated['period'] ?? 'monthly';
                    $year = $validated['year'] ?? Carbon::now()->year;
                    $data = $dashboardService->getRevenueChart($period, $year);
                    break;

                case 'package_distribution':
                    $data = $dashboardService->getPackageDistribution();
                    break;

                case 'growth':
                    $data = $dashboardService->getSchoolGrowth();
                    break;

                default:
                    return response()->json(['error' => 'Invalid chart type'], 400);
            }

            return response()->json([
                'success' => true,
                'data' => $data
            ]);
        } catch (\Exception $e) {
            Log::error('Error fetching chart data', [
                'error' => $e->getMessage(),
                'chart_type' => $validated['chart_type']
            ]);

            return response()->json([
                'success' => false,
                'error' => 'Failed to load chart data'
            ], 500);
        }
    }
}
