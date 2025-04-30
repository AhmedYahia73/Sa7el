<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ServiceType extends Model
{
    protected $fillable =[
        'name',
        'image',
        'status',
    ];
    
    public function translations()
    {
        return $this->morphMany(Translation::class, 'translatable');
    }
}
