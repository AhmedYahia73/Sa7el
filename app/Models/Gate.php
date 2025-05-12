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
    protected $appends = ['image_link'];

    public function getImageLinkAttribute(){
        return url('storage/' . $this->image);
    }
}
