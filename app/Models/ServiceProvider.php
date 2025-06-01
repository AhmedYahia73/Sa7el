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
        'mall_id',
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
}
