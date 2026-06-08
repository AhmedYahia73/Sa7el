<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Appartment extends Model
{
    protected $fillable =[
        'unit',
        'location',
        'appartment_type_id',
        'user_id',
        'village_id',
        'entrance_status',
        'pool_status',
        'beach_status',
        'rent_code_status',
        'selling_status',
        'rent_status',
        'visits_status',
        'options_status', // => كل النقط فى الابليكشن
        'all_status',
    ];
    protected $appends = ['image_link'];

    public function getImageLinkAttribute(){
        return url('storage/' . $this->image);
    }

    public function maintenance(){
        return $this->hasMany(AppartmentMaintenanceFeez::class, 'appartment_id');
    }

    public function visitors(){
        return $this->hasMany(VisitorCode::class, 'appartment_id');
    }

    public function type(){
        return $this->belongsTo(AppartmentType::class, 'appartment_type_id');
    }

    public function user(){
        return $this->belongsTo(User::class, 'user_id');
    }

    public function village(){
        return $this->belongsTo(Village::class, 'village_id');
    }

    public function zone(){
        return $this->belongsTo(Zone::class, 'zone_id');
    }

    public function appartment_code(){
        return $this->hasMany(AppartmentCode::class, 'appartment_id');
    }
}
