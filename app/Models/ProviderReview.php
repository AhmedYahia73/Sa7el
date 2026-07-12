<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProviderReview extends Model
{
    protected $fillable =[
        'user_id',
        'provider_id',
        'rate',
        'comment',
    ];
    
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
    
    public function provider()
    {
        return $this->belongsTo(Provider::class, 'provider_id');
    }
}
