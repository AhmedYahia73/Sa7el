<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Offer extends Model
{
    protected $fillable =[
        'village_id',
        'owner_id',
        'appartment_id',
        'price_day',
        'price_month',
        'price',
        'description',
        'type',
    ];

    public function village(){
        return $this->belongsTo(Village::class);
    }

    public function owner(){
        return $this->belongsTo(User::class, 'owner_id');
    }

    public function appartment(){
        return $this->belongsTo(Appartment::class, 'appartment_id');
    }

    public function offer_status(){
        return $this->hasOne(OfferStatus::class, 'appartment_id', 'appartment_id');
    }
}
