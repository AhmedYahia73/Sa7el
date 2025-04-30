<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Package extends Model
{
    protected $fillable =[
        'service_id',
        'name',
        'description',
        'price',
        'type',
        'feez',
        'discount',
        'type',
        'status',
    ];
    
    public function translations()
    {
        return $this->morphMany(Translation::class, 'translatable');
    }
}
