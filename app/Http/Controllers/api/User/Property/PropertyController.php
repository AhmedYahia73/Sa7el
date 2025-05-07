<?php

namespace App\Http\Controllers\api\User\Property;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

use App\Models\Appartment;

class PropertyController extends Controller
{
    public function __construct(private Appartment $appartment){}

    public function my_property(Request $request){
        $appartment = $this->appartment
        ->where('user_id', $request->user()->id)
        ->get();
    }

    public function add_property(Request $request){
        $validator = Validator::make($request->all(), [
            'village_id' => 'required|exists:villages,id',
            'code' => 'required',
        ]);
        if ($validator->fails()) { // if Validate Make Error Return Message Error
            return response()->json([
                'error' => $validator->errors(),
            ],400);
        }

        $appartment = $this->appartment
        ->where('village_id', $request->village_id)
        ->where('code', $request->code)
        ->first();
        if (empty($appartment)) {
            return response()->json([
                'errors' => 'appartment is not found'
            ]);
        }
        $appartment->user_id = $request->user()->id;
        $appartment->save();

        return response()->json([
            'success' => 'You add data success'
        ]);
    }
}
