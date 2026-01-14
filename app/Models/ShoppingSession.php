<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ShoppingSession extends Model
{
    use HasFactory;
    protected $guarded = ['id'];

    public function items()
    {
        return $this->hasMany(ShoppingItem::class);
    }

    public function getTotalEstimatedAttribute()
    {
        return $this->items->sum(function($item) {
            return $item->quantity * $item->estimated_price;
        });
    }

    public function getTotalSpentAttribute()
    {
        return $this->items->where('is_purchased', true)->sum(function($item) {
            return $item->quantity * $item->estimated_price;
        });
    }

    public function getProgressAttribute()
    {
        $total = $this->items->count();
        if($total == 0) return 0;
        $purchased = $this->items->where('is_purchased', true)->count();
        return round(($purchased / $total) * 100);
    }
}
