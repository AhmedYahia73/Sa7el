<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    protected $fillable =[
        'village_id',
        'image',
        'description',
    ];
    protected $appends = ['image_link'];

    public function getImageLinkAttribute(){
        return url('storage/' . $this->image);
    }

    public function love(){
        return $this->belongsToMany(User::class, 'post_love');
    }

    public function my_love(){
        return $this->belongsToMany(User::class, 'post_love')
        ->where('users.id', auth()->user()->id);
    }
}
