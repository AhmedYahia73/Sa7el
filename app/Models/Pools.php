<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pools extends Model
{
    protected $fillable =[
        'name',
        'qr_code',
        'village_id',
        'from',
        'to',
        'status', 
    ];
    protected $appends = ['ar_name'];

    public function security(){
        return $this->belongsToMany(SecurityMan::class, 'security_position', 'pool_id', 'security_id');
    }

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

    public function gallery(){
        return $this->hasMany(PoolGallary::class, 'pool_id');
    }
}
