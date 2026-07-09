<?php

namespace App\Http\Controllers\api\Village\Rent;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
 
use App\Models\AppartmentCode;
use App\Models\Appartment;

class RentController extends Controller
{
    public function __construct(private AppartmentCode $rents){}

    public function view(Request $request){
        $rents = $this->rents
        ->with('owner', 'appartment', 'user')
        ->where('type', 'renter') 
        ->where('village_id', $request->user()->village_id)
        ->where("to", ">=", date("Y-m-d"))
        ->orderByDesc('id')
        ->get();

        return response()->json([
            'rents' => $rents,
        ]);
    }

    public function unit_renters(Request $request){
        $validator = Validator::make($request->all(), [
            'appartment_id' => 'required|exists:appartments,id',
        ]);

        if ($validator->fails()) { 
            return response()->json([
                'errors' => $validator->errors(),
            ], 400);
        }

        $rents = $this->rents
            ->with('owner:id,name,phone', 'user:id,name,phone')
            ->where('type', 'renter') 
            ->where('village_id', $request->user()->village_id)
            ->where("appartment_id", $request->appartment_id)
            ->where("to", ">=", date("Y-m-d"))
            ->orderByDesc('id')
            ->get(); 

        return response()->json([
            'rents' => $rents,
            'rents_count' => $rents->count(),
        ]);
    }

    public function renters(Request $request){
        $validator = Validator::make($request->all(), [
            'status' => 'in:current,past,upcoming',
            'per_page' => 'integer|min:1|max:100', // اختياري: لتحديد عدد العناصر في الصفحة
        ]);

        if ($validator->fails()) { 
            return response()->json([
                'errors' => $validator->errors(),
            ], 400);
        }

        $rents = $this->rents
            ->with('owner:id,name,phone', 'appartment:id,unit', 'user:id,name,phone')
            ->where('type', 'renter') 
            ->where('village_id', $request->user()->village_id)
            ->orderByDesc('id');

        $today = date("Y-m-d");

        if ($request->has('status')) {
            if ($request->status == 'current') {
                $rents = $rents->where('from', '<=', $today)->where('to', '>=', $today);
            } else if ($request->status == 'past') {
                $rents = $rents->where('to', '<', $today);
            } else if ($request->status == 'upcoming') {
                $rents = $rents->where('from', '>', $today);
            }
        }

        // تحديد عدد العناصر لكل صفحة (مثلاً 15 عنصر كوضع افتراضي)
        $perPage = $request->get('per_page', 50);

        // استخدام paginate بدلاً من get
        $rents = $rents->paginate($perPage)->through(function($item) use ($today) {
            // تحديد الحالة بناءً على التواريخ
            if ($item->from <= $today && $item->to >= $today) {
                $item->status = "current";
            } else if ($item->to < $today) {
                $item->status = "past";
            } else {
                $item->status = "upcoming";
            }
            return $item;
        });

        return response()->json([
            'rents' => $rents,
        ]);
    }

    public function renters_numbers(Request $request){
        $all_rents = $this->rents
        ->where('type', 'renter') 
        ->where('village_id', $request->user()->village_id)
        ->count();
        $today = date("Y-m-d"); 
        $current_rents = $this->rents
        ->where('type', 'renter') 
        ->where('village_id', $request->user()->village_id)
        ->where('from', '<=', $today)
        ->where('to', '>=', $today)
        ->count();
        $past_rents = $this->rents
        ->where('type', 'renter') 
        ->where('village_id', $request->user()->village_id)
        ->where('to', '<', $today)
        ->count();
        $upcoming_rents = $this->rents
        ->where('type', 'renter') 
        ->where('village_id', $request->user()->village_id)
        ->where('from', '>', $today)
        ->count(); 

        return response()->json([
            'all_rents' => $all_rents,
            'past_rents' => $past_rents,
            'current_rents' => $current_rents,
            'upcoming_rents' => $upcoming_rents,
        ]);
    }

    public function delete_user(Request $request){
        $validator = Validator::make($request->all(), [
            'id' => 'required|exists:appartment_codes,id',
        ]);
        if ($validator->fails()) { // if Validate Make Error Return Message Error
            return response()->json([
                'errors' => $validator->errors(),
            ],400);
        }

        $code = AppartmentCode::
        where("id", $request->id)
        ->update([
            "user_id" => null
        ]);

        return response()->json([
            "success" => "You delete data success"
        ]);
    }

    public function delete_code(Request $request){
        $validator = Validator::make($request->all(), [
            'code' => 'required', 
        ]);
        if ($validator->fails()) { // if Validate Make Error Return Message Error
            return response()->json([
                'errors' => $validator->errors(),
            ],400);
        }

        $code = AppartmentCode::
        where("code", $request->code) 
        ->delete();

        return response()->json([
            "success" => "You delete data success"
        ]);
    }
}
