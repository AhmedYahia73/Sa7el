<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Provider extends Model
{
    protected $fillable =[
        'service_id',
        'name',
        'phone',
        'image',
        'from',
        'to',
        'package_id',
        'location', 
        'description', 
        'status', 
    ];
    
    public function translations()
    {
        return $this->morphMany(Translation::class, 'translatable');
    }
}
