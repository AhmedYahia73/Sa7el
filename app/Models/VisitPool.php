<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VisitPool extends Model
{
    protected $fillable = [
        'user_id', 
        'village_id',
        'appartment_id',
        'pool_id', 
        'image',
        'type',
        'visitor_type',
        'code',
        'user_type',
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
