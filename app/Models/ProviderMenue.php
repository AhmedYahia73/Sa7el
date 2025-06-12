<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProviderMenue extends Model
{
    protected $fillable = [
        'image',
        'status',
        'provider_id',
    ];
    protected $appends = ['image_link'];

    public function getImageLinkAttribute(){
        return url('storage/' . $this->image);
    }
}
