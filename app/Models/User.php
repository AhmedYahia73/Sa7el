<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasApiTokens, HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'birthDate',
        'gender',
        'email',
        'email_verified_at',
        'phone',
        'password',
        'user_type',
        'village_id',
        'provider_id',
        'admin_position_id',
        'image',
        'role',
        'parent_user_id',
        'status',
        'rent_from',
        'rent_to',
        'provider_only',
        'qr_code',
    ];
    protected $appends = ['image_link', 'qr_code_link'];

    public function getQrCodeLinkAttribute(){
        return url('storage/' . $this->qr_code);
    }

    public function village(){
        return $this->belongsTo(Village::class, 'village_id');
    }

    public function love(){
        return $this->belongsToMany(Post::class, 'post_love');
    }

    public function love_provider(){
        return $this->belongsToMany(Provider::class, 'love_services', 'user_id', 'provider_id');
    }

    public function villages_user(){
        return $this->belongsToMany(Village::class, 'user_village');
    }

    public function appartments(){
        return $this->hasMany(Appartment::class, 'user_id');
    }

    public function provider(){
        return $this->belongsTo(Provider::class, 'provider_id');
    }

    public function position(){
        return $this->belongsTo(AdminPosition::class, 'admin_position_id');
    }

    public function parent(){
        return $this->belongsTo(User::class, 'parent_user_id');
    }

    public function appartment_code(){
        return $this->hasMany(AppartmentCode::class, 'user_id');
    }

    public function getImageLinkAttribute(){
        return url('storage/' . $this->image);
    }

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }
}
