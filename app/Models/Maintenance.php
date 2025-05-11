<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Maintenance extends Model
{
    protected $fillable =[
        'user_id',
        'appartment_id',
        'maintenance_type_id',
        'village_id',
        'description',
        'image',
        'status', 
    ];
    protected $appends = ['image_link', 'status_request'];

    public function getImageLinkAttribute(){
        return url('storage/' . $this->image);
    }

    public function getStatusRequestAttribute(){
        return $this->status ? 'completed': 'pending';
    }

    public function maintenance_type(){
        return $this->belongsTo(MaintenanceType::class ,'maintenance_type_id');
    }

    public function appartment(){
        return $this->belongsTo(Appartment::class ,'appartment_id');
    }

    public function user(){
        return $this->belongsTo(User::class);
    }
}
