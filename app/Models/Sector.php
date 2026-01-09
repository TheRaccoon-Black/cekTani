<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sector extends Model
{
    use HasFactory;

    protected $fillable = [
        "land_id",
        "name",
        "area_size",
        "geojson_data",
        "created_at",
        "updated_at",
    ];

    public function beds()
    {
        return $this->hasMany(Bed::class);
    }

    // Relasi: Satu Sektor dimilik oleh SATU Lahan
    // Ini diperlukan karena di controller Anda memanggil ->with(['land'])
    public function land()
    {
        return $this->belongsTo(Land::class);
    }
}
