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
            $maintenance_provider = $request->maintenance_provider;
            return [
                'id' => $item->id,
                'name' => $request->local == 'en' ?
                $item->name : $item->ar_name?? $item->name,
                'image' => $item->image_link,
                'maintenance_provider' => [
                    'name' => $request->local == 'en' ?
                    $maintenance_provider->name : $maintenance_provider->ar_name?? $maintenance_provider->name,
                    'location' => $maintenance_provider->location,
                    'from' => $maintenance_provider->open_from,
                    'to' => $maintenance_provider->open_to,
                    'status' => $maintenance_provider->status,
                    'village' => $maintenance_provider?->village?->name,
                    'cover_image' => $maintenance_provider->cover_image_link,

                    'service_price' => optional($maintenance_provider?->service_price?->where('status', 1))->pluck('image_link') ?? collect([]),
                    'videos' => $maintenance_provider?->videos?->where('status', 1)?->values()->map(function($element){
                        return [
                            'id' => $element->id,
                            'description' => $element->description,
                            'video_link' => $element->video_link,
                            'love_count' => $element->love->count(),
                            'my_love' => $element->my_love->count() > 0 ? true : false,
                        ];
                    }),
                    'watts_status' => $maintenance_provider?->contact?->watts_status ?? 0,
                    'phone_status' => $maintenance_provider?->contact?->phone_status ?? 0,
                    'website_status' => $maintenance_provider?->contact?->website_status ?? 0,
                    'instagram_status' => $maintenance_provider?->contact?->instagram_status ?? 0,
                    'watts' => $maintenance_provider?->contact?->watts ?? null,
                    'phone' => $maintenance_provider?->contact?->phone ?? null,
                    'website' => $maintenance_provider?->contact?->website ?? null,
                    'instagram' => $maintenance_provider?->contact?->instagram ?? null,
 
                    'gallery' => $maintenance_provider->gallery->map(function($element){
                        return [
                            'id' => $element->id,
                            'image' => $element->image_link,
                            'love_count' => $element->love->count(),
                            'my_love' => $element->my_love->count() > 0 ? true : false,
                        ];
                    }),
                    'description' => $request->local == 'en' ?
                    $maintenance_provider->description : $maintenance_provider->ar_description?? $maintenance_provider->description,
                    'loves_count' => count($maintenance_provider->love_user),
                    'my_love' => count($maintenance_provider->love_user->where('id', $request->user()->id)) > 0
                    ? true :false,
                ]
            ];
        });

        return response()->json([
            'maintenance_type' => $maintenance_type
        ]);
    }
}
