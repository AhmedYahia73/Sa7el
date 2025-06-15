<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AdminPosition extends Model
{
    protected $fillable =[
        'name',
        'type',
        'status',
    ];

    public function roles(){
        return $this->hasMany(AdminRole::class, 'position_id');
    }

    public function sup_roles(){
        return $this->hasMany(SuperRole::class, 'position_id');
    }
}
