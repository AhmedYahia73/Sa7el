<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Maintenance extends Model
{
    protected $fillable =[
        'user_id',
        'appartment_id',
        'maintenance_type_id',
        'description',
        'image',
        'status', 
    ];

    public function maintenance_type(){
        return $this->belongsTo(MaintenanceType::class ,'maintenance_type_id');
    }
}
