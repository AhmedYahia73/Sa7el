<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EntranceGate extends Model
{
    protected $fillable =[
        'gate_id',
        'user_id',
        'time',
        'village_id',
    ];

    public function gate(){
        return $this->belongsTo(Gate::class);
    }

    public function user(){
        return $this->belongsTo(User::class, 'user_id');
    }
}
