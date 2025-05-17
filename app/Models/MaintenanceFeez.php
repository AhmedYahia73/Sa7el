<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MaintenanceFeez extends Model
{
    protected $fillable =[
        'name',
        'price',
        'year',
        'village_id',
    ];

    public function appartments(){
        return $this->hasMany(AppartmentMaintenanceFeez::class, 'maintenance_id');
    }

    public function village(){
        return $this->belongTo(Village::class, 'village_id');
    }
}
