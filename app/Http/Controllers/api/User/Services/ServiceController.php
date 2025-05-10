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
        $services = $this->services()
        ->where('village_id', $request->village_id)
        ->with('providers') // load all providers
        ->get();
        // Optionally filter in PHP
        $services->each(function ($service) use ($request) {
            $service->my_providers = $service->providers->where('village_id', $request->user()->village_id)->values();
            $service->other_providers = $service->providers->where('village_id', '!=', $request->user()->village_id)->values();
        });

        return response()->json([
            'services' => $services
        ]);
    }
}
