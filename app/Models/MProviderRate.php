<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MProviderRate extends Model
{
    protected $fillable =[
        'rate', 
        'm_provider_id', 
    ];
}
