<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Appartment extends Model
{
    protected $fillable =[
        'unit',
        'image',
        'number_floors',
        'appartment_type_id',
        'user_id',
        'village_id',
    ];

    public function type(){
        return $this->belongsTo(AppartmentType::class, 'appartment_type_id');
    }
}
