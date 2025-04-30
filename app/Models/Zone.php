<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Zone extends Model
{
    protected $fillable = [
        'name',
        'image',
        'description',
        'status',
    ];
    protected $appends = ['image_link', 'ar_name', 'ar_description'];

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

    public function translations()
    {
        return $this->morphMany(Translation::class, 'translatable');
    }
}
