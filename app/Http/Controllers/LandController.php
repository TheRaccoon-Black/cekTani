<?php

namespace App\Http\Controllers;

use App\Models\Land;
use Illuminate\Http\Request;

class LandController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $lands = Land::where('user_id', auth()->id())->latest()->paginate(10);
        return view('lands.index', compact('lands'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $existingLands = Land::select('id', 'name', 'geojson_data', 'area_size')->get();

    return view('lands.create', compact('existingLands'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'address' => 'nullable|string',
            'geojson_data' => 'required|json',
            'area_size' => 'nullable|numeric',
        ]);

        Land::create([
            'user_id' => auth()->id(),
            'name' => $request->name,
            'address' => $request->address,
            'geojson_data' => json_decode($request->geojson_data),
            'area_size' => $request->area_size ?? 0,
        ]);

        return redirect()->route('lands.index')->with('success', 'Lahan berhasil dipetakan!');

    }

    /**
     * Display the specified resource.
     */
    public function show(Land $land)
    {
        return view('lands.show', compact('land'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Land $land)
{
    // Pastikan hanya pemilik yang bisa edit
    if ($land->user_id !== auth()->id()) {
        abort(403);
    }
    return view('lands.edit', compact('land'));
}

public function update(Request $request, Land $land)
{
    // Validasi input
    $request->validate([
        'name' => 'required|string|max:255',
        'geojson_data' => 'required', // Harus ada data peta
    ]);

    // Hitung luas baru jika bentuk berubah (opsional, tapi disarankan)
    // Disini kita simpan raw datanya dulu

    $land->update([
        'name' => $request->name,
        'address' => $request->address,
        'description' => $request->description,
        'geojson_data' => $request->geojson_data,
        'area_size' => $request->area_size ?? 0,
    ]);

    return redirect()->route('lands.index')->with('success', 'Data lahan berhasil diperbarui.');
}

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Land $land)
    {
        if ($land->user_id !== auth()->id()) {
        abort(403);
    }

    $land->delete();

    return redirect()->route('lands.index')->with('success', 'Data lahan berhasil dihapus.');
    }

    public function dashboardMap()
{
    // Ambil data lahan urut dari terbaru
    $lands = Land::latest()->get();

    // Hitung total luas (dalam m2)
    $totalAreaM2 = $lands->sum('area_size');

    // Konversi ke Hektar (1 Ha = 10.000 m2)
    // Jika ingin tetap m2, hapus pembagiannya
    $totalAreaHa = $totalAreaM2 / 10000;

    return view('lands.dashboard-map', compact('lands', 'totalAreaHa', 'totalAreaM2'));
}
public function mapSectors($id)
{
    $land = Land::with('sectors')->findOrFail($id);

    // Hitung statistik untuk dikirim ke view
    $totalLandArea = $land->area_size;
    $usedArea = $land->sectors->sum('area_size'); // Pastikan kolom area_size sudah dibuat di db
    $remainingArea = $totalLandArea - $usedArea;
    $usagePercentage = ($totalLandArea > 0) ? ($usedArea / $totalLandArea) * 100 : 0;

    return view('lands.map_sectors', compact('land', 'usedArea', 'remainingArea', 'usagePercentage'));
}

public function storeSector(Request $request, $id)
{
    // Validasi dan simpan (seperti kode langkah sebelumnya)
    $request->validate([
        'name' => 'required',
        'geojson_data' => 'required',
        'area_size' => 'required|numeric'
    ]);

    $land = Land::findOrFail($id);
    $land->sectors()->create([
        'name' => $request->name,
        'geojson_data' => $request->geojson_data,
        'area_size' => $request->area_size
    ]);

    return redirect()->back()->with('success', 'Sektor lahan berhasil disimpan.');
}
public function destroySector($id)
{
    $sector = \App\Models\Sector::findOrFail($id);
    $sector->delete();

    return redirect()->back()->with('success', 'Sektor berhasil dihapus.');
}
}
