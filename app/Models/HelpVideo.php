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
        'status', 
    ];

    protected function casts(): array
    {
        return [
            'name' => 'array',
            'description' => 'array',
        ];
    }
}
