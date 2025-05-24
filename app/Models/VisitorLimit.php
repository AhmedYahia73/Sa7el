<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VisitorLimit extends Model
{
    protected $fillable = [
        'guest',
        'worker',
        'delivery',
        'village_id',
    ];
}
