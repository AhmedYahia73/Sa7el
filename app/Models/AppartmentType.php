<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AppartmentType extends Model
{
    protected $fillable =[
        'name',
        'image',
        'status',
    ];
    protected $appends = ['ar_name', 'image_link'];
 
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
