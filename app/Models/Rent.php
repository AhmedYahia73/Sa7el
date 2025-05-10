<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Rent extends Model
{
    protected $fillable =[
        'owner_id',
        'unit_id',
        'from',
        'to',
        'reterner_id',
        'unit_type_id',
        'code',
        'people',
        'status',
    ];

    public function unit(){
        return $this->belongsTo(Appartment::class, 'unit_id');
    }

    public function unit_type(){
        return $this->belongsTo(AppartmentType::class, 'unit_type_id');
    }

    public function renter(){
        return $this->belongsTo(User::class, 'reterner_id');
    }
}
