<?php

namespace App\Http\Controllers\api\Village\Rent;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

use App\Models\AppartmentCode;

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
