<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PaymentMethod extends Model
{
    protected $fillable =[
        'logo',
        'name',
        'description',
        'status', 
    ];
    
    public function translations()
    {
        return $this->morphMany(Translation::class, 'translatable');
    }
}
