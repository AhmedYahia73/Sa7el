<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SuperRole extends Model
{
    protected $fillable = [
        'position_id',
        'module',
        'action',
    ];
}
