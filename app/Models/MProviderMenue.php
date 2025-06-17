<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MProviderMenue extends Model
{
    protected $fillable = [
        'image',
        'status',
        'maintenance_provider_id',
    ];
    protected $appends = ['image_link'];

    public function getImageLinkAttribute(){
        return url('storage/' . $this->image);
    }
}
