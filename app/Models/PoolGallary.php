<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PoolGallary extends Model
{
    protected $fillable =[
        'pool_id',
        'image',
    ];
    protected $appends = ['image_link'];

    public function getImageLinkAttribute(){
        return url('storage/' . $this->image);
    }
}
