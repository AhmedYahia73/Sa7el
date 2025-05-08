<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EntrancePool extends Model
{
    protected $fillable =[
        'pool_id',
        'user_id',
        'time',
    ];
}
