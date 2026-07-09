<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RentImage extends Model
{
    protected $fillable =[
        'image',
        'description', 
    ];
    protected $appends = ['image_link'];
    
    public function code(){
        return $this->belongsToMany(AppartmentCode::class, "appartment_code_rent_image", "rent_image_id", "appartment_code_id");
    }

    public function getImageLinkAttribute(){
        if(isset($this->image)){
            return url('storage/' . $this->image);
        }
    }
}
