<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VisitorLimit extends Model
{
    protected $fillable = [
        'guest',
        'worker',
        'delivery',
        'renter_guest',
        'renter_worker',
        'renter_delivery',
        'village_id',
    ];
}
