<?php

namespace App\Http\Controllers;

use App\Models\PlantingCycle;
use App\Models\CycleLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class CycleLogController extends Controller
{
    public function store(Request $request, $plantingCycleId)
    {
        $request->validate([
            'log_date' => 'required|date',
            'phase' => 'required|string', // Vegetatif, Generatif, dll
            'activity' => 'required|string',
            'notes' => 'nullable|string',
            'photo' => 'nullable|image|max:2048', // Max 2MB
        ]);

        $data = $request->except('photo');
        $data['planting_cycle_id'] = $plantingCycleId;

        if ($request->hasFile('photo')) {
            $path = $request->file('photo')->store('cycle_logs', 'public');
            $data['photo_path'] = $path;
        }

        CycleLog::create($data);

        return redirect()->back()->with('success', 'Catatan harian berhasil disimpan!');
    }

    public function destroy($id)
    {
        $log = CycleLog::findOrFail($id);

        if ($log->photo_path) {
            Storage::disk('public')->delete($log->photo_path);
        }

        $log->delete();
        return redirect()->back()->with('success', 'Catatan dihapus.');
    }
}
