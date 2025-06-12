<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProviderContact extends Model
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
        'provider_id',
    ];
}
