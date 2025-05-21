<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserPool extends Model
{
    protected $fillable = [
        'user_id',
        'pool_id',
        'village_id',
    ];
}
