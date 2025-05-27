<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ServiceType extends Model
{
    protected $fillable =[
        'name',
        'image',
        'village_id',
        'status',
    ];
    protected $appends = ['image_link', 'ar_name'];

    public function getArNameAttribute(){
        return $this->translations
        ->where('key', 'name')
        ->where('locale', 'ar')
        ->first()?->value;
    }

    public function getImageLinkAttribute(){
        return url('storage/' . $this->image);
    }
    
    public function translations()
    {
        return $this->morphMany(Translation::class, 'translatable');
    }

    public function providers(){
        return $this->hasMany(Provider::class, 'service_id');
    } 
}
