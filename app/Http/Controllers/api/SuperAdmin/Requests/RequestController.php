<?php

namespace App\Http\Controllers\api\SuperAdmin\Requests;

use App\Http\Controllers\Controller;
use App\Models\LoginRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class RequestController extends Controller
{

    public function login_request(Request $request){
        $requests = LoginRequest::with(['user']) // جلب بيانات المستخدم مسبقاً لتسريع الأداء
            ->where('village_id', $request->user()->village_id)
            ->where("status", "pending")
            ->latest() // ترتيب من الأحدث إلى الأقدم
            ->paginate($request->get('per_page', 15)) // جلب عدد عناصر معين (الافتراضي 15)
            ->through(function($item){
                return [
                    'id' => $item->id,
                    'user_name' => $item->user?->name,
                    'user_phone' => $item->user?->phone,
                    'user_email' => $item->user?->email,
                    'ip_address' => $item->ip_address,
                ];
            });

        return response()->json([
            'login_requests' => $requests
        ]);
    }

    public function login_request_status(Request $request, $id){
        $validator = Validator::make($request->all(), [
            'status' => 'required|in:approve,reject',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors(),
            ],400);
        }

        $login_request = LoginRequest::where('id', $id)->first();
        $login_request->update(['status' => $request->status]);

        return response()->json([
            'message' => 'Login request status updated successfully'
        ]);
    }
}
