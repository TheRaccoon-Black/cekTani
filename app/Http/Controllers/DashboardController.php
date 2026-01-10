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
        // --- 0. FILTERING OPTIONS (Fitur Baru) ---
        $selectedLandId = $request->input('land_id');
        $selectedYear = $request->input('year', date('Y'));
        $selectedMonth = $request->input('month', date('m')); // Default bulan ini

        // Query Base untuk Transaksi (Agar bisa difilter)
        $transactionQuery = Transaction::query()
            ->whereYear('transaction_date', $selectedYear);

        // Query Base untuk Siklus Tanam
        $cycleQuery = PlantingCycle::where('status', 'active');

        // Terapkan Filter Lahan jika dipilih
        if ($selectedLandId) {
            $transactionQuery->where('land_id', $selectedLandId);
            $cycleQuery->whereHas('bed.sector', function ($q) use ($selectedLandId) {
                $q->where('land_id', $selectedLandId);
            });
        }

        // --- 1. RINGKASAN ASET (Berdasarkan Filter) ---
        $totalLands = Land::count(); // Tetap global
        $totalSectors = $selectedLandId ? Sector::where('land_id', $selectedLandId)->count() : Sector::count();
        $totalBeds = $selectedLandId ? Bed::whereHas('sector', function ($q) use ($selectedLandId) {
            $q->where('land_id', $selectedLandId);
        })->count() : Bed::count();

        // --- 2. OPERASIONAL & EFISIENSI ---
        $activeCycles = $cycleQuery->with('commodity', 'bed.sector.land')->get();
        $totalPlants = $activeCycles->sum('current_plant_count');
        $activeBedsCount = $activeCycles->count();
        $occupancyRate = ($totalBeds > 0) ? ($activeBedsCount / $totalBeds) * 100 : 0;

        // --- 3. KEUANGAN (FILTERED) ---
        // Clone query agar tidak tumpang tindih
        $incomeTotal = (clone $transactionQuery)->where('type', 'income')->sum('amount');
        $expenseTotal = (clone $transactionQuery)->where('type', 'expense')->sum('amount');
        $netProfit = $incomeTotal - $expenseTotal;

        // Hitung Average Cost per Plant (Efisiensi)
        // Total Pengeluaran dibagi Total Tanaman Aktif (Indikator kasar HPP berjalan)
        $costPerPlant = ($totalPlants > 0) ? ($expenseTotal / $totalPlants) : 0;

        // --- 4. INSIGHT: STRUKTUR BIAYA (Dimana uang habis?) ---
        $expenseBreakdown = (clone $transactionQuery)
            ->where('type', 'expense')
            ->select('category', DB::raw('sum(amount) as total'))
            ->groupBy('category')
            ->orderBy('total', 'desc')
            ->get();

        $costCategories = $expenseBreakdown->pluck('category');
        $costValues = $expenseBreakdown->pluck('total');

        // --- 5. INSIGHT: KESEHATAN TANAMAN (Dari Log Jurnal) ---
        // Menghitung jumlah isu (Hama/Penyakit) vs Kegiatan Rutin
        $logsQuery = CycleLog::query();
        if ($selectedLandId) {
            $logsQuery->whereHas('plantingCycle.bed.sector', function ($q) use ($selectedLandId) {
                $q->where('land_id', $selectedLandId);
            });
        }
        $logStats = $logsQuery->select('phase', DB::raw('count(*) as total'))
            ->groupBy('phase')
            ->pluck('total', 'phase');

        // --- 6. CHART CASHFLOW (Tetap 12 Bulan dalam Tahun Terpilih) ---
        $chartLabels = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
        $incomeData = [];
        $expenseData = [];

        for ($m = 1; $m <= 12; $m++) {
            $incomeData[] = (clone $transactionQuery)->whereMonth('transaction_date', $m)->where('type', 'income')->sum('amount');
            $expenseData[] = (clone $transactionQuery)->whereMonth('transaction_date', $m)->where('type', 'expense')->sum('amount');
        }

        // --- 7. DATA PENDUKUNG LAIN ---
        $lands = Land::all(); // Untuk dropdown filter
        $alerts = $activeCycles->filter(function ($cycle) {
            return Carbon::now()->greaterThan(Carbon::parse($cycle->estimated_harvest_date));
        });

        // Tanaman Terbanyak
        $topCommodity = $activeCycles->groupBy('commodity.name')
            ->sortByDesc(function ($group) {
                return $group->count();
            })
            ->first();
        $topCommodityName = $topCommodity ? $activeCycles->where('commodity_id', $topCommodity->first()->commodity_id)->first()->commodity->name : 'N/A';

        $harvestHistory = PlantingCycle::where('status', 'harvested')
            // Filter Lahan jika ada
            ->when($selectedLandId, function ($q) use ($selectedLandId) {
                $q->whereHas('bed.sector', function ($sq) use ($selectedLandId) {
                    $sq->where('land_id', $selectedLandId);
                });
            })
            ->with(['commodity', 'bed.sector', 'transactions']) // Eager load transaksi untuk hitung duit
            ->orderBy('updated_at', 'desc') // Asumsi updated_at adalah waktu panen selesai
            ->take(5)
            ->get()
            ->map(function ($cycle) {
                // Hitung manual total income dari siklus ini
                $income = $cycle->transactions->where('type', 'income')->sum('amount');
                $expense = $cycle->transactions->where('type', 'expense')->sum('amount');

                $cycle->real_profit = $income - $expense;
                // Metric Paling Penting: Berapa rupiah per pohon?
                $cycle->rev_per_plant = ($cycle->initial_plant_count > 0) ? ($income / $cycle->initial_plant_count) : 0;

                return $cycle;
            });

        // --- 9. TAMBAHAN INSIGHT: LIVE FEED LAPANGAN (LOGS) ---
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
            'selectedYear', // Data Filter
            'totalLands',
            'totalSectors',
            'totalBeds',
            'occupancyRate',
            'totalPlants',
            'activeBedsCount',
            'topCommodityName',
            'incomeTotal',
            'expenseTotal',
            'netProfit',
            'costPerPlant',
            'costCategories',
            'costValues', // Pie Chart Data
            'logStats', // Bar Chart Data
            'chartLabels',
            'incomeData',
            'expenseData', // Line Chart Data
            'alerts', 'harvestHistory', 'recentLogs'
        ));
    }
}
