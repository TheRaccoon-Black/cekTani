<?php

namespace App\Http\Controllers;

use App\Models\ShoppingSession;
use App\Models\ShoppingItem;
use App\Models\Inventory;
use App\Models\InventoryLog;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Carbon\Carbon;

class ShoppingListController extends Controller
{
    public function index()
    {
        $sessions = ShoppingSession::with('items')
            ->orderByRaw("FIELD(status, 'active', 'archived')")
            ->orderBy('created_at', 'desc')
            ->get();

        return view('shopping.index', compact('sessions'));
    }

    public function storeSession(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'planning_date' => 'nullable|date',
        ]);

        ShoppingSession::create([
            'name' => $request->name,
            'planning_date' => $request->planning_date ?? Carbon::now(),
            'status' => 'active'
        ]);

        return back()->with('success', 'Sesi belanja baru berhasil dibuat.');
    }

    public function destroySession($id)
    {
        $session = ShoppingSession::findOrFail($id);
        $session->delete();

        return redirect()->route('shopping.index')->with('success', 'Sesi belanja dihapus.');
    }

    public function show($id)
    {
        $session = ShoppingSession::with('items')->findOrFail($id);

        $inventories = Inventory::orderBy('name')->get();

        $lands = \App\Models\Land::with('sectors.beds')->get();

        return view('shopping.show', compact('session', 'inventories', 'lands'));
    }

    public function storeItem(Request $request, $sessionId)
    {
        $request->validate([
            'name' => 'required|string',
            'quantity' => 'required|numeric|min:0.01',
            'type' => 'required|in:stock,direct',
            'land_id' => 'nullable|exists:lands,id',
            'sector_id' => 'nullable|exists:sectors,id',
            'bed_id' => 'nullable|exists:beds,id',
        ]);

        $itemName = $request->name;
        $itemUnit = $request->unit ?? 'pcs';

        if ($request->inventory_id) {
            $existingInv = Inventory::find($request->inventory_id);
            if ($existingInv) {
                $itemName = $existingInv->name;
                $itemUnit = $existingInv->unit;
            }
        }

        ShoppingItem::create([
            'shopping_session_id' => $sessionId,
            'inventory_id' => $request->inventory_id,
            'name' => $itemName,
            'quantity' => $request->quantity,
            'unit' => $itemUnit,
            'estimated_price' => $request->estimated_price ?? 0,
            'type' => $request->type,
            'url' => $request->url,
            'land_id' => $request->land_id,
            'sector_id' => $request->sector_id,
            'bed_id' => $request->bed_id,
            'is_purchased' => false
        ]);

        return back()->with('success', 'Item ditambahkan.');
    }

    public function purchaseItem(Request $request, $id)
    {
        $item = ShoppingItem::findOrFail($id);

        $request->validate([
            'actual_price' => 'required|numeric|min:0',
            'actual_qty' => 'required|numeric|min:0.01',
        ]);

        $totalCost = $request->actual_price * $request->actual_qty;

        if ($item->type == 'stock') {
            $inventory = null;
            if ($item->inventory_id) {
                $inventory = Inventory::find($item->inventory_id);
            }
            if (!$inventory) {
                $inventory = Inventory::create([
                    'name' => $item->name,
                    'category' => 'Umum',
                    'unit' => $item->unit,
                    'stock' => 0,
                    'avg_price' => 0
                ]);
                $item->update(['inventory_id' => $inventory->id]);
            }
            $newAvgPrice = $inventory->calculateNewAvgPrice($request->actual_qty, $request->actual_price);
            $inventory->update(['stock' => $inventory->stock + $request->actual_qty, 'avg_price' => $newAvgPrice]);
            InventoryLog::create([
                'inventory_id' => $inventory->id, 'type' => 'in', 'quantity' => $request->actual_qty,
                'price_per_unit' => $request->actual_price, 'total_price' => $totalCost,
                'reference_type' => 'manual', 'notes' => 'Pembelian via Sesi: ' . ($item->session->name ?? 'Belanja')
            ]);
        }

        $plantingCycleId = null;
        if ($item->bed_id) {
            $bed = \App\Models\Bed::find($item->bed_id);
            if ($bed && $bed->activePlantingCycle) {
                $plantingCycleId = $bed->activePlantingCycle->id;
            }
        }

        Transaction::create([
            'user_id' => auth()->id() ?? 1,
            'transaction_date' => Carbon::now(),
            'type' => 'expense',
            'category' => ($item->type == 'stock') ? 'Belanja Stok' : 'Operasional/Jasa',
            'amount' => $totalCost,
            'description' => "Beli: {$item->name} ({$request->actual_qty} {$item->unit})",

            'land_id' => $item->land_id,
            'sector_id' => $item->sector_id,
            'bed_id' => $item->bed_id,
            'planting_cycle_id' => $plantingCycleId,
        ]);

        $item->update([
            'is_purchased' => true,
            'quantity' => $request->actual_qty,
            'estimated_price' => $request->actual_price
        ]);

        return back()->with('success', 'Transaksi tercatat! Biaya dialokasikan sesuai lokasi.');
    }

}
