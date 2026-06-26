<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HelpGroup extends Model
{
    protected $fillable =[
        'name', 
        'status', 
    ];

    protected function casts(): array
    {
        return [
            'name' => 'array',
        ];
    }
}
