<?php

namespace App\Http\Controllers;

use App\Models\Bed;
use App\Models\Commodity;
use App\Models\PlantingCycle;
use Illuminate\Http\Request;
use Carbon\Carbon;

class PlantingCycleController extends Controller
{
    public function create(Bed $bed)
    {
        // Cek apakah bedengan ini sedang dipakai?
        // Jika ada siklus 'active', tolak user menanam lagi.
        $activeCycle = $bed->plantingCycles()->where('status', 'active')->first();
        if ($activeCycle) {
            return redirect()->back()->withErrors('Bedengan ini sedang aktif ditanami! Panen dulu sebelum tanam baru.');
        }

        $commodities = Commodity::all();
        return view('cycles.create', compact('bed', 'commodities'));
    }

    public function store(Request $request, Bed $bed)
    {
        $request->validate([
            'commodity_id' => 'required|exists:commodities,id',
            'start_date' => 'required|date',
            'initial_plant_count' => 'required|integer|min:1',
        ]);

        // 1. Ambil Data Komoditas untuk hitung panen
        $commodity = Commodity::findOrFail($request->commodity_id);

        // 2. Hitung Estimasi Panen
        // Rumus: Tanggal Tanam + Durasi Panen Komoditas
        $startDate = Carbon::parse($request->start_date);
        $estHarvestDate = $startDate->copy()->addDays($commodity->harvest_duration_days);

        // 3. Simpan
        PlantingCycle::create([
            'bed_id' => $bed->id,
            'commodity_id' => $commodity->id,
            'start_date' => $startDate,
            'estimated_harvest_date' => $estHarvestDate,
            'initial_plant_count' => $request->initial_plant_count,
            'current_plant_count' => $request->initial_plant_count, // Awal tanam = jumlah hidup
            'status' => 'active',
        ]);

        // Kembali ke halaman list bedengan di sektor tersebut
        return redirect()->route('sectors.beds.index', $bed->sector_id)
            ->with('success', 'Berhasil mulai tanam ' . $commodity->name . '!');
    }

    public function harvest(Request $request, $id)
    {
        // Cari siklus tanam berdasarkan ID
        $cycle = PlantingCycle::findOrFail($id);

        // Ubah status menjadi 'harvested' (Panen Selesai)
        // Data ini TIDAK DIHAPUS, tapi disimpan sebagai riwayat.
        $cycle->update([
            'status' => 'harvested',
            // Opsional: Anda bisa update 'current_plant_count' jadi 0 jika mau
        ]);

        // Kembali ke halaman bedengan
        return redirect()->back()->with('success', 'Siklus tanam selesai! Data masuk ke riwayat.');
    }

    public function show($id)
    {
        // Ambil data siklus beserta log-nya (urutkan dari yang terbaru)
        $cycle = PlantingCycle::with(['bed.sector.land', 'commodity', 'logs' => function ($q) {
            $q->orderBy('log_date', 'desc')->orderBy('created_at', 'desc');
        }])->findOrFail($id);

        return view('cycles.show', compact('cycle'));
    }
}
