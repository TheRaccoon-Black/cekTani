<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CycleLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'planting_cycle_id',
        'log_date',
        'phase',
        'activity',
        'notes',
        'photo_path',
    ];

    public function plantingCycle()
    {
        return $this->belongsTo(PlantingCycle::class);
    }
}
