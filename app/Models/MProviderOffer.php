<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MProviderOffer extends Model
{
    protected $fillable =[
        'description',
        'image',
        'status',
        'maintenance_provider_id',
    ];
}
