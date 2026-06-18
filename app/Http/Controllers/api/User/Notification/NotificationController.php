<?php

namespace App\Http\Controllers\api\User\Notification;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class NotificationController extends Controller
{

    public function notification(Request $request){
        $notifications = Notification::
        where("type", "user")
        ->where("user_id", $request->user()->id)
        ->orderByDesc("id")
        ->paginate(10)
        ->through(function($item){
            return [
                "id" => $item->id,
                "notification" => $item->notification,
                "is_read" => $item->is_read, 
            ];
        });

        return response()->json([
            "notifications" => $notifications
        ]);
    }

    public function is_read(Request $request){
        $validator = Validator::make($request->all(), [
            'items' => 'required|array', 
            'items.*' => 'exists:notifications,id', 
        ]);
        if ($validator->fails()) { // if Validate Make Error Return Message Error
            return response()->json([
                'errors' => $validator->errors(),
            ],400);
        }

        Notification::
        whereIn("id", $request->items)
        ->where("user_id", $request->user()->id)
        ->update([
            "is_read" => true
        ]);

        return response()->json([
            "success" => "You update data success"
        ]);
    }

    public function read_all(Request $request){  
        Notification:: 
        where("user_id", $request->user()->id)
        ->update([
            "is_read" => true
        ]);

        return response()->json([
            "success" => "You update data success"
        ]);
    }
}
