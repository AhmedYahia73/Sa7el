<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VisitBeach extends Model
{
    protected $fillable = [
        'user_id', 
        'village_id',
        'appartment_id',
        'beach_id', 
        'image',
        'type',
        'visitor_type',
        'code',
        'user_type',
        'inside_gate_id',
    ];

    public function user(){
        return $this->belongsTo(User::class, 'user_id');
    }

    public function beach(){
        return $this->belongsTo(Beach::class, 'beach_id');
    }

    public function inside_gate(){
        return $this->belongsTo(InsideGate::class, 'inside_gate_id');
    }

    public function appartment(){
        return $this->belongsTo(Appartment::class, 'appartment_id');
    }
}
