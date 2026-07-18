<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DeviceLog extends Model
{ 
    protected $fillable =[
        'device', 
        'user_id', 
        'status', 
    ];
}
