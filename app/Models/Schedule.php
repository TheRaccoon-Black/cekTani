<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Schedule extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    public function land() { return $this->belongsTo(Land::class); }
    public function sector() { return $this->belongsTo(Sector::class); }
    public function bed() { return $this->belongsTo(Bed::class); }
    public function plantingCycle() { return $this->belongsTo(PlantingCycle::class); }

    public function getColorAttribute()
    {
        return match($this->type) {
            'fertilizing' => '#71dd37',
            'pest_control' => '#ff3e1d',
            'irrigation' => '#03c3ec',
            'harvest' => '#ffab00',
            default => '#8592a3',
        };
    }
}
