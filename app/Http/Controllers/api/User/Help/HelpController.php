<?php

namespace App\Http\Controllers\api\User\Help;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

use App\Models\HelpGroup;
use App\Models\HelpVideo;

class HelpController extends Controller
{
    public function groups(Request $request){
        $validator = Validator::make($request->all(), [
            'local' => 'required|in:en,ar',
        ]);
        if ($validator->fails()) { // if Validate Make Error Return Message Error
            $firstError = $validator->errors()->first();
            return response()->json([
                'errors' => $firstError,
            ],400);
        }
        $local = $request->local;
        $data = HelpGroup::
        where("status", 1)
        ->get()
        ->map(function($item) use($local){
            return [
                "id" => $item->id,
                "name" => $item->name[$local],
            ];
        });

        return response()->json([
            "data" => $data
        ]);
    }
    
    public function videos(Request $request){
        $validator = Validator::make($request->all(), [
            'local' => 'required|in:en,ar',
            'help_group_id' => 'required|exists:help_groups,id',
        ]);
        if ($validator->fails()) { // if Validate Make Error Return Message Error
            $firstError = $validator->errors()->first();
            return response()->json([
                'errors' => $firstError,
            ],400);
        }
        $local = $request->local;
        $data = HelpVideo::
        where("status", 1)
        ->where("help_group_id", $request->help_group_id)
        ->get()
        ->map(function($item) use($local){
            return [
                "id" => $item->id,
                "name" => $item->name[$local],
                "description" => $item->description[$local],
                "video" => $local == "en" ? $item->en_video_link : $item->ar_video_link,
            ];
        });

        return response()->json([
            "data" => $data
        ]);
    }
}
