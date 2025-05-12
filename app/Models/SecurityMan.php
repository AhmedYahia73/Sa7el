<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SecurityMan extends Model
{
    protected $fillable =[
        'name',
        'location',
        'image',
        'shift_from',
        'shift_to',
        'password',
        'email',
        'phone',
        'type',
        'status',
    ];
}
