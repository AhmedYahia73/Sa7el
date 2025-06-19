<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ServiceProvider extends Model
{
    protected $fillable =[
        'maintenance_type_id', 
        'name', 
        'phone', 
        'image', 
        'location', 
        'description', 
        'from', 
        'to', 
        'package_id', 
        'status', 
        'village_id', 
        'open_from', 
        'open_to', 
        'cover_image', 
    ];
    protected $appends = ['image_link', 'ar_name', 'ar_description',
    'cover_image_link'];

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

    public function getCoverImageLinkAttribute(){
        return url('storage/' . $this->cover_image);
    }
    
    public function package()
    {
        return $this->belongsTo(Package::class, 'package_id');
    }

    public function village()
    {
        return $this->belongsTo(Village::class, 'village_id');
    }
    
    public function maintenance()
    {
        return $this->belongsTo(MaintenanceType::class, 'maintenance_type_id');
    }
    
    public function translations()
    {
        return $this->morphMany(Translation::class, 'translatable');
    } 

    public function admin(){
        return $this->hasMany(User::class, 'maintenance_provider_id')
        ->where('role', 'maintenance_provider');
    }

    public function love_user(){
        return $this->belongsToMany(User::class, 'm_provider_love', 'maintenance_provider_id', 'user_id');
    }

    public function service_price()
    {
        return $this->hasMany(MProviderMenue::class, 'm_provider_id');
    }
    
    public function videos()
    {
        return $this->hasMany(MProviderVideos::class, 'm_provider_id');
    }
    
    public function contact()
    {
        return $this->hasOne(MProviderContact::class, 'm_provider_id');
    }
    
    public function rate_items()
    {
        return $this->hasMany(MProviderRate::class, 'm_provider_id');
    }
 
    public function getRateAttribute()
    {
        $count = $this->rate_items->count();
        if ($count == 0) {
            return null;
        }
        return $this->rate_items->sum('rate') / $count;
    }

    public function gallery(){
        return $this->hasMany(MaintenanceProviderGallery::class, 'provider_id');
    }
}
