<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VillageSetting extends Model
{
    protected $fillable = [
        'renter_limit',
        'village_id',
    ];
}
