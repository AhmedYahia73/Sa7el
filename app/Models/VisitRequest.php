<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VisitRequest extends Model
{
    protected $fillable = [
        'owner_id',
        'visitor_type',
        'village_id',
        'village_id',
    ];
}
