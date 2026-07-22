<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserBeach extends Model
{
    protected $fillable = [
        'user_id',
        'beach_id',
        'village_id',
        'user_type',
        'umbrella',
        'appartment_id',
    ];

    public function user(){
        return $this->belongsTo(User::class, 'user_id');
    }

    public function beach(){
        return $this->belongsTo(Beach::class, 'beach_id');
    }

    public function appartment(){
        return $this->belongsTo(Appartment::class, 'appartment_id');
    }
}
