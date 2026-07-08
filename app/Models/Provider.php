<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Provider extends Model
{
    protected $fillable =[
        'service_id',
        'name',
        'phone',
        'image',
        'from',
        'to',
        'village_id',
        'package_id',
        'location', 
        'description', 
        'status', 
        'cover_image',
        'zone_id',
        'mall_id',
        'admin_id',
        'location_map',
    ];
    protected $appends = ['image_link', 'ar_name', 'ar_description', 'rate',
    'cover_image_link'];

    public function getArNameAttribute(){
        return $this->translations
        ->where('key', 'name')
        ->where('locale', 'ar')
        ->first()?->value;
    }

    public function love_user(){
        return $this->belongsToMany(User::class, 'love_services', 'provider_id', 'user_id');
    }

    public function getArDescriptionAttribute(){
        return $this->translations
        ->where('key', 'description')
        ->where('locale', 'ar')
        ->first()?->value;
    }

    public function getImageLinkAttribute(){
        return url('storage/' . $this->image);
    }

    public function getCoverImageLinkAttribute(){
        return url('storage/' . $this->cover_image);
    }
    
    public function zone()
    {
        return $this->belongsTo(Zone::class, 'zone_id');
    }
    
    public function menue()
    {
        return $this->hasMany(ProviderMenue::class, 'provider_id');
    }
    
    public function videos()
    {
        return $this->hasMany(ProviderVideos::class, 'provider_id');
    }
    
    public function contact()
    {
        return $this->hasOne(ProviderContact::class, 'provider_id');
    }
    
    public function package()
    {
        return $this->belongsTo(Package::class, 'package_id');
    }

    public function village()
    {
        return $this->belongsTo(Village::class, 'village_id');
    }
    
    public function rate_items()
    {
        return $this->hasMany(ProviderRate::class, 'provider_id');
    }
 
    public function getRateAttribute()
    {
        $count = $this->rate_items->count();
        if ($count == 0) {
            return null;
        }
        return $this->rate_items->sum('rate') / $count;
    }
    
    public function service()
    {
        return $this->belongsTo(ServiceType::class, 'service_id');
    }
    
    public function translations()
    {
        return $this->morphMany(Translation::class, 'translatable');
    }

    public function admin(){
        return $this->hasMany(User::class, 'provider_id')
        ->where('role', 'provider');
    }

    public function super_admin(){
        return $this->belongsTo(User::class, 'admin_id');
    }

    public function mall(){
        return $this->belongsTo(Mall::class, 'mall_id');
    }


    public function gallery(){
        return $this->hasMany(ProviderGallary::class, 'provider_id');
    }

    public function work_hours()
    {
        return $this->hasMany(ProviderWorkHours::class, 'provider_id');
    }

    /**
     * Check if provider is open right now.
     * Handles overnight hours (e.g. open 21:00 wednesday to 02:00 thursday)
     */
    public function isOpenNow(): bool
    {
        $now = \Carbon\Carbon::now();
        $currentTime = $now->format('H:i:s');

        // Check today's schedule first
        $todayName     = strtolower($now->englishDayOfWeek);
        $todayHours    = $this->work_hours->firstWhere('day', $todayName);

        if ($todayHours && !$todayHours->is_closed) {
            if ($todayHours->is_24_hours) return true;

            if ($todayHours->from && $todayHours->to) {
                $from = $todayHours->from;
                $to   = $todayHours->to;

                if ($from <= $to) {
                    // Normal hours: 09:00 → 22:00
                    if ($currentTime >= $from && $currentTime <= $to) return true;
                } else {
                    // Overnight starting today: 21:00 → 02:00
                    if ($currentTime >= $from) return true;
                }
            }
        }

        // Check yesterday's schedule — maybe it's an overnight shift still running
        $yesterdayName  = strtolower($now->copy()->subDay()->englishDayOfWeek);
        $yesterdayHours = $this->work_hours->firstWhere('day', $yesterdayName);

        if ($yesterdayHours && !$yesterdayHours->is_closed && !$yesterdayHours->is_24_hours) {
            if ($yesterdayHours->from && $yesterdayHours->to) {
                $from = $yesterdayHours->from;
                $to   = $yesterdayHours->to;

                // Overnight from yesterday: if from > to and current time is still before closing
                if ($from > $to && $currentTime <= $to) return true;
            }
        }

        return false;
    }
}
