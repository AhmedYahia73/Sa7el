<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VillageGallary extends Model
{
    protected $fillable = [
        'image',
        'status',
        'village_id',
    ];
    protected $appends = ['image_link'];

    public function getImageLinkAttribute(){
        return url('storage/' . $this->image);
    }
}
