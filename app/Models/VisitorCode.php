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
}
