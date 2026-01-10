<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use HasFactory;
    protected $guarded = ['id'];

    protected $dates = ['transaction_date'];

    // Relasi ke Owner
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function land()
    {
        return $this->belongsTo(Land::class);
    }

    public function sector()
    {
        return $this->belongsTo(Sector::class);
    }

    public function bed()
    {
        return $this->belongsTo(Bed::class);
    }

    public function plantingCycle()
    {
        return $this->belongsTo(PlantingCycle::class);
    }

    // 1. Biaya Langsung (Khusus Bedengan Ini)
    public function getDirectExpenseAttribute()
    {
        return $this->transactions()->where('type', 'expense')->sum('amount');
    }

    
    // 3. Alokasi Beban Sektor (Shared Cost)
    public function getSectorOverheadAttribute()
    {
        $sectorId = $this->bed->sector_id;

        // Cari biaya sektor yg 'general' (bed_id null)
        $totalSectorExpense = Transaction::where('sector_id', $sectorId)
            ->whereNull('bed_id')
            ->where('type', 'expense')
            // Hitung hanya transaksi yang terjadi selama masa tanam ini
            ->whereBetween('transaction_date', [$this->start_date, $this->estimated_harvest_date])
            ->sum('amount');

        if ($totalSectorExpense == 0) return 0;

        // Bagi rata dengan jumlah bedengan di sektor tersebut
        $totalBeds = Bed::where('sector_id', $sectorId)->count();
        return $totalBeds > 0 ? ($totalSectorExpense / $totalBeds) : 0;
    }

    // 4. Alokasi Beban Lahan (Shared Cost Global)
    public function getLandOverheadAttribute()
    {
        $landId = $this->bed->sector->land_id;

        // Cari biaya lahan yg 'general' (sector_id & bed_id null)
        $totalLandExpense = Transaction::where('land_id', $landId)
            ->whereNull('sector_id')
            ->whereNull('bed_id')
            ->where('type', 'expense')
            ->whereBetween('transaction_date', [$this->start_date, $this->estimated_harvest_date])
            ->sum('amount');

        if ($totalLandExpense == 0) return 0;

        // Bagi rata dengan TOTAL bedengan di lahan tersebut
        $totalBedsInLand = Bed::whereHas('sector', function($q) use ($landId) {
            $q->where('land_id', $landId);
        })->count();

        return $totalBedsInLand > 0 ? ($totalLandExpense / $totalBedsInLand) : 0;
    }

    // 5. Total Modal (Full Costing)
    public function getFullExpenseAttribute()
    {
        return $this->direct_expense + $this->sector_overhead + $this->land_overhead;
    }

    // 6. Profit Bersih
    public function getNetProfitAttribute()
    {
        return $this->income - $this->full_expense;
    }
}
