<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Appartment extends Model
{
    protected $fillable =[
        'unit',
        'image',
        'number_floors',
        'appartment_type_id',
        'user_id',
        'village_id',
    ];
    protected $appends = ['image_link'];

    public function getImageLinkAttribute(){
        return url('storage/' . $this->image);
    }

    public function type(){
        return $this->belongsTo(AppartmentType::class, 'appartment_type_id');
    }
}
