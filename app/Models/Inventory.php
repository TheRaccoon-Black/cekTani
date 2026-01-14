<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Inventory extends Model
{
    use HasFactory;
    protected $guarded = ['id'];

    public function logs() {
        return $this->hasMany(InventoryLog::class)->orderBy('created_at', 'desc');
    }

    public function calculateNewAvgPrice($newQty, $newPrice)
    {
        $currentVal = $this->stock * $this->avg_price;
        $newVal = $newQty * $newPrice;
        $totalStock = $this->stock + $newQty;

        if ($totalStock <= 0) return $newPrice;

        return ($currentVal + $newVal) / $totalStock;
    }
}
