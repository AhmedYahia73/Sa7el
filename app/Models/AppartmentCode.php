<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AppartmentCode extends Model
{
    protected $fillable =[
        'appartment_id',
        'user_id',
        'village_id',
        'from',
        'to',
        'type',
        'code',
        'people',
        'image',
        'owner_id',
        'user_type',
    ];
    protected $appends = ['image_id_link'];

    protected function casts(): array
    {
        return [
            'image' => 'array',
        ];
    }
    
    public function rent_images(){
        return $this->belongsToMany(RentImage::class, "appartment_code_rent_image", "appartment_code_id", "rent_image_id");
    }

    public function getImageIdLinkAttribute()
    {
        $images = $this->image;
        
        // لو مش مصفوفة أو فاضية رجع مصفوفة فاضية فوراً
        if (!is_array($images) || empty($images)) {
            return [];
        }

        $images_link = [];
        foreach ($images as $item) {
            $images_link[] = url('storage/' . $item);
        }
        
        return $images_link;
    }

    public function appartment(){
        return $this->belongsTo(Appartment::class, 'appartment_id');
    }

    public function owner(){
        return $this->belongsTo(User::class, 'owner_id');
    }

    public function village(){
        return $this->belongsTo(Village::class, 'village_id');
    }

    public function user(){
        return $this->belongsTo(User::class, 'user_id');
    }

    public function images(){
        return $this->hasMany(AppartmentCodeImage::class, 'appartment_code_id');
    }
}
