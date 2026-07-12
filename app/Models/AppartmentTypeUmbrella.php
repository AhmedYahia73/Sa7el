<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AppartmentTypeUmbrella extends Model
{
    protected $fillable =[
        'appartment_type_id',
        'village_id',
        'umbrellas',
    ];

    public function type(){
        return $this->belongsTo(AppartmentType::class, 'appartment_type_id');
    }
}
