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
        'village_id',
        'package_id',
        'location', 
        'description', 
        'open_from', 
        'open_to', 
        'status', 
        'cover_image',
        'zone_id',
        'mall_id',
        'admin_id',
    ];
    protected $appends = ['image_link', 'ar_name', 'ar_description', 'rate',
    'cover_image_link'];

    public function getArNameAttribute(){
        return $this->translations
        ->where('key', 'name')
        ->where('locale', 'ar')
        ->first()?->value;
    }

    public function love_user(){
        return $this->belongsToMany(User::class, 'love_services', 'provider_id', 'user_id');
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

    public function getCoverImageLinkAttribute(){
        return url('storage/' . $this->cover_image);
    }
    
    public function zone()
    {
        return $this->belongsTo(Zone::class, 'zone_id');
    }
    
    public function package()
    {
        return $this->belongsTo(Package::class, 'package_id');
    }

    public function village()
    {
        return $this->belongsTo(Village::class, 'village_id');
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

    public function admin(){
        return $this->hasMany(User::class, 'provider_id')
        ->where('role', 'provider');
    }

    public function super_admin(){
        return $this->belongsTo(User::class, 'admin_id');
    }

    public function mall(){
        return $this->belongsTo(Mall::class, 'mall_id');
    }


    public function gallery(){
        return $this->belongsTo(ProviderGallary::class, 'provider_id');
    }
}
