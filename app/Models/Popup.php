<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Village;

class Popup extends Model
{
    protected $fillable =[
        'village_id',
        'title',
        'description',
        'image',
        'ar_title',
        'ar_description',
        'ar_image',
        'all',
        "status"
    ];
    protected $appends = ['image_link', 'ar_image_link'];

    public function getImageLinkAttribute(){
        if(isset($this->image)){
            return url('storage/' . $this->image);
        }
    }

    public function getArImageLinkAttribute(){
        if(isset($this->ar_image)){
            return url('storage/' . $this->ar_image);
        }
    }

    public function village()
    {
        return $this->belongsTo(Village::class);
    }
}
