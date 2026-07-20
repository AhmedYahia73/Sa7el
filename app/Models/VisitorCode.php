<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VisitorCode extends Model
{
    protected $fillable = [
        'user_id',
        'qr_code',
        'code',
        'village_id',
        'appartment_id',
        'visitor_type',
    ];
    protected $appends = ['qr_code_link'];

    public function getQrCodeLinkAttribute(){
        return url('storage/' . $this->image);
    }

    public function is_visit(){
        return $this->hasOne(VisitVillage::class, 'code', 'code');
    }

    public function unit(){
        return $this->belongsTo(Appartment::class, 'appartment_id');
    }

    public function user(){
        return $this->belongsTo(User::class, 'user_id');
    }
}
