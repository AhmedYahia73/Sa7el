<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OfferImage extends Model
{
    protected $fillable =[
        'village_id',
        'owner_id',
        'appartment_id',
        'image',
    ];
    protected $appends = ['image_link'];

    public function getImageLinkAttribute(){
        return url('storage/' . $this->image);
    }
}
