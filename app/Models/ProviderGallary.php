<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProviderGallary extends Model
{
    protected $fillable =[
        'image',
        'provider_id',
        'status',
    ];
}
