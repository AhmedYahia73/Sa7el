<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserPool extends Model
{
    protected $fillable = [
        'user_id',
        'pool_id',
        'village_id',
        'user_type',
        'umbrella',
        'appartment_id',
    ];

    public function user(){
        return $this->belongsTo(User::class, 'user_id');
    }

    public function pool(){
        return $this->belongsTo(Pools::class, 'pool_id');
    }

    public function appartment(){
        return $this->belongsTo(Appartment::class, 'appartment_id');
    }
}
