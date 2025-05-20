<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PaymentMaintenanceRequest extends Model
{
    protected $fillable =[
        'maintenance_feez_id',
        'user_id',
        'village_id',
        'paid',
        'receipt',
        'status',
    ];

    public function maintenance(){
        return $this->belongsTo(MaintenanceFeez::class, 'maintenance_feez_id');
    }

    public function user(){
        return $this->belongsTo(User::class, 'user_id');
    }
}
