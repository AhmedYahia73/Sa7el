<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    protected $fillable =[
        'village_id',
        'admin_id',
        'description',
    ];
    protected $appends = ['image_link'];

    public function getImageLinkAttribute(){
        return url('storage/' . $this->image);
    }

    public function images(){
        return $this->hasMany(PostImage::class, 'post_id');
    }

    public function admin(){
        return $this->belongsTo(User::class, 'admin_id');
    }

    public function village(){
        return $this->belongsTo(Village::class, 'village_id');
    }

    public function love(){
        return $this->belongsToMany(User::class, 'post_love');
    }

    public function my_love(){
        return $this->belongsToMany(User::class, 'post_love')
        ->where('users.id', auth()->user()->id);
    }
}
