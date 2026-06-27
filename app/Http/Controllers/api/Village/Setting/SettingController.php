<?php

namespace App\Http\Controllers\api\Village\Setting;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

use App\Models\VillageSetting;

class SettingController extends Controller
{
    public function view(Request $request){
        $settings = VillageSetting::
        where("village_id", $request->user()->village_id)
        ->first();

        return response()->json([
            "renter_limit" => $settings->renter_limit ?? 10
        ]);
    }

    public function update(Request $request){
        $validator = Validator::make($request->all(), [
            'renter_limit' => 'required|numeric',
        ]);
        if ($validator->fails()) { // if Validate Make Error Return Message Error
            return response()->json([
                'errors' => $validator->errors(),
            ],400);
        }

        $settings = VillageSetting::
        where("village_id", $request->user()->village_id)
        ->first();
        if($settings){
            $settings->update([
                "renter_limit" => $request->renter_limit
            ]);
        }
        else{
            VillageSetting::
            create([
                "renter_limit" => $request->renter_limit,
                "village_id" => $request->user()->village_id
            ]);
        }

        return response()->json([
            "success" => "You update data success"
        ]);
    }
}
