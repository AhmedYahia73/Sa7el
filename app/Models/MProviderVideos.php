<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MProviderVideos extends Model
{
    protected $fillable = [
        'description',
        'video',
        'status',
        'maintenance_provider_id',
    ];
    protected $appends = ['video_link'];

    public function getVideoLinkAttribute(){
        return url('storage/' . $this->video);
    }

    public function love(){
        return $this->belongsToMany(User::class, 'm_provider_video_love', 'm_provider_video_id', 'user_id');
    } 

    public function my_love(){
        return $this->belongsToMany(User::class, 'm_provider_video_love', 'm_provider_video_id', 'user_id')
        ->where('users.id', auth()->user()->id);
    }
}
