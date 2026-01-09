<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Bed extends Model
{
    use HasFactory;
    protected $fillable = [
        "sector_id",
        "name",
        "width",
        "length",
        "max_capacity",
        "created_at",
        "updated_at",
    ];

    public function sector()
    {
        return $this->belongsTo(Sector::class);
    }
}
