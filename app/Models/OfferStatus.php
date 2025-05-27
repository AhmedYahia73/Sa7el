<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OfferStatus extends Model
{
    protected $fillable =[
        'appartment_id',
        'rent_status',
        'sale_status',
    ];
}
