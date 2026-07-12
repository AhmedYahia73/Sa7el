<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ZoneVillage extends Model
{
    protected $fillable = [
        'name',
        'description',
        'lat',
        'lng',
        'village_id',
    ];

    public function village(){
        return $this->belongsTo(Village::class, "village_id");
    }
    
    protected function casts(): array
    {
        return [
            'name' => 'array',
            'description' => 'array',
        ];
    }
}
