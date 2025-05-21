<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserBeach extends Model
{
    protected $fillable = [
        'user_id',
        'beach_id',
        'village_id',
    ];
}
