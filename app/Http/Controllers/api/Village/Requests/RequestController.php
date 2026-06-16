<?php

namespace App\Http\Controllers\api\Village\Requests;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

use App\Models\CodeRequest;
use App\Models\LoginRequest;
use App\Models\AppartmentCode;
use App\Models\Appartment;

class RequestController extends Controller
{
    public function code_request(Request $request){
        $requests = CodeRequest::
        with("user", "appartment")
        ->where('village_id', $request->user()->village_id)
        ->where("status", "pending")
        ->latest() // اختياري: لترتيب الطلبات من الأحدث للأقدم
        ->paginate(15) // حدد عدد العناصر في الصفحة الواحدة (مثلاً 15)
        ->through(function($item) {
            return [
                'id' => $item->id,
                'user_name' => $item->user?->name,
                'user_phone' => $item->user?->phone,
                'user_email' => $item->user?->email,
                'appartment_unit' => $item->appartment?->unit,
                'appartment_location' => $item->appartment?->location,
                'code' => $item->code,
                'people_count' => collect($item->appartment_codes)->count(),
            ];
        });

        return response()->json([
            'code_requests' => $requests
        ]);
    }

    public function code_request_status(Request $request, $id){
        $validator = Validator::make($request->all(), [
            'status' => 'required|in:approve,reject',
        ]);
        if ($validator->fails()) { // if Validate Make Error Return Message Error
            return response()->json([
                'errors' => $validator->errors(),
            ],400);
        }

        $codes = CodeRequest::where('id', $id)
        ->where('village_id', $request->user()->village_id)->first();
        $appartment_code = AppartmentCode::
        where("code", $codes->code)
        ->where("appartment_id", $codes->appartment_id)
        ->first();
        if($request->status == 'approve'){
            $appartment_code->user_id = $codes->user_id;
            $appartment_code->save();
            if ($appartment_code->type == 'owner') {
                Appartment::
                where('id', $appartment_code->appartment_id)
                ->update([
                    'user_id' => $request->user()->id
                ]);
            }
        }
        $codes->update(['status' => $request->status]);

        return response()->json([
            'message' => 'Code request status updated successfully'
        ]);
    }
    
    public function login_request(Request $request){
        $requests = LoginRequest::with(['user', 'unit']) // جلب بيانات المستخدم مسبقاً لتسريع الأداء
            ->where("status", "pending")
            ->where('village_id', $request->user()->village_id)
            ->latest() // ترتيب من الأحدث إلى الأقدم
            ->paginate($request->get('per_page', 15)) // جلب عدد عناصر معين (الافتراضي 15)
            ->through(function($item){
                return [
                    'id' => $item->id,
                    'user_name' => $item->user?->name,
                    'user_phone' => $item->user?->phone,
                    'user_email' => $item->user?->email,
                    'appartment_unit' => $item->appartment?->unit,
                    'appartment_location' => $item->appartment?->location,
                    'people_count' => collect($item->appartment_codes)->count(),
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

        $login_request = LoginRequest::where('id', $id)
            ->where('village_id', $request->user()->village_id)
            ->first();
        $login_request->update(['status' => $request->status]);

        return response()->json([
            'message' => 'Login request status updated successfully'
        ]);
    }
}
