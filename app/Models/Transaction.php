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
}
