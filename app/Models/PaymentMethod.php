<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PaymentMethod extends Model
{
    protected $fillable =[
        'logo',
        'name',
        'description',
        'status', 
    ];
    
    protected $appends = ['logo_link', 'ar_name', 'ar_description'];

    public function getArNameAttribute(){
        return $this->translations
        ->where('key', 'name')
        ->where('locale', 'ar')
        ->first()?->value;
    }

    public function getArDescriptionAttribute(){
        return $this->translations
        ->where('key', 'description')
        ->where('locale', 'ar')
        ->first()?->value;
    }

    public function getLogoLinkAttribute(){
        return url('storage/' . $this->logo);
    }
    
    public function translations()
    {
        return $this->morphMany(Translation::class, 'translatable');
    }
}
