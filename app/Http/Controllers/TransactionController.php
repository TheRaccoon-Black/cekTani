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
    // Halaman List Transaksi (Laporan Pusat)
    public function index(Request $request)
    {
        // 1. FILTER RANGE
        $startDate = $request->start_date ?? Carbon::now()->startOfMonth()->format('Y-m-d');
        $endDate = $request->end_date ?? Carbon::now()->endOfMonth()->format('Y-m-d');

        // 2. QUERY DASAR
        $query = Transaction::whereBetween('transaction_date', [$startDate, $endDate]);
        if ($request->land_id) {
            $query->where('land_id', $request->land_id);
        }

        // 3. HITUNG KPI UTAMA (Current Period)
        $currentStats = (clone $query)->selectRaw('
            SUM(CASE WHEN type = "income" THEN amount ELSE 0 END) as income,
            SUM(CASE WHEN type = "expense" THEN amount ELSE 0 END) as expense
        ')->first();

        $netProfit = $currentStats->income - $currentStats->expense;

        // 4. HITUNG PERBANDINGAN (Last Period) - Untuk Insight "Naik/Turun"
        // Logika: Jika filter bulan ini, bandingkan dengan bulan lalu
        $startLast = Carbon::parse($startDate)->subMonth()->format('Y-m-d');
        $endLast = Carbon::parse($endDate)->subMonth()->format('Y-m-d');

        $lastStats = Transaction::whereBetween('transaction_date', [$startLast, $endLast])
            ->when($request->land_id, function($q) use ($request) { $q->where('land_id', $request->land_id); })
            ->selectRaw('
                SUM(CASE WHEN type = "income" THEN amount ELSE 0 END) as income,
                SUM(CASE WHEN type = "expense" THEN amount ELSE 0 END) as expense
            ')->first();

        // Hitung Persentase Pertumbuhan
        $incomeGrowth = $this->calculateGrowth($currentStats->income, $lastStats->income);
        $expenseGrowth = $this->calculateGrowth($currentStats->expense, $lastStats->expense);
        $profitGrowth = $this->calculateGrowth($netProfit, ($lastStats->income - $lastStats->expense));

        // 5. BREAKDOWN PENGELUARAN (Pie Chart Analysis)
        // Melihat pos biaya terbesar (Inefisiensi Check)
        $expenseBreakdown = (clone $query)->where('type', 'expense')
            ->select('category', DB::raw('SUM(amount) as total'))
            ->groupBy('category')
            ->orderByDesc('total')
            ->get();

        // 6. FORECASTING (PREDIKSI) PENDAPATAN
        // Mengambil siklus tanam yang estimasi panennya masuk dalam range filter atau bulan depan
        $forecastIncome = PlantingCycle::where('status', 'active')
            ->whereBetween('estimated_harvest_date', [Carbon::now(), Carbon::now()->addMonths(1)])
            ->with('commodity') // Asumsi ada data harga estimasi di komoditas
            ->get()
            ->sum(function($cycle) {
                // Rumus kasar: Populasi x Estimasi Harga Jual (Misal 5000/tanaman)
                // Idealnya di tabel commodity ada field 'estimated_price'
                return $cycle->current_plant_count * 5000;
            });

        // 7. CHART TREND HARIAN (Area Chart)
        $dailyTrend = (clone $query)
            ->selectRaw('DATE(transaction_date) as date,
                         SUM(CASE WHEN type = "income" THEN amount ELSE 0 END) as income,
                         SUM(CASE WHEN type = "expense" THEN amount ELSE 0 END) as expense')
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        // Data Tables
        $transactions = (clone $query)->with(['land', 'bed'])->latest('transaction_date')->paginate(10);
        $lands = Land::select('id', 'name')->get();

        return view('finance.index', compact(
            'transactions', 'lands', 'startDate', 'endDate',
            'currentStats', 'netProfit',
            'incomeGrowth', 'expenseGrowth', 'profitGrowth',
            'expenseBreakdown', 'dailyTrend', 'forecastIncome'
        ));
    }

    private function calculateGrowth($current, $previous)
    {
        if ($previous == 0) return 100; // Jika sebelumnya 0, anggap naik 100%
        return (($current - $previous) / $previous) * 100;
    }

    // Halaman Form Catat Transaksi (Pusat)
    public function create()
    {
        $lands = Land::with('sectors.beds')->get();
        return view('finance.create', compact('lands'));
    }

    // Proses Simpan Transaksi (Logic Penentuan Level)
    public function store(Request $request)
    {
        $request->validate([
            'transaction_date' => 'required|date',
            'type' => 'required|in:expense,income',
            'amount' => 'required|numeric|min:0',
            'category' => 'required|string',
            'scope_level' => 'required|in:land,sector,bed', // Input hidden atau radio button
            'land_id' => 'required|exists:lands,id',
        ]);

        $data = [
            'user_id' => auth()->id() ?? 1, // Sesuaikan auth
            'transaction_date' => $request->transaction_date,
            'type' => $request->type,
            'amount' => $request->amount,
            'category' => $request->category,
            'description' => $request->description,
            'land_id' => $request->land_id,
        ];

        // LOGIKA LEVEL (HIERARKI)
        if ($request->scope_level == 'sector') {
            $data['sector_id'] = $request->sector_id;
        }
        elseif ($request->scope_level == 'bed') {
            $data['sector_id'] = $request->sector_id;
            $data['bed_id'] = $request->bed_id;

            // Cek apakah ada tanaman aktif? Jika ya, tempelkan ID-nya
            $bed = Bed::find($request->bed_id);
            $activeCycle = $bed->plantingCycles()->where('status', 'active')->first();
            if($activeCycle) {
                $data['planting_cycle_id'] = $activeCycle->id;
            }
        }

        Transaction::create($data);

        return redirect()->route('finance.index')->with('success', 'Transaksi berhasil dicatat!');
    }

    // Simpan Transaksi Khusus dari Dashboard Siklus (Shortcut)
    public function storeForCycle(Request $request, $cycleId)
    {
        $cycle = PlantingCycle::with('bed.sector')->findOrFail($cycleId);

        Transaction::create([
            'user_id' => auth()->id() ?? 1,
            'planting_cycle_id' => $cycle->id,
            'bed_id' => $cycle->bed_id,
            'sector_id' => $cycle->bed->sector_id,
            'land_id' => $cycle->bed->sector->land_id,
            'transaction_date' => $request->transaction_date,
            'type' => $request->type,
            'amount' => $request->amount,
            'category' => $request->category,
            'description' => $request->description,
        ]);

        return redirect()->back()->with('success', 'Keuangan tercatat!');
    }
}
