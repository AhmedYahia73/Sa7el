<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Beach extends Model
{
    protected $fillable =[
        'name',
        'from',
        'to',
        'status', 
    ];
}
