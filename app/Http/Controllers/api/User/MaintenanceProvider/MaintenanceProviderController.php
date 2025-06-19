<?php

namespace App\Http\Controllers\api\User\MaintenanceProvider;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

use App\Models\MaintenanceType;

class MaintenanceProviderController extends Controller
{
    public function __construct(private MaintenanceType $maintenance_type){}

    public function view(Request $request){
        $maintenance_type = $this->maintenance_type
        ->with('maintenance_provider')
        ->where('status', 1)
        ->get()
        ->map(function($item) use($request){
            return [
                'id' => $item->id,
                'name' => $request->local == 'en' ?
                $item->name : $item->ar_name?? $item->name,
                'image' => $item->image_link,
                'maintenance_provider' => $item->maintenance_provider
                ->map(function($element) use($request){
                    return [
                        'name' => $request->local == 'en' ?
                        $element->name : $element->ar_name?? $element->name,
                        'location' => $element->location,
                        'from' => $element->open_from,
                        'to' => $element->open_to,
                        'status' => $element->status,
                        'village' => $element?->village?->name,
                        'cover_image' => $element->cover_image_link,

                        'service_price' => optional($element?->service_price?->where('status', 1))->pluck('image_link') ?? collect([]),
                        'videos' => $element?->videos?->where('status', 1)?->values()->map(function($element){
                            return [
                                'id' => $element->id,
                                'description' => $element->description,
                                'video_link' => $element->video_link,
                                'love_count' => $element->love->count(),
                                'my_love' => $element->my_love->count() > 0 ? true : false,
                            ];
                        }),
                        'watts_status' => $element?->contact?->watts_status ?? 0,
                        'phone_status' => $element?->contact?->phone_status ?? 0,
                        'website_status' => $element?->contact?->website_status ?? 0,
                        'instagram_status' => $element?->contact?->instagram_status ?? 0,
                        'watts' => $element?->contact?->watts ?? null,
                        'phone' => $element?->contact?->phone ?? null,
                        'website' => $element?->contact?->website ?? null,
                        'instagram' => $element?->contact?->instagram ?? null,
    
                        'gallery' => $element->gallery->map(function($element){
                            return [
                                'id' => $element->id,
                                'image' => $element->image_link,
                                'love_count' => $element->love->count(),
                                'my_love' => $element->my_love->count() > 0 ? true : false,
                            ];
                        }),
                        'description' => $request->local == 'en' ?
                        $element->description : $element->ar_description?? $element->description,
                        'loves_count' => count($element->love_user),
                        'my_love' => count($element->love_user->where('id', $request->user()->id)) > 0
                        ? true :false,
                    ];
                })
            ];
        });

        return response()->json([
            'maintenance_type' => $maintenance_type
        ]);
    }
}
