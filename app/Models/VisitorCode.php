<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VisitorCode extends Model
{
    protected $fillable = [
        'user_id',
        'qr_code',
        'code',
        'village_id',
    ];
}
