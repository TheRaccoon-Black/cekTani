<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Land extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    // Otomatis ubah JSON di database jadi Array PHP saat diambil
    protected $casts = [
        'geojson_data' => 'array',
        'area_size' => 'float',
    ];

    public function sectors() {
        return $this->hasMany(Sector::class);
    }
}
