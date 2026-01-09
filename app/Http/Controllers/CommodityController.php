<?php

namespace App\Http\Controllers;

use App\Models\Commodity;
use Illuminate\Http\Request;

class CommodityController extends Controller
{
    public function index()
    {
        $commodities = Commodity::latest()->get();
        return view('commodities.index', compact('commodities'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'variety' => 'nullable|string|max:255',
            'harvest_duration_days' => 'required|integer|min:1',
        ]);

        Commodity::create($request->all());

        return redirect()->route('commodities.index')
                         ->with('success', 'Komoditas berhasil ditambahkan.');
    }

    public function edit(Commodity $commodity)
    {
        return view('commodities.edit', compact('commodity'));
    }

    public function update(Request $request, Commodity $commodity)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'variety' => 'nullable|string|max:255',
            'harvest_duration_days' => 'required|integer|min:1',
        ]);

        $commodity->update($request->all());

        return redirect()->route('commodities.index')
                         ->with('success', 'Data komoditas diperbarui.');
    }

    public function destroy(Commodity $commodity)
    {
        $commodity->delete();
        return redirect()->route('commodities.index')
                         ->with('success', 'Komoditas dihapus.');
    }
}
