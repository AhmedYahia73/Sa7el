<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VisitVillage extends Model
{
    protected $fillable = [
        'user_id', 
        'village_id', 
        'gate_id', 
        'image', 
    ];
}
