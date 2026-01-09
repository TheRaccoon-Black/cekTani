<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PlantingCycle extends Model
{
    use HasFactory;

    protected $guarded = ['id'];
    protected $dates = ['start_date', 'estimated_harvest_date'];

    // Relasi ke Keuangan
    public function transactions() {
        return $this->hasMany(Transaction::class);
    }

    // Menghitung Total Biaya untuk siklus ini (HPP)
    public function getTotalExpenseAttribute() {
        return $this->transactions()->where('type', 'expense')->sum('amount');
    }

    // Menghitung Total Omzet Panen siklus ini
    public function getTotalIncomeAttribute() {
        return $this->transactions()->where('type', 'income')->sum('amount');
    }
}
