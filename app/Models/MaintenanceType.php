<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MaintenanceType extends Model
{
    protected $fillable =[
        'name',
        'image',
        'status', 
    ];
    protected $appends = ['image_link', 'ar_name'];

    public function village(){
        return $this->belongsToMany(Village::class, 'maintenance_type_villages', 
        'maintenance_types_id', 'village_id')->withPivot('status');
    }

    public function maintenance_provider(){
        return $this->hasMany(ServiceProvider::class, 'maintenance_type_id');
    }

    public function getImageLinkAttribute(){
        return url('storage/' . $this->image);
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
}
