<?php

namespace App\Http\Controllers;

use App\Models\Bed;
use App\Models\Land;
use App\Models\Transaction;
use Illuminate\Http\Request;
use App\Models\PlantingCycle;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class TransactionController extends Controller
{
    public function index(Request $request)
    {
        $startDate = $request->start_date ?? Carbon::now()->startOfMonth()->format('Y-m-d');
        $endDate = $request->end_date ?? Carbon::now()->endOfMonth()->format('Y-m-d');

        $query = Transaction::whereBetween('transaction_date', [$startDate, $endDate]);
        if ($request->land_id) {
            $query->where('land_id', $request->land_id);
        }

        $currentStats = (clone $query)->selectRaw('
            SUM(CASE WHEN type = "income" THEN amount ELSE 0 END) as income,
            SUM(CASE WHEN type = "expense" THEN amount ELSE 0 END) as expense
        ')->first();

        $netProfit = $currentStats->income - $currentStats->expense;

        $startLast = Carbon::parse($startDate)->subMonth()->format('Y-m-d');
        $endLast = Carbon::parse($endDate)->subMonth()->format('Y-m-d');

        $lastStats = Transaction::whereBetween('transaction_date', [$startLast, $endLast])
            ->when($request->land_id, function ($q) use ($request) {
                $q->where('land_id', $request->land_id);
            })
            ->selectRaw('
                SUM(CASE WHEN type = "income" THEN amount ELSE 0 END) as income,
                SUM(CASE WHEN type = "expense" THEN amount ELSE 0 END) as expense
            ')->first();

        $incomeGrowth = $this->calculateGrowth($currentStats->income, $lastStats->income);
        $expenseGrowth = $this->calculateGrowth($currentStats->expense, $lastStats->expense);
        $profitGrowth = $this->calculateGrowth($netProfit, ($lastStats->income - $lastStats->expense));

        $expenseBreakdown = (clone $query)->whereIn('type', ['expense', 'cost_allocation'])
            ->select('category', DB::raw('SUM(amount) as total'))
            ->groupBy('category')
            ->orderByDesc('total')
            ->get();

        $forecastIncome = PlantingCycle::where('status', 'active')
            ->whereBetween('estimated_harvest_date', [Carbon::now(), Carbon::now()->addMonths(1)])
            ->with('commodity')
            ->get()
            ->sum(function ($cycle) {
                return $cycle->current_plant_count * 5000;
            });

        $dailyTrend = (clone $query)
            ->selectRaw('DATE(transaction_date) as date,
                         SUM(CASE WHEN type = "income" THEN amount ELSE 0 END) as income,
                         SUM(CASE WHEN type = "expense" THEN amount ELSE 0 END) as expense')
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        $transactions = (clone $query)->with(['land', 'bed', 'plantingCycle'])->latest('transaction_date')->paginate(10);
        $lands = Land::select('id', 'name')->get();

        return view('finance.index', compact(
            'transactions',
            'lands',
            'startDate',
            'endDate',
            'currentStats',
            'netProfit',
            'incomeGrowth',
            'expenseGrowth',
            'profitGrowth',
            'expenseBreakdown',
            'dailyTrend',
            'forecastIncome'
        ));
    }

    private function calculateGrowth($current, $previous)
    {
        if ($previous == 0) return 100;
        return (($current - $previous) / $previous) * 100;
    }

    public function create()
    {
        $lands = Land::with('sectors.beds')->get();
        return view('finance.create', compact('lands'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'transaction_date' => 'required|date',
            'type' => 'required|in:expense,income,cost_allocation',
            'amount' => 'required|numeric|min:0',
            'category' => 'required|string',
            'scope_level' => 'required|in:land,sector,bed',
            'land_id' => 'required|exists:lands,id',
        ]);

        $data = [
            'user_id' => auth()->id() ?? 1,
            'transaction_date' => $request->transaction_date,
            'type' => $request->type,
            'amount' => $request->amount,
            'category' => $request->category,
            'description' => $request->description,
            'land_id' => $request->land_id,
        ];

        if ($request->scope_level == 'sector') {
            $data['sector_id'] = $request->sector_id;
        } elseif ($request->scope_level == 'bed') {
            $data['sector_id'] = $request->sector_id;
            $data['bed_id'] = $request->bed_id;

            $bed = Bed::find($request->bed_id);
            $activeCycle = $bed->plantingCycles()->where('status', 'active')->first();
            if ($activeCycle) {
                $data['planting_cycle_id'] = $activeCycle->id;
            }
        }

        Transaction::create($data);

        return redirect()->route('finance.index')->with('success', 'Transaksi berhasil dicatat!');
    }

    public function areaReport(Request $request)
    {
        $landId = $request->land_id;
        $sectorId = $request->sector_id;
        $startDate = $request->start_date ?? date('Y-m-01');
        $endDate = $request->end_date ?? date('Y-m-d');

        $lands = Land::with('sectors')->get();

        $query = Transaction::with(['bed', 'plantingCycle.commodity', 'plantingCycle'])
            ->whereBetween('transaction_date', [$startDate, $endDate]);

        if ($landId) $query->where('land_id', $landId);
        if ($sectorId) $query->where('sector_id', $sectorId);

        $transactions = $query->orderBy('transaction_date', 'desc')->get();

        $directIncome = $transactions->where('type', 'income')->sum('amount');

        $cashExpense = $transactions->where('type', 'expense')->sum('amount');
        $internalCost = $transactions->where('type', 'cost_allocation')->sum('amount');

        $directExpense = $cashExpense + $internalCost;

        $allocatedIncome = 0;
        $allocatedExpense = 0;
        $selectedAreaName = "Semua Data";
        $totalAreaSize = 0;
        $allocationNote = "";

        if ($landId) {
            $land = Land::find($landId);
            $totalLandArea = $land->area_size;
            $selectedAreaName = $land->name;
            $totalAreaSize = $totalLandArea;

            if ($sectorId) {
                $sector = $land->sectors->where('id', $sectorId)->first();
                if ($sector) {
                    $selectedAreaName .= " > " . $sector->name;
                    $totalAreaSize = $sector->area_size;

                    $ratio = ($totalLandArea > 0) ? ($sector->area_size / $totalLandArea) : 0;

                    $parentStats = Transaction::where('land_id', $landId)
                        ->whereNull('sector_id')
                        ->whereBetween('transaction_date', [$startDate, $endDate])
                        ->selectRaw('
                            SUM(CASE WHEN type = "income" THEN amount ELSE 0 END) as income,
                            -- Di sini expense + cost_allocation dijumlahkan agar alokasi stok umum (pupuk lahan) terhitung
                            SUM(CASE WHEN type IN ("expense", "cost_allocation") THEN amount ELSE 0 END) as expense
                        ')->first();

                    $allocatedIncome = $parentStats->income * $ratio;
                    $allocatedExpense = $parentStats->expense * $ratio;

                    $allocationNote = "Termasuk alokasi " . round($ratio * 100, 1) . "% dari biaya umum Lahan.";
                } else {
                    $sectorId = null;
                }
            }
        }

        $totalIncome = $directIncome + $allocatedIncome;
        $totalExpense = $directExpense + $allocatedExpense;
        $profit = $totalIncome - $totalExpense;
        $incomePerMeter = $totalAreaSize > 0 ? $totalIncome / $totalAreaSize : 0;
        $expensePerMeter = $totalAreaSize > 0 ? $totalExpense / $totalAreaSize : 0;

        return view('finance.area_report', compact(
            'lands', 'transactions', 'landId', 'sectorId', 'startDate', 'endDate',
            'selectedAreaName', 'totalAreaSize', 'allocationNote',
            'totalIncome', 'totalExpense', 'profit',
            'directIncome', 'directExpense',
            'cashExpense', 'internalCost',
            'allocatedIncome', 'allocatedExpense',
            'incomePerMeter', 'expensePerMeter'
        ));
    }
}
