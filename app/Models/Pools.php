<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pools extends Model
{
    protected $fillable =[
        'name',
        'from',
        'to',
        'status', 
    ];
}
