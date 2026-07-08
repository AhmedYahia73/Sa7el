<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProviderWorkHours extends Model
{
    protected $fillable = [
        'provider_id',
        'day',
        'from',
        'to',
        'is_24_hours',
        'is_closed',
    ];

    protected $casts = [
        'is_24_hours' => 'boolean',
        'is_closed'   => 'boolean',
    ];

    public function provider()
    {
        return $this->belongsTo(Provider::class);
    }
}
