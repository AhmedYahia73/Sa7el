<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProviderRate extends Model
{
    protected $fillable =[
        'rate', 
        'provider_id', 
    ];
}
