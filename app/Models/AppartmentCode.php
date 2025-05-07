<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AppartmentCode extends Model
{
    protected $fillable =[
        'appartment_id',
        'user_id',
        'village_id',
        'from',
        'to',
        'type',
        'code',
    ];

    public function appartment(){
        return $this->belongsTo(Appartment::class, 'appartment_id');
    }
}
