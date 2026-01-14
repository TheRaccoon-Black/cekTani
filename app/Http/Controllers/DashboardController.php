<?php

namespace App\Http\Controllers;

use App\Models\Land;
use App\Models\Sector;
use App\Models\PlantingCycle;
use App\Models\Transaction;
use App\Models\Bed;
use App\Models\CycleLog;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $selectedLandId = $request->input('land_id');
        $selectedYear = $request->input('year', date('Y'));

        $transactionQuery = Transaction::query()
            ->whereYear('transaction_date', $selectedYear);

        $cycleQuery = PlantingCycle::where('status', 'active');

        if ($selectedLandId) {
            $transactionQuery->where('land_id', $selectedLandId);
            $cycleQuery->whereHas('bed.sector', function ($q) use ($selectedLandId) {
                $q->where('land_id', $selectedLandId);
            });
        }

        $totalLands = Land::count();
        $totalSectors = $selectedLandId ? Sector::where('land_id', $selectedLandId)->count() : Sector::count();
        $totalBeds = $selectedLandId ? Bed::whereHas('sector', function ($q) use ($selectedLandId) {
            $q->where('land_id', $selectedLandId);
        })->count() : Bed::count();

        $activeCycles = $cycleQuery->with('commodity', 'bed.sector.land')->get();
        $totalPlants = $activeCycles->sum('current_plant_count');
        $activeBedsCount = $activeCycles->count();
        $occupancyRate = ($totalBeds > 0) ? ($activeBedsCount / $totalBeds) * 100 : 0;

        $incomeTotal = (clone $transactionQuery)->where('type', 'income')->sum('amount');

        $cashExpenseTotal = (clone $transactionQuery)->where('type', 'expense')->sum('amount');
        $netProfit = $incomeTotal - $cashExpenseTotal;

        $productionCostTotal = (clone $transactionQuery)->whereIn('type', ['expense', 'cost_allocation'])->sum('amount');

        $costPerPlant = ($totalPlants > 0) ? ($productionCostTotal / $totalPlants) : 0;

        $expenseBreakdown = (clone $transactionQuery)
            ->whereIn('type', ['expense', 'cost_allocation'])
            ->select('category', DB::raw('sum(amount) as total'))
            ->groupBy('category')
            ->orderBy('total', 'desc')
            ->get();

        $costCategories = $expenseBreakdown->pluck('category');
        $costValues = $expenseBreakdown->pluck('total');

        $logsQuery = CycleLog::query();
        if ($selectedLandId) {
            $logsQuery->whereHas('plantingCycle.bed.sector', function ($q) use ($selectedLandId) {
                $q->where('land_id', $selectedLandId);
            });
        }
        $logStats = $logsQuery->select('phase', DB::raw('count(*) as total'))
            ->groupBy('phase')
            ->pluck('total', 'phase');

        $chartLabels = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
        $incomeData = [];
        $expenseData = [];

        for ($m = 1; $m <= 12; $m++) {
            $incomeData[] = (clone $transactionQuery)->whereMonth('transaction_date', $m)->where('type', 'income')->sum('amount');
            $expenseData[] = (clone $transactionQuery)->whereMonth('transaction_date', $m)->where('type', 'expense')->sum('amount');
        }

        $lands = Land::all();
        $alerts = $activeCycles->filter(function ($cycle) {
            return Carbon::now()->greaterThan(Carbon::parse($cycle->estimated_harvest_date));
        });

        $topCommodity = $activeCycles->groupBy('commodity.name')
            ->sortByDesc(function ($group) { return $group->count(); })->first();
        $topCommodityName = $topCommodity ? $activeCycles->where('commodity_id', $topCommodity->first()->commodity_id)->first()->commodity->name : 'N/A';

        $harvestHistory = PlantingCycle::where('status', 'harvested')
            ->when($selectedLandId, function ($q) use ($selectedLandId) {
                $q->whereHas('bed.sector', function ($sq) use ($selectedLandId) {
                    $sq->where('land_id', $selectedLandId);
                });
            })
            ->with(['commodity', 'bed.sector', 'transactions'])
            ->orderBy('updated_at', 'desc')
            ->take(5)
            ->get()
            ->map(function ($cycle) {
                $income = $cycle->transactions->where('type', 'income')->sum('amount');
                $expense = $cycle->transactions->whereIn('type', ['expense', 'cost_allocation'])->sum('amount');

                $cycle->real_profit = $income - $expense;
                $cycle->rev_per_plant = ($cycle->initial_plant_count > 0) ? ($income / $cycle->initial_plant_count) : 0;

                return $cycle;
            });

        $recentLogs = CycleLog::with(['plantingCycle.bed.sector', 'plantingCycle.commodity'])
            ->when($selectedLandId, function ($q) use ($selectedLandId) {
                $q->whereHas('plantingCycle.bed.sector', function ($sq) use ($selectedLandId) {
                    $sq->where('land_id', $selectedLandId);
                });
            })
            ->orderBy('log_date', 'desc')
            ->orderBy('created_at', 'desc')
            ->take(6)
            ->get();

        return view('dashboard', compact(
            'lands',
            'selectedLandId',
            'selectedYear',
            'totalLands',
            'totalSectors',
            'totalBeds',
            'occupancyRate',
            'totalPlants',
            'activeBedsCount',
            'topCommodityName',
            'incomeTotal',
            'cashExpenseTotal',
            'netProfit',
            'costPerPlant',
            'costCategories',
            'costValues',
            'logStats',
            'chartLabels',
            'incomeData',
            'expenseData',
            'alerts', 'harvestHistory', 'recentLogs'
        ));
    }
}
