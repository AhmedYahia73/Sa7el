<?php

namespace App\Http\Controllers\api\Village\ServiceType;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ServiceTypeController extends Controller
{
    public function __construct(private ServiceType $service_type){}

    public function view(Request $request){
        $my_service_type = $this->service_type
        ->whereHas('village', function($query) use($request){
            $query->where('villages.id', $request->user()->id);
        })
        ->get();
        $service_type = $this->service_type
        ->whereDoesntHave('village', function($query) use($request){
            $query->where('villages.id', $request->user()->id);
        })
        ->get();

        return response()->json([
            'my_service_type' => $my_service_type,
            'service_type' => $service_type,
        ]);
    }

    public function add(Request $request){
        $validator = Validator::make($request->all(), [
            'service_type_id' => 'required|exists:service_types,id',
        ]);
        if ($validator->fails()) { // if Validate Make Error Return Message Error
            return response()->json([
                'errors' => $validator->errors(),
            ],400);
        }
        $service_type = $service_type
        ->where('id', $request->service_type_id)
        ->first();
        $service_type->village()->attach($request->user()->village_id);

        return response()->json([
            'success' => 'You add data success'
        ]);
    }

    public function delete(Request $request){
        $validator = Validator::make($request->all(), [
            'service_type_id' => 'required|exists:service_types,id',
        ]);
        if ($validator->fails()) { // if Validate Make Error Return Message Error
            return response()->json([
                'errors' => $validator->errors(),
            ],400);
        }
        $service_type = $service_type
        ->where('id', $request->service_type_id)
        ->first();
        $service_type->village()->detach($request->user()->village_id);

        return response()->json([
            'success' => 'You delete data success'
        ]);
    }
}
