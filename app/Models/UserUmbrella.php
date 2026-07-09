<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserUmbrella extends Model
{
    protected $fillable = [
        'user_id',
        'umbrellas',
    ];
}
