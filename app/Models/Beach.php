<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Beach extends Model
{
    protected $fillable =[
        'name',
        'qr_code',
        'from',
        'to',
        'village_id',
        'status', 
    ];
    protected $appends = ['ar_name'];

    public function getArNameAttribute(){
        return $this->translations
        ->where('key', 'name')
        ->where('locale', 'ar')
        ->first()?->value;
    }
    
    public function translations()
    {
        return $this->morphMany(Translation::class, 'translatable');
    }
}
