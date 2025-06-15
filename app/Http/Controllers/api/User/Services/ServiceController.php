<?php

namespace App\Http\Controllers\api\User\Services;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

use App\Models\ServiceType;
use App\Models\Provider;
use App\Models\ProviderGallary;
use App\Models\ProviderVideos;

class ServiceController extends Controller
{
    public function __construct(private ServiceType $services,
    private Provider $provider, private ProviderGallary $provider_gallery
    , private ProviderVideos $provider_video){}

    public function view(Request $request){
        $validator = Validator::make($request->all(), [
            'village_id' => 'required|exists:villages,id',
            'local' => 'required',
        ]);
        if ($validator->fails()) { // if Validate Make Error Return Message Error
            $firstError = $validator->errors()->first();
            return response()->json([
                'errors' => $firstError,
            ],400);
        }
        $services = $this->services
        ->where('status', 1)
        ->whereHas('providers', function($query) use($request){
            $query->where('village_id', $request->village_id);
        })
        ->with('providers') // load all providers
        ->get();
        // Optionally filter in PHP
        $services->each(function ($service) use ($request) {
            $service->my_providers = $service->providers
            ->where('status', 1)->where('village_id', $request->village_id)->values()
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
                    'service' => $item?->service?->name,
                    'village' => $item?->village?->name,
                    'cover_image' => $item->cover_image_link,
                    'location_map' => $item->location_map,

                    'menue' => optional($item?->menue?->where('status', 1))->pluck('image_link') ?? collect([]),
                    'videos' => $item?->videos?->where('status', 1)?->values()->map(function($element){
                        return [
                            'id' => $element->id,
                            'description' => $element->description,
                            'video_link' => $element->video_link,
                            'love_count' => $element->love->count(),
                            'my_love' => $element->my_love->count() > 0 ? true : false,
                        ];
                    }),
                    'watts_status' => $item?->contact?->watts_status ?? 0,
                    'phone_status' => $item?->contact?->phone_status ?? 0,
                    'website_status' => $item?->contact?->website_status ?? 0,
                    'instagram_status' => $item?->contact?->instagram_status ?? 0,
                    'watts' => $item?->contact?->watts ?? null,
                    'phone' => $item?->contact?->phone ?? null,
                    'website' => $item?->contact?->website ?? null,
                    'instagram' => $item?->contact?->instagram ?? null,

                    'zone' => $item?->zone?->translations
                    ->where('locale', $request->local)->first()?->value ?? $item?->zone?->name,
                    'mall' => $item?->mall?->translations
                    ->where('locale', $request->local)->first()?->value ?? $item?->mall?->name,
                    'village' => $item?->village?->name,
                    'gallery' => $item->gallery->map(function($element){
                        return [
                            'id' => $element->id,
                            'image' => $element->image_link,
                            'love_count' => $element->love->count(),
                            'my_love' => $element->my_love->count() > 0 ? true : false,
                        ];
                    }),
                    'description' => $request->local == 'en' ?
                    $item->description : $item->ar_description?? $item->description,
                    'loves_count' => count($item->love_user),
                    'my_love' => count($item->love_user->where('id', $request->user()->id)) > 0
                    ? true :false,
                ];
            });
        });
        $services = $services
        ->map(function($item) use($request){
            return [
                'id' => $item->id,
                'name' => $request->local == 'en' ?
                $item->name : $item->ar_name?? $item->name,
                'image' => $item->image_link,
                'status' => $item->status,
                'description' => $request->local == 'en' ?
                $item->description : $item->ar_description?? $item->description,
                'my_providers' => $item->my_providers, 
            ];
        });

        return response()->json([
            'services' => $services
        ]);
    }

    public function out_service(Request $request){
        $validator = Validator::make($request->all(), [
            'local' => 'required',
        ]);
        if ($validator->fails()) { // if Validate Make Error Return Message Error
            $firstError = $validator->errors()->first();
            return response()->json([
                'errors' => $firstError,
            ],400);
        }
        $services = $this->services
        ->where('status', 1)
        ->whereHas('providers')
        ->with('providers') // load all providers
        ->get();
        
        $services = $services
        ->map(function($item) use($request){
            return [
                'id' => $item->id,
                'name' => $request->local == 'en' ?
                $item->name : $item->ar_name?? $item->name,
                'image' => $item->image_link,
                'status' => $item->status,
                'description' => $request->local == 'en' ?
                $item->description : $item->ar_description?? $item->description,
                'providers' => $item->providers,
            ];
        });

        return response()->json([
            'services' => $services
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
        $provider = $this->provider
        ->where('id', $id)
        ->first();
        if ($love) {
            $provider->love_user()->detach($request->user()->id);
            $provider->love_user()->attach($request->user()->id);
        } else {
            $provider->love_user()->detach($request->user()->id);
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
        $providers = $this->provider
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
            'providers' => $providers
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
        $provider_gallery = $this->provider_gallery
        ->where('id', $id)
        ->first();
        if ($love) {
            $provider_gallery->love()->detach($request->user()->id);
            $provider_gallery->love()->attach($request->user()->id);
        } else {
            $provider_gallery->love()->detach($request->user()->id);
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
        $provider_video = $this->provider_video
        ->where('id', $id)
        ->first();
        if ($love) {
            $provider_video->love()->detach($request->user()->id);
            $provider_video->love()->attach($request->user()->id);
        } else {
            $provider_video->love()->detach($request->user()->id);
        }
        
        return response()->json([
            'success' => 'You update react success'
        ]);
    }
}
