<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProviderVideos extends Model
{
    protected $fillable = [
        'description',
        'video',
        'status',
        'provider_id',
    ];
    protected $appends = ['video_link'];

    public function getVideoLinkAttribute(){
        return url('storage/' . $this->video);
    }
}
