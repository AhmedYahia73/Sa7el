<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MaintenanceProviderGallery extends Model
{
    protected $fillable =[
        'image',
        'provider_id',
        'status',
    ];
    protected $appends = ['image_link'];

    public function getImageLinkAttribute(){
        return url('storage/' . $this->image);
    }

    public function love(){
        return $this->belongsToMany(User::class, 'm_provider_gallary_love', 'm_provider_gallery_id', 'user_id');
    }

    public function my_love(){
        return $this->belongsToMany(User::class, 'm_provider_gallary_love', 'm_provider_gallery_id', 'user_id')
        ->where('users.id', auth()->user()->id);
    }
}
