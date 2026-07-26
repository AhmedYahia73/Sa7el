<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Gate extends Model
{
    protected $fillable =[
        'name',
        'location',
        'status',
        'village_id',
        'image',
    ];
    protected $appends = ['image_link', 'ar_name'];

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

    public function security(){
        return $this->belongsToMany(SecurityMan::class, 'security_position', 'gate_id', 'security_id');
    }

    public function getImageLinkAttribute(){
        return url('storage/' . $this->image);
    }
}
