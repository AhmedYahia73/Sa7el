<?php

namespace App\Http\Controllers\api\User\Home;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

use App\Models\Village;
use App\Models\Popup;

class HomeController extends Controller
{
    public function village($id){
        $village = Village::
        where("id", $id)
        ->first();

        return response()->json([
            "logo" => $village->logo_link,
            "name" => $village->name,
        ]);
    }

    public function popup_all(Request $request){
        $validator = Validator::make($request->all(), [
            'locale' => 'required|in:ar,en',
        ]);
        if ($validator->fails()) { // if Validate Make Error Return Message Error
            return response()->json([
                'errors' => $validator->errors(),
            ],400);
        }

        $locale = $request->locale;
        $popup = Popup::
        where("all", 1)
        ->first();
        if($popup){
            $popup = [
                "title" => $locale == "en" ? 
                $popup->title : $popup->ar_title,
                "description" => $locale == "en" ? 
                $popup->description : $popup->ar_description,
                "image" => $locale == "en" ? 
                $popup->image_link : $popup->ar_image_link,
            ];
        }

        return response()->json([
            "popup" => $popup
        ]);
    }

    public function village_popup(Request $request){
        $validator = Validator::make($request->all(), [
            'locale' => 'required|in:ar,en',
            'village_id' => "required|exists:villages,id"
        ]);
        if ($validator->fails()) { // if Validate Make Error Return Message Error
            return response()->json([
                'errors' => $validator->errors(),
            ],400);
        }

        $locale = $request->locale;
        $popup = Popup::
        where("village_id", $request->village_id)
        ->first();
        if($popup){
            $popup = [
                "title" => $locale == "en" ? 
                $popup->title : $popup->ar_title,
                "description" => $locale == "en" ? 
                $popup->description : $popup->ar_description,
                "image" => $locale == "en" ? 
                $popup->image_link : $popup->ar_image_link,
            ];
        }

        return response()->json([
            "popup" => $popup
        ]);
    }
}
