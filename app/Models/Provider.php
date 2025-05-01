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
    protected $appends = ['image_link', 'ar_name', 'ar_description', 'rate'];

    public function getArNameAttribute(){
        return $this->translations
        ->where('key', 'name')
        ->where('locale', 'ar')
        ->first()?->value;
    }

    public function getArDescriptionAttribute(){
        return $this->translations
        ->where('key', 'description')
        ->where('locale', 'ar')
        ->first()?->value;
    }

    public function getImageLinkAttribute(){
        return url('storage/' . $this->image);
    }
    
    public function package()
    {
        return $this->belongsTo(Package::class, 'package_id');
    }
    
    public function rate_items()
    {
        return $this->hasMany(ProviderRate::class, 'provider_id');
    }
 
    public function getRateAttribute()
    {
        $count = $this->rate_items->count();
        if ($count == 0) {
            return null;
        }
        return $this->rate_items->sum('rate') / $count;
    }
    
    public function service()
    {
        return $this->belongsTo(ServiceType::class, 'service_id');
    }
    
    public function translations()
    {
        return $this->morphMany(Translation::class, 'translatable');
    }
}
