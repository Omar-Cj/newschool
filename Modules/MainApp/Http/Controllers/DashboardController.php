<?php

declare(strict_types=1);

namespace Modules\MainApp\Http\Controllers;

use Illuminate\Support\Str;
use function Ramsey\Uuid\v1;
use Illuminate\Http\Request;

use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Modules\MainApp\Entities\Subscription;
use Modules\MainApp\Http\Repositories\FAQRepository;
use Modules\MainApp\Http\Repositories\SchoolRepository;
use Modules\MainApp\Http\Repositories\FeatureRepository;
use Modules\MainApp\Http\Repositories\PackageRepository;
use Modules\MainApp\Services\DashboardMetricsService;

class DashboardController extends Controller
{
    private $schoolRepo;
    private $featureRepo;
    private $packageRepo;
    private $faqRepo;
    private $metricsService;

    function __construct(
        SchoolRepository  $schoolRepo,
        FeatureRepository $featureRepo,
        PackageRepository $packageRepo,
        FAQRepository     $faqRepo,
        DashboardMetricsService $metricsService
    )
    {
        if (!Schema::hasTable('settings') && !Schema::hasTable('users')  ) {
            abort(400);
        }
        $this->schoolRepo  = $schoolRepo;
        $this->featureRepo = $featureRepo;
        $this->packageRepo = $packageRepo;
        $this->faqRepo     = $faqRepo;
        $this->metricsService = $metricsService;
    }

    public function index(Request $request)
    {
        // Get revenue per school for bar chart
        $schoolRevenue = DB::select("
            SELECT
                sc.name as school_name,
                COALESCE(SUM(CASE WHEN sp.status = 1 THEN sp.amount ELSE 0 END), 0) as total_revenue
            FROM schools sc
            LEFT JOIN subscriptions s ON s.school_id = sc.id
            LEFT JOIN subscription_payments sp ON sp.subscription_id = s.id
            GROUP BY sc.id, sc.name
            ORDER BY total_revenue DESC
            LIMIT 20
        ");

        $data['school_names'] = array_column($schoolRevenue, 'school_name');
        $data['revenues'] = array_map(function($item) {
            return (float) $item->total_revenue;
        }, $schoolRevenue);
        $data['totalSchool']       = $this->schoolRepo->all()->count();
        $data['activeSchools']     = $this->schoolRepo->activeAll()->count();
        $data['inactiveSchools']   = $data['totalSchool'] - $data['activeSchools'];
        $data['totalFeature']      = $this->featureRepo->all()->count();
        $data['totalPackage']      = $this->packageRepo->all()->count();
        $data['totalFAQ']          = $this->faqRepo->all()->count();

        // Enhanced dashboard metrics
        $data['metrics']           = $this->metricsService->getMetricCards();
        $data['recentPayments']    = $this->metricsService->getRecentPayments(10);
        $data['schoolsNearExpiry'] = $this->metricsService->getSchoolsNearExpiry(30);
        $data['packageDistribution'] = $this->metricsService->getPackageDistribution();

        return view('mainapp::dashboard', compact('data'));
    }
}
