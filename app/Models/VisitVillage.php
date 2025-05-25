<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VisitVillage extends Model
{
    protected $fillable = [
        'user_id', 
        'village_id',
        'appartment_id',
        'gate_id', 
        'image',
        'type',
        'visitor_type',
        'code',
    ];

    public function user(){
        return $this->belongsTo(User::class, 'user_id');
    }

    public function gate(){
        return $this->belongsTo(Gate::class, 'gate_id');
    }

    public function appartment(){
        return $this->belongsTo(Appartment::class, 'appartment_id');
    }
}
