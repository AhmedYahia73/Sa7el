<?php

namespace App\Http\Controllers\api\SuperAdmin\Requests;

use App\Http\Controllers\Controller;
use App\Models\Appartment;
use App\Models\AppartmentCode;
use App\Models\CodeRequest;
use App\Models\LoginRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class RequestController extends Controller
{
    
    public function code_request(Request $request){
        $requests = CodeRequest::
        where("status", "pending")
        ->with("village", "user", "appartment")
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
                "village" => $item?->village?->name,
                'code' => $item->code,
                'people_count' => collect($item->appartment_codes)->count(),
                "created_at" => $item->created_at,
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
        ->first();
        $codes = AppartmentCode::
        where("code", $codes->code)
        ->where("appartment_id", $codes->appartment_id) 
        ->get();
        if($codes->count() == 0 ){
            return response()->json([
                'message' => 'Code is wrong'
            ], 400); 
        }
        if($codes->count() < $codes[0]->people){
            $code_item = $codes[0];
            $people = $codes->count() - $codes[0]->people;
            for ($i=0; $i < $people; $i++) { 
                AppartmentCode::create([
                    'appartment_id' => $code_item->appartment_id, 
                    'village_id' => $code_item->village_id,
                    'from' => $code_item->from,
                    'to' => $code_item->to,
                    'type' => $code_item->type,
                    'code' => $code_item->code,
                    'people' => $code_item->people,
                    'image' => $code_item->image,
                    'owner_id' => $code_item->owner_id,
                    'user_type' => $code_item->user_type,
                ]);
            }
        }
        $appartment_code = AppartmentCode::
        where("code", $codes->code)
        ->where("appartment_id", $codes->appartment_id)
        ->whereNull("user_id")
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
        $requests = LoginRequest::with(['user', 'village', 'appartment']) // جلب بيانات المستخدم مسبقاً لتسريع الأداء
            ->where("status", "pending") 
            ->latest() // ترتيب من الأحدث إلى الأقدم
            ->paginate($request->get('per_page', 15)) // جلب عدد عناصر معين (الافتراضي 15)
            ->through(function($item){
                return [
                    'id' => $item->id,
                    'user_name' => $item->user?->name,
                    'user_phone' => $item->user?->phone,
                    'user_email' => $item->user?->email,
                    "village" => $item?->village?->name,
                    'ip_address' => $item->ip_address,
                    'appartment_unit' => $item->appartment?->unit,
                    'appartment_location' => $item->appartment?->location,
                    'people_count' => collect($item->appartment_codes)->count(),
                    "created_at" => $item->created_at,
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
            ->first();
        $login_request->update(['status' => $request->status]);

        return response()->json([
            'message' => 'Login request status updated successfully'
        ]);
    }
}
