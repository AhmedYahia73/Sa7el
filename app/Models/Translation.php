<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Translation extends Model
{
    protected $fillable =[
        'locale', 
        'translatable', 
        'key', 
        'value', 
    ];
}
