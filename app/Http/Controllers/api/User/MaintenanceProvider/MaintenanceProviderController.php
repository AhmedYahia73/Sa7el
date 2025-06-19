<?php

namespace App\Http\Controllers\api\User\MaintenanceProvider;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

use App\Models\MaintenanceType;
use App\Models\ServiceProvider;
use App\Models\MProviderVideos;
use App\Models\MaintenanceProviderGallery;

class MaintenanceProviderController extends Controller
{
    public function __construct(private MaintenanceType $maintenance_type,
    private ServiceProvider $maintenance_provider, 
    private MaintenanceProviderGallery $maintenance_provider_gallery,
    private MProviderVideos $maintenance_provider_video){}

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
                        'id' => $element->id,
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

    public function love(Request $request, $id){
        $validator = Validator::make($request->all(), [
            'love' => 'required|boolean',
        ]);
        if ($validator->fails()) { // if Validate Make Error Return Message Error
            $firstError = $validator->errors()->first();
            return response()->json([
                'errors' => $firstError,
            ],400);
        }
        
        $love = $request->love;
        $maintenance_provider = $this->maintenance_provider
        ->where('id', $id)
        ->first();
        if ($love) {
            $maintenance_provider->love_user()->detach($request->user()->id);
            $maintenance_provider->love_user()->attach($request->user()->id);
        } else {
            $maintenance_provider->love_user()->detach($request->user()->id);
        }
        
        return response()->json([
            'success' => 'You update react success'
        ]);
    }

    public function love_history(Request $request){
        $validator = Validator::make($request->all(), [
            'local' => 'required|in:en,ar',
        ]);
        if ($validator->fails()) { // if Validate Make Error Return Message Error
            $firstError = $validator->errors()->first();
            return response()->json([
                'errors' => $firstError,
            ],400);
        }
        $maintenance_providers = $this->maintenance_provider
        ->whereHas('love_user', function($query) use($request){
            $query->where('users.id', $request->user()->id);
        })
        ->get()
        ->map(function($item) use($request){
            return [
                'id' => $item->id,
                'name' => $request->local == 'en' ?
                $item->name : $item->ar_name?? $item->name,
                'image' => $item->image_link,
                'location' => $item->location,
                'phone' => $item->phone,
                'from' => $item->open_from,
                'to' => $item->open_to,
                'status' => $item->status,
                'description' => $request->local == 'en' ?
                $item->description : $item->ar_description?? $item->description,
                'loves_count' => count($item->love_user),
                'my_love' => count($item->love_user->where('id', $request->user()->id)) > 0
                ? true : false,
            ];
        });
        
        return response()->json([
            'maintenance_providers' => $maintenance_providers
        ]);
    }

    public function image_love(Request $request, $id){
        $validator = Validator::make($request->all(), [
            'love' => 'required|boolean',
        ]);
        if ($validator->fails()) { // if Validate Make Error Return Message Error
            $firstError = $validator->errors()->first();
            return response()->json([
                'errors' => $firstError,
            ],400);
        }
        
        $love = $request->love;
        $maintenance_provider_gallery = $this->maintenance_provider_gallery
        ->where('id', $id)
        ->first();
        if ($love) {
            $maintenance_provider_gallery->love()->detach($request->user()->id);
            $maintenance_provider_gallery->love()->attach($request->user()->id);
        } else {
            $maintenance_provider_gallery->love()->detach($request->user()->id);
        }
        
        return response()->json([
            'success' => 'You update react success'
        ]);
    }

    public function video_love(Request $request, $id){
        $validator = Validator::make($request->all(), [
            'love' => 'required|boolean',
        ]);
        if ($validator->fails()) { // if Validate Make Error Return Message Error
            $firstError = $validator->errors()->first();
            return response()->json([
                'errors' => $firstError,
            ],400);
        }
        
        $love = $request->love;
        $maintenance_provider_video = $this->maintenance_provider_video
        ->where('id', $id)
        ->first();
        if ($love) {
            $maintenance_provider_video->love()->detach($request->user()->id);
            $maintenance_provider_video->love()->attach($request->user()->id);
        } else {
            $maintenance_provider_video->love()->detach($request->user()->id);
        }
        
        return response()->json([
            'success' => 'You update react success'
        ]);
    }
}
