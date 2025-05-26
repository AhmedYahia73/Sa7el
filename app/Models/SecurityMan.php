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
        'image',
        'village_id', 
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

    public function pool(){
        return $this->belongsToMany(Pools::class, 'security_position', 'security_id', 'pool_id');
    }

    public function beach(){
        return $this->belongsToMany(Beach::class, 'security_position', 'security_id', 'beach_id');
    }

    public function gate(){
        return $this->belongsToMany(Gate::class, 'security_position', 'security_id', 'gate_id');
    }

}
