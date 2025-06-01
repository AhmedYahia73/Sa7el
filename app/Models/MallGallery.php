<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MallGallery extends Model
{
    protected $fillable =[
        'image',
        'mall_id',
        'status',
    ];
    protected $appends = ['image_link'];

    public function getImageLinkAttribute(){
        return url('storage/' . $this->image);
    }
}
