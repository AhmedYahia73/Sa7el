<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Application extends Model
{
    protected $fillable =[
        'google_api',
        'app_description', 
    ]; 
}
