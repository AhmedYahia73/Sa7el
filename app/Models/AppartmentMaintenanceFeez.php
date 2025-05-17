<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AppartmentMaintenanceFeez extends Model
{
    protected $fillable =[
        'appartment_id',
        'maintenance_id',
        'user_id',
        'paid',
        'total',
    ];
    protected $appends = ['status', 'remain'];

    public function getStatusAttribute(){
        if ($this->total <= $this->paid) {
            return 'paid';
        } 
        else {
            return 'unpaid';
        }
    }

    public function getRemainAttribute(){
        return $this->total - $this->paid;
    }

    public function users(){
        return $this->belongsTo(User::class, 'user_id');
    }

    public function appartment_unit(){
        return $this->belongsTo(Appartment::class, 'appartment_id');
    }
}
