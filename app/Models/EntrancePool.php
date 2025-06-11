<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EntrancePool extends Model
{
    protected $fillable =[
        'pool_id',
        'user_id',
        'time',
        'village_id',
    ];

    public function pool(){
        return $this->belongsTo(Pools::class);
    }

    public function user(){
        return $this->belongsTo(User::class, 'user_id');
    }
}
