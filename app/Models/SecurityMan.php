<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class SecurityMan extends Model
{
    use HasApiTokens, HasFactory;
    protected $fillable =[
        'name',
        'location',
        'image',
        'village_id',
        'shift_from',
        'shift_to',
        'password',
        'email',
        'phone',
        'type',
        'status',
    ];
    protected $appends = ['image_link'];

    public function getImageLinkAttribute(){
        return url('storage/' . $this->image);
    }

    protected $hidden = [
        'password',
        'remember_token',
    ];
    
    protected function casts(): array
    {
        return [
            'password' => 'hashed',
        ];
    }
}
