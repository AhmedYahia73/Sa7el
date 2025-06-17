<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MProviderContact extends Model
{
    protected $fillable =[
        'watts_status',
        'phone_status',
        'website_status',
        'instagram_status',
        'watts',
        'phone',
        'website',
        'instagram',
        'maintenance_provider_id',
    ];
}
