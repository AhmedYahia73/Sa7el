<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EntranceBeach extends Model
{
    protected $fillable =[
        'beach_id',
        'user_id',
        'time',
    ];

    public function beach(){
        return $this->belongsTo(Beach::class);
    }
}
