<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HelpVideo extends Model
{
    protected $fillable =[
        'name',
        'description',
        'ar_video',
        'en_video',
        "help_group_id", 
        'status', 
    ];
    protected $appends = ['ar_video_link', 'en_video_link'];

    public function getArVideoLinkAttribute(){
        return url('storage/' . $this->ar_video);
    }
    
    public function getEnVideoLinkAttribute(){
        return url('storage/' . $this->en_video);
    }

    public function group(){
        return $this->belongsTo(HelpGroup::class, 'help_group_id');
    }

    protected function casts(): array
    {
        return [
            'name' => 'array',
            'description' => 'array',
        ];
    }
}
