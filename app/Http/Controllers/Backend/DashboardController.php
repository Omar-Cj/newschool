<?php

namespace App\Http\Controllers\Backend;

use App\Models\Role;
use App\Models\User;
use App\Models\Search;
use App\Models\Language;
use App\Models\Permission;
use App\Models\Staff\Designation;
use Illuminate\Http\Request;
use App\Interfaces\UserInterface;
use App\Http\Controllers\Controller;
use App\Models\Accounts\Expense;
use App\Models\Accounts\Income;
use App\Repositories\DashboardRepository;
use Illuminate\Support\Facades\Schema;

class DashboardController extends Controller
{
    private $repo;


    function __construct(DashboardRepository $repo)
    {

        if (!Schema::hasTable('settings') && !Schema::hasTable('users')  ) {
            abort(400);
        } 
        $this->repo       = $repo;
    }

    public function index()
    {
        $data = $this->repo->index();
        $data['genderDistribution'] = $this->repo->genderDistribution();
        $data['categoryDistribution'] = $this->repo->categoryDistribution();
        $data['shiftDistribution'] = $this->repo->shiftDistribution();
        $data['gradeDistribution'] = $this->repo->gradeDistribution();
        return view('backend.dashboard', compact('data'));
    }

    public function feesCollectionMonthly() {
        return $this->repo->feesCollectionYearly();
    }

    public function revenueYearly() {
        return $this->repo->revenueYearly();
    }

    public function feesCollectionCurrentMonth()
    {
        return $this->repo->feesCollection();
    }

    public function incomeExpenseCurrentMonth()
    {
        return $this->repo->incomeExpense();
    }

    public function todayAttendance()
    {
        return $this->repo->attendance();
    }

    public function transportationDistribution()
    {
        return response()->json($this->repo->transportationDistribution());
    }

    public function eventsCurrentMonth()
    {
        return $this->repo->eventsCurrentMonth();
    }

    public function searchMenuData(Request $request){
        try {
            $search = Search::query();
            if ($request->has('search')) {
                $search->where('title', 'like', '%' . $request->search . '%');
            }
            $search = $search->where('user_type', 'Admin')->take(10)->get()->map(function ($item) {
                return [
                    'title' => $item->title,
                    'route_name' => route($item->route_name),
                ];
            });
            return response()->json($search);
        } catch (\Throwable $th) {
            return response()->json(['error' => $th->getMessage()], 500);
        }
    }

    /**
     * School dashboard - main dashboard for school users (admins, teachers, staff)
     */
    public function schoolDashboard()
    {
        return $this->index();
    }

    /**
     * LMS dashboard
     */
    public function lmsDashboard()
    {
        return $this->index();
    }

    /**
     * CRM dashboard
     */
    public function crmDashboard()
    {
        return $this->index();
    }

}
