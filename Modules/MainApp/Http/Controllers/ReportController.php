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
        ]);

        $graceExceeded = $validated['grace_exceeded'] ?? false;

        try {
            // Fetch outstanding payments using stored procedure
            $outstandingPayments = DB::select(
                'CALL sp_get_outstanding_payments_report(?)',
                [$graceExceeded ? 1 : 0]
            );

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
        ]);

        $dateFrom = $validated['date_from'];
        $dateTo = $validated['date_to'];
        $schoolId = $validated['school_id'] ?? null;

        try {
            $export = new PaymentCollectionExport($dateFrom, $dateTo, $schoolId);
            $filename = 'payment_collection_' . date('Y-m-d_His');

            if ($format === 'excel') {
                return Excel::download($export, "{$filename}.xlsx");
            }

            // PDF export
            return Excel::download($export, "{$filename}.pdf", \Maatwebsite\Excel\Excel::DOMPDF);
        } catch (\Exception $e) {
            Log::error('Error exporting payment collection report', [
                'error' => $e->getMessage(),
                'format' => $format
            ]);

            return back()->with('danger', 'Failed to export report');
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
            'date_from' => 'required|date',
            'date_to' => 'required|date|after_or_equal:date_from',
        ]);

        $dateFrom = $validated['date_from'];
        $dateTo = $validated['date_to'];

        try {
            $export = new SchoolGrowthExport($dateFrom, $dateTo);
            $filename = 'school_growth_' . date('Y-m-d_His');

            if ($format === 'excel') {
                return Excel::download($export, "{$filename}.xlsx");
            }

            return Excel::download($export, "{$filename}.pdf", \Maatwebsite\Excel\Excel::DOMPDF);
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
        ]);

        $graceExceeded = $validated['grace_exceeded'] ?? false;

        try {
            $export = new OutstandingPaymentsExport($graceExceeded);
            $filename = 'outstanding_payments_' . date('Y-m-d_His');

            if ($format === 'excel') {
                return Excel::download($export, "{$filename}.xlsx");
            }

            return Excel::download($export, "{$filename}.pdf", \Maatwebsite\Excel\Excel::DOMPDF);
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
