<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AppartmentCode extends Model
{
    protected $fillable =[
        'appartment_id',
        'user_id',
        'village_id',
        'from',
        'to',
        'type',
        'code',
        'people',
        'image',
        'owner_id',
        'user_type',
    ];
    protected $appends = ['image_id_link'];

    public function getImageIdLinkAttribute(){
        return url('storage/' . $this->image);
    }

    public function appartment(){
        return $this->belongsTo(Appartment::class, 'appartment_id');
    }

    public function owner(){
        return $this->belongsTo(User::class, 'owner_id');
    }

    public function village(){
        return $this->belongsTo(Village::class, 'village_id');
    }

    public function user(){
        return $this->belongsTo(User::class, 'user_id');
    }
}
