<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Mall extends Model
{
    protected $fillable =[
        'name',
        'description',
        'open_from', 
        'open_to', 
        'image',
        'cover_image',
        'zone_id',
        'status',
    ];
    protected $appends = ['image_link', 'ar_name', 'ar_description', 'cover_image_link'];

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
    
    public function translations()
    {
        return $this->morphMany(Translation::class, 'translatable');
    }

    public function getCoverImageLinkAttribute(){
        return url('storage/' . $this->cover_image);
    }

    public function getImageLinkAttribute(){
        return url('storage/' . $this->image);
    }

    public function providers(){
        return $this->hasMany(Provider::class, 'mall_id');
    }

    public function zone(){
        return $this->belongsTo(Zone::class, 'zone_id');
    }
}
