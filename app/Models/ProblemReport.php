<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProblemReport extends Model
{
    protected $fillable =[
        'google_map',
        'description',
        'image',
        'user_id',
        'village_id',
        'status', 
    ];
}
