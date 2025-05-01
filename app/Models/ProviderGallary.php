<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProviderGallary extends Model
{
    protected $fillable =[
        'image',
        'provider_id',
        'status',
    ];
    protected $appends = ['image_link'];

    public function getImageLinkAttribute(){
        return url('storage/' . $this->image);
    }
}
