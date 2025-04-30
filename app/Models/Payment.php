<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    protected $fillable =[
        'payment_method_id',
        'package_id',
        'service_id',
        'amount',
        'type',
        'rejected_reason',
        'discount',
        'status',
    ];
}
