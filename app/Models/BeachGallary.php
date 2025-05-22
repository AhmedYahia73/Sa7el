<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BeachGallary extends Model
{
    protected $fillable =[
        'beach_id',
        'image',
    ];
    protected $appends = ['image_link'];

    public function getImageLinkAttribute(){
        return url($this->image);
    }
}
