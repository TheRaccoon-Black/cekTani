<?php

namespace App\Http\Controllers;

use App\Models\Schedule;
use App\Models\Land;
use App\Models\CycleLog;
use App\Models\Bed;
use App\Models\Inventory;
use App\Models\InventoryLog;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Carbon\Carbon;

class ScheduleController extends Controller
{
    public function index(Request $request)
    {
        $lands = Land::with(['sectors.beds.activePlantingCycle'])->get();
        $inventories = Inventory::where('stock', '>', 0)->orderBy('name')->get();

        $events = [];
        $schedules = Schedule::with(['plantingCycle.commodity', 'bed', 'sector', 'land'])
            ->when($request->land_id, function($q) use ($request) {
                $q->where('land_id', $request->land_id);
            })
            ->get();

        foreach ($schedules as $sched) {
            $events[] = [
                'id' => $sched->id,
                'title' => $sched->title,
                'start' => $sched->due_date,
                'backgroundColor' => $sched->status == 'completed' ? '#d3d3d3' : $sched->color,
                'borderColor' => $sched->status == 'completed' ? '#d3d3d3' : $sched->color,
                'extendedProps' => [
                    'status' => $sched->status,
                    'location' => $this->getLocationName($sched),
                    'notes' => $sched->notes,
                    'type' => $sched->type
                ]
            ];
        }

        return view('schedules.index', compact('lands', 'events', 'inventories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required',
            'due_date' => 'required|date',
            'land_id' => 'required|exists:lands,id',
            'sector_id' => 'nullable|exists:sectors,id',
            'bed_id' => 'nullable|exists:beds,id',
        ]);

        $data = $request->all();

        if ($request->bed_id) {
            $bed = Bed::find($request->bed_id);
            if ($bed && $bed->activePlantingCycle) {
                $data['planting_cycle_id'] = $bed->activePlantingCycle->id;
            }
        }

        Schedule::create($data);

        return back()->with('success', 'Jadwal berhasil dibuat!');
    }

    public function complete(Request $request, $id)
    {
        $schedule = Schedule::findOrFail($id);

        $request->validate([
            'quantity' => 'nullable|numeric|min:0',
            'inventory_id' => 'nullable|exists:inventories,id',
        ]);

        $logDescription = "Pengerjaan terjadwal: " . $schedule->title . ". " . $request->notes;

        if ($request->inventory_id && $request->quantity > 0) {
            $item = Inventory::find($request->inventory_id);

            if ($item->stock < $request->quantity) {
                return back()->with('error', 'Gagal! Stok ' . $item->name . ' tersisa ' . $item->stock);
            }

            $cost = $item->avg_price * $request->quantity;
            $item->decrement('stock', $request->quantity);

            InventoryLog::create([
                'inventory_id' => $item->id,
                'type' => 'out',
                'quantity' => $request->quantity,
                'price_per_unit' => $item->avg_price,
                'total_price' => $cost,
                'reference_type' => 'schedule',
                'reference_id' => $schedule->id,
                'notes' => 'Pemakaian untuk jadwal: ' . $schedule->title
            ]);

            Transaction::create([
                'user_id' => auth()->id(),
                'transaction_date' => Carbon::now(),
                'type' => 'cost_allocation',
                'category' => 'Pemakaian Stok',
                'amount' => $cost,
                'description' => "Auto-log material: " . $item->name . " (" . $request->quantity . " " . $item->unit . ")",
                'land_id' => $schedule->land_id,
                'planting_cycle_id' => $schedule->planting_cycle_id,
            ]);

            $logDescription .= " [Material: " . $item->name . " " . $request->quantity . " " . $item->unit . "]";
        }

        $schedule->update([
            'status' => 'completed',
            'notes' => $schedule->notes . "\n[Selesai]: " . $request->notes
        ]);

        if ($schedule->planting_cycle_id) {
            CycleLog::create([
                'planting_cycle_id' => $schedule->planting_cycle_id,
                'log_date' => Carbon::now(),
                'phase' => $this->mapTypeToPhase($schedule->type),
                'activity_description' => $logDescription,
            ]);
        }

        return back()->with('success', 'Tugas selesai, stok terpotong, & biaya tercatat otomatis!');
    }

    private function getLocationName($sched) {
        $loc = $sched->land->name;
        if ($sched->sector) $loc .= ' > ' . $sched->sector->name;
        if ($sched->bed) {
            $loc .= ' > ' . $sched->bed->name;
            if($sched->plantingCycle) $loc .= ' (' . $sched->plantingCycle->commodity->name . ')';
        }
        return $loc;
    }

    private function mapTypeToPhase($type) {
        return match($type) {
            'fertilizing' => 'Vegetatif',
            'pest_control' => 'Hama & Penyakit',
            'harvest' => 'Panen',
            default => 'Pemeliharaan',
        };
    }
}
