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
        'status',
    ];
}
