<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Zone. extends Model
{
    protected $fillable = [
        'village_id',
        'name',
        'ar_name',
        'description',
        'ar_description',
        'lat',
        'lng',
    ];
}
