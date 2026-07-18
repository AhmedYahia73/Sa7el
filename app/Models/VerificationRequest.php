<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VerificationRequest extends Model
{
    protected $fillable = [
        'user_id',
        'image',
        'status',
    ];
    protected $appends = ['image_link'];


    public function getImageLinkAttribute(){
        if($this->image){
            return url('storage/' . $this->image);
        }
        return null;
    }

    public function user(){
        return $this->belongsTo(User::class, 'user_id');
    }
}
