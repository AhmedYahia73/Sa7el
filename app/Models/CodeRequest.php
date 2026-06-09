<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CodeRequest extends Model
{
    protected $fillable =[
        'user_id',
        'appartment_id',
        'code',
        'appartment_codes',
        'status', // "pending", "approve", "reject"
    ];

    protected function casts(): array
    {
        return [
            'appartment_codes' => 'array',
        ];
    }

    public function user(){
        return $this->belongsTo(User::class, 'user_id');
    }

    public function appartment(){
        return $this->belongsTo(Appartment::class, 'appartment_id');
    }
}
