<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PlantingCycle;
use App\Models\Transaction;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class AnalysisController extends Controller
{
    public function comparison(Request $request)
    {
        $cycles = PlantingCycle::with(['bed.sector', 'commodity'])
            ->orderBy('start_date', 'desc')
            ->get();

        $dataA = null;
        $dataB = null;
        $error = null;

        if ($request->cycle_a && $request->cycle_b) {
            if ($request->cycle_a == $request->cycle_b) {
                return redirect()->route('analysis.comparison', ['cycle_a' => $request->cycle_a])
                    ->with('error', 'Silakan pilih dua siklus yang berbeda untuk dibandingkan.');
            }

            $dataA = $this->calculateMetrics($request->cycle_a);
            $dataB = $this->calculateMetrics($request->cycle_b);
        } elseif ($request->cycle_a) {
            $dataA = $this->calculateMetrics($request->cycle_a);
        }

        return view('analysis.comparison', compact('cycles', 'dataA', 'dataB'));
    }

    private function calculateMetrics($cycleId)
    {
        $cycle = PlantingCycle::with(['commodity', 'bed.sector'])->find($cycleId);

        if (!$cycle) return null;

        $transactions = Transaction::where('planting_cycle_id', $cycleId)->get();

        $income = $transactions->where('type', 'income')->sum('amount');
        $expense = $transactions->where('type', 'expense')->sum('amount');
        $profit = $income - $expense;

        $categories = $transactions->where('type', 'expense')
            ->groupBy('category')
            ->map(fn($row) => $row->sum('amount'));

        $startDate = Carbon::parse($cycle->start_date);
        $endDate = $cycle->actual_harvest_date ? Carbon::parse($cycle->actual_harvest_date) : Carbon::now();
        $duration = $startDate->diffInDays($endDate);
        $duration = $duration < 1 ? 1 : $duration;

        $initialPop = $cycle->initial_plant_count > 0 ? $cycle->initial_plant_count : 1;
        $currentPop = $cycle->current_plant_count;

        $survivalRate = ($currentPop / $initialPop) * 100;

        return [
            'id' => $cycle->id,
            'title' => $cycle->commodity->name,
            'subtitle' => $cycle->bed->sector->name . ' - ' . $cycle->bed->name,
            'status' => $cycle->status,
            'date_info' => $startDate->format('d M Y') . ' (' . $duration . ' Hari)',

            'total_income' => $income,
            'total_expense' => $expense,
            'net_profit' => $profit,

            'roi' => $this->safeDiv($profit, $expense) * 100,
            'margin' => $this->safeDiv($profit, $income) * 100,

            'cost_per_plant' => $this->safeDiv($expense, $currentPop),
            'profit_per_plant' => $this->safeDiv($profit, $currentPop),

            'survival_rate' => $survivalRate,
            'daily_burn_rate' => $this->safeDiv($expense, $duration), // Biaya per hari

            'breakdown' => [
                'pupuk' => $categories['Pupuk'] ?? 0,
                'obat' => ($categories['Pestisida'] ?? 0) + ($categories['Obat'] ?? 0),
                'tenaga' => $categories['Tenaga Kerja'] ?? 0,
                'lainnya' => $transactions->where('type', 'expense')
                            ->whereNotIn('category', ['Pupuk', 'Pestisida', 'Obat', 'Tenaga Kerja'])
                            ->sum('amount')
            ]
        ];
    }

    private function safeDiv($numerator, $denominator)
    {
        return $denominator != 0 ? $numerator / $denominator : 0;
    }
}
