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
        'start_date',
        'expire_date',
        'village_id',
        'provider_id',
        'status',
    ];

    public function payment_method(){
        return $this->belongsTo(PaymentMethod::class, 'payment_method_id');
    }

    public function package(){
        return $this->belongsTo(Package::class, 'package_id');
    }

    public function service(){
        return $this->belongsTo(ServiceType::class, 'service_id');
    }

    public function village(){
        return $this->belongsTo(Village::class);
    }

    public function provider(){
        return $this->belongsTo(Provider::class);
    }
}
