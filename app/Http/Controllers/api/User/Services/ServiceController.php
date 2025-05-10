<?php

namespace App\Http\Controllers\api\User\Services;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

use App\Models\ServiceType;

class ServiceController extends Controller
{
    public function __construct(private ServiceType $services){}

    public function view(Request $request){
        $validator = Validator::make($request->all(), [
            'village_id' => 'required|exists:villages,id',
        ]);
        if ($validator->fails()) { // if Validate Make Error Return Message Error
            $firstError = $validator->errors()->first();
            return response()->json([
                'error' => $firstError,
            ],400);
        }
        $services = $this->services
        ->whereHas('village', function($query) use($request){
            $query->where('villages.id', $request->village_id);
        })
        ->where('status', 1)
        ->with('providers') // load all providers
        ->get()
        ->map(function($query){
            return [
                'id' => $item->id,
                'name' => $request->local == 'en' ?
                $item->name : $item->ar_name?? $item->name,
                'image' => $item->image_link,
                'status' => $item->status,
                'description' => $request->local == 'en' ?
                $item->description : $item->ar_description?? $item->description,
                'my_providers' => $item->my_providers,
                'other_providers' => $item->other_providers,
            ];
        });
        // Optionally filter in PHP
        $services->each(function ($service) use ($request) {
            $service->my_providers = $service->providers
            ->where('status', 1)->where('village_id', $request->village_id)->values()
            ->map(function($query){
                return [
                    'id' => $item->id,
                    'name' => $request->local == 'en' ?
                    $item->name : $item->ar_name?? $item->name,
                    'image' => $item->image_link,
                    'status' => $item->status,
                    'description' => $request->local == 'en' ?
                    $item->description : $item->ar_description?? $item->description,
                ];
            });
            $service->other_providers = $service->providers
            ->where('status', 1)->where('village_id', '!=', $request->village_id)->values()
            ->map(function($query){
                return [
                    'id' => $item->id,
                    'name' => $request->local == 'en' ?
                    $item->name : $item->ar_name?? $item->name,
                    'image' => $item->image_link,
                    'status' => $item->status,
                    'description' => $request->local == 'en' ?
                    $item->description : $item->ar_description?? $item->description,
                ];
            });
        });

        return response()->json([
            'services' => $services
        ]);
    }
}
