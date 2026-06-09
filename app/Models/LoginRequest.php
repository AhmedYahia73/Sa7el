<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LoginRequest extends Model
{
    protected $fillable =[
        'user_id',
        "ip_address",
        // 'village_id',
        'status', // "pending", "approve", "reject"
    ];

    public function user(){
        return $this->belongsTo(User::class, 'user_id');
    }
}
