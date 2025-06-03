<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PostImage extends Model
{
    protected $fillable =[ 
        'post_id',
        'image', 
    ];
    protected $appends = ['image_link'];
}
