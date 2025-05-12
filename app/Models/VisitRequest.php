<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VisitRequest extends Model
{
    protected $fillable = [
        'owner_id',
        'village_id',
        'appartment_id',
        'visitor_type',
        'date',
        'time',
    ];

    public function owner(){
        return $this->belongsTo(User::class, 'owner_id');
    }

    public function village(){
        return $this->belongsTo(Village::class, 'village_id');
    }

    public function appartment(){
        return $this->belongsTo(Appartment::class, 'appartment_id');
    }
}
