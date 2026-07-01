<?php

namespace App\Http\Controllers\api\Village\Setting;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

use App\Models\VillageSetting;
use App\Models\AppartmentType;

class SettingController extends Controller
{
    
    public function view(Request $request){
        $settings = VillageSetting::
        where("village_id", $request->user()->village_id)
        ->with(["type" => function($query){
            $query->select("id", "name");
        }])
        ->get();

        return response()->json([
            "settings" => $settings
        ]);
    }

    public function lists(Request $request){
        $appartments_types = AppartmentType::
        where("status", 1)
        ->get()
        ->map(function($item){
            return [
                "id" => $item->id,
                "name" => $item->name, 
            ];
        });

        return response()->json([
            "appartments_types" => $appartments_types
        ]);
    }

    public function show(Request $request, $id){
        $settings = VillageSetting::
        where("village_id", $request->user()->village_id)
        ->where("id", $id)
        ->with(["type" => function($query){
            $query->select("id", "name");
        }])
        ->firstOrFail();

        return response()->json([
            "settings" => $settings
        ]);
    }

    public function store(Request $request){
        $validator = Validator::make($request->all(), [
            'renter_limit' => 'required|numeric',
            'appartment_type_id' => 'required|exists:appartment_types,id',
        ]);
        if ($validator->fails()) { // if Validate Make Error Return Message Error
            return response()->json([
                'errors' => $validator->errors(),
            ],400);
        }

        $data = VillageSetting::
        create([
            "renter_limit" => $request->renter_limit,
            "village_id" => $request->user()->village_id,
            "appartment_type_id" => $request->appartment_type_id
        ]);

        return response()->json([
            "success" => "You add data success",
            "data" => $data
        ]);
    }

    public function update(Request $request, $id){
        $validator = Validator::make($request->all(), [
            'renter_limit' => 'required|numeric',
            'appartment_type_id' => 'required|exists:appartment_types,id',
        ]);
        if ($validator->fails()) { // if Validate Make Error Return Message Error
            return response()->json([
                'errors' => $validator->errors(),
            ],400);
        }

        $settings = VillageSetting::
        where("village_id", $request->user()->village_id)
        ->where("id", $id)
        ->firstOrFail(); 
        $settings->update([
            "renter_limit" => $request->renter_limit,
            "appartment_type_id" => $request->appartment_type_id
        ]); 

        return response()->json([
            "success" => "You update data success"
        ]);
    }

    public function delete(Request $request, $id){
        $settings = VillageSetting::
        where("village_id", $request->user()->village_id)
        ->where("id", $id)
        ->firstOrFail(); 
        $settings->delete();

        return response()->json([
            "success" => "You delete data success"
        ]);
    }
}
