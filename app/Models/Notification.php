<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    protected $fillable =[
        'village_id',
        'code_request_id',
        'login_request_id',
        "type", // user, admin
        'notification',
        'is_read',
        "user_id",
    ];
}
