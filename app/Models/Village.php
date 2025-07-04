<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Village extends Model
{
    protected $fillable = [
        'name',
        'description',
        'location',
        'image',
        'from',
        'to',
        'package_id',
        'zone_id',
        'status',
        'cover_image',
        'location_map',
    ];
    protected $appends = ['image_link', 'ar_name', 'ar_description', 'cover_image_link'];

    public function getCoverImageLinkAttribute(){
        return url('storage/' . $this->cover_image);
    }

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

    public function units(){
        return $this->hasMany(Appartment::class, 'village_id');
    }

    public function package(){
        return $this->belongsTo(Package::class, 'package_id');
    }

    public function getImageLinkAttribute(){
        return url('storage/' . $this->image);
    }

    public function zone(){
        return $this->belongsTo(Zone::class, 'zone_id');
    }

    public function population(){
        return $this->hasMany(User::class, 'village_id')
        ->where('role', 'user');
    }

    public function providers(){
        return $this->hasMany(Provider::class, 'village_id');
    }

    public function maintenance_providers(){
        return $this->hasMany(Provider::class, 'village_id');
    } 

    public function admin(){
        return $this->hasMany(User::class, 'village_id')
        ->where('role', 'village');
    }

    public function translations()
    {
        return $this->morphMany(Translation::class, 'translatable');
    }

    public function gallery()
    {
        return $this->hasMany(VillageGallary::class, 'village_id');
    }
}
