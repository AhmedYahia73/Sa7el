<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Appartment extends Model
{
    protected $fillable =[
        'unit',
        'number_floors',
        'appartment_type_id',
        'village_id',
        'user_id',
        'code',
    ];
}
