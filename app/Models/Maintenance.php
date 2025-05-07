<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Maintenance extends Model
{
    protected $fillable =[
        'user_id',
        'appartment_id',
        'maintenance_type_id',
        'description',
        'image',
        'status', 
    ];
}
