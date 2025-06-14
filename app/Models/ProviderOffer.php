<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProviderOffer extends Model
{
    protected $fillable =[
        'description',
        'image',
        'status',
        'provider_id',
    ];
}
