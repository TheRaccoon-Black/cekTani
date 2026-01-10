<?php

namespace App\Http\Controllers;

use App\Models\Bed;
use App\Models\Sector;
use Illuminate\Http\Request;

class BedController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index($sectorId)
    {
        $sector = Sector::with(['beds', 'land'])->findOrFail($sectorId);

        return view('beds.index', compact('sector'));
    }

    public function store(Request $request, $sectorId)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'width' => 'required|numeric|min:0',
            'length' => 'required|numeric|min:0',
            'max_capacity' => 'required|integer|min:0',
        ]);

        $bed = new Bed();
        $bed->sector_id = $sectorId;
        $bed->name = $request->name;
        $bed->width = $request->width;
        $bed->length = $request->length;
        $bed->max_capacity = $request->max_capacity;
        $bed->save();

        return redirect()->back()->with('success', 'Bedengan berhasil ditambahkan!');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
{
    $bed = Bed::with('sector.land')->findOrFail($id);
    return view('beds.edit', compact('bed'));
}

public function update(Request $request, $id)
{
    $request->validate([
        'name' => 'required|string|max:255',
        'width' => 'required|numeric|min:0',
        'length' => 'required|numeric|min:0',
        'max_capacity' => 'required|integer|min:0',
    ]);

    $bed = Bed::findOrFail($id);
    $bed->update([
        'name' => $request->name,
        'width' => $request->width,
        'length' => $request->length,
        'max_capacity' => $request->max_capacity,
    ]);

    return redirect()->route('sectors.beds.index', $bed->sector_id)
                     ->with('success', 'Data bedengan berhasil diperbarui.');
}

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        Bed::destroy($id);
        return redirect()->back()->with('success', 'Bedengan dihapus.');
    }

    public function history($id)
{
    // Ambil bedengan beserta siklus tanam yang SUDAH SELESAI (harvested)
    // Diurutkan dari yang paling baru selesai
    $bed = Bed::with(['plantingCycles' => function($query) {
        $query->where('status', 'harvested')
              ->with('commodity') // Load nama tanaman
              ->orderBy('updated_at', 'desc');
    }, 'sector.land'])->findOrFail($id);

    return view('beds.history', compact('bed'));
}


}
