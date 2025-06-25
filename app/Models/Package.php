<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Package extends Model
{
    protected $fillable =[
        'service_id',
        'name',
        'description',
        'price',
        'type',
        'feez',
        'discount',
        'admin_num',
        'security_num',
        'maintenance_module',
        'beach_pool_module',
        'maintenance_type_id',
        'status',
    ];
    protected $appends = ['ar_name', 'ar_description'];
    
    public function getArNameAttribute(){
        return $this->translations
        ->where('key', 'name')
        ->where('locale', 'ar')
        ->first()?->value;
    }

    public function getArDescriptionAttribute(){
        return $this->translations
        ->where('key', 'description')
        ->where('locale', 'ar')
        ->first()?->value;
    }

    public function service(){
        return $this->belongsTo(ServiceType::class, 'service_id');
    }

    public function maintenance_type(){
        return $this->belongsTo(MaintenanceType::class, 'maintenance_type_id');
    }

    public function translations()
    {
        return $this->morphMany(Translation::class, 'translatable');
    }
}
