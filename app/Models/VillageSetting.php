<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VillageSetting extends Model
{
    protected $fillable = [
        'renter_limit',
        'village_id',
        'appartment_type_id',
    ];


    public function type(){
        return $this->belongsTo(AppartmentType::class, 'appartment_type_id');
    }
}
