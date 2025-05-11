<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProblemReport extends Model
{
    protected $fillable =[
        'google_map',
        'description',
        'image',
        'user_id',
        'village_id',
        'status', 
    ];
    protected $appends = ['image_link'];

    public function getImageLinkAttribute(){
        return url('storage/' . $this->image);
    }

    public function user(){
        return $this->belongsTo(User::class, 'user_id');
    }
}
