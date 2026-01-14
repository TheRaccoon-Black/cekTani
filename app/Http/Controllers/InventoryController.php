<?php

namespace App\Http\Controllers;

use App\Models\Inventory;
use App\Models\InventoryLog;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Carbon\Carbon;

class InventoryController extends Controller
{
    public function index()
    {
        $items = Inventory::orderBy('category')->get();
        return view('inventory.index', compact('items'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'category' => 'required',
            'unit' => 'required',
        ]);
        Inventory::create($request->all());
        return back()->with('success', 'Barang baru terdaftar di gudang.');
    }

    public function purchase(Request $request, $id)
    {
        $request->validate([
            'quantity' => 'required|numeric|min:0.1',
            'price_per_unit' => 'required|numeric|min:0',
        ]);

        $item = Inventory::findOrFail($id);
        $totalCost = $request->quantity * $request->price_per_unit;

        $trx = Transaction::create([
            'user_id' => auth()->id(),
            'transaction_date' => Carbon::now(),
            'type' => 'expense',
            'category' => 'Belanja Stok',
            'amount' => $totalCost,
            'description' => "Beli Stok: {$item->name} ({$request->quantity} {$item->unit})",
        ]);

        $newAvgPrice = $item->calculateNewAvgPrice($request->quantity, $request->price_per_unit);

        $item->update([
            'stock' => $item->stock + $request->quantity,
            'avg_price' => $newAvgPrice
        ]);

        InventoryLog::create([
            'inventory_id' => $item->id,
            'type' => 'in',
            'quantity' => $request->quantity,
            'price_per_unit' => $request->price_per_unit,
            'total_price' => $totalCost,
            'reference_type' => 'transaction',
            'reference_id' => $trx->id,
            'notes' => 'Pembelian Stok Baru'
        ]);

        return back()->with('success', 'Stok bertambah & Keuangan tercatat!');
    }
}
