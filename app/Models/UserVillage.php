<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserVillage extends Model
{
    protected $fillable = [
        'user_id',
        'village_id',
        'type',
        'rent_from',
        'rent_to',
    ];
}
