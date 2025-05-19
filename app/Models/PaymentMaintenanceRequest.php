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
}
