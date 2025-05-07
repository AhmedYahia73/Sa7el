<?php

namespace App\Http\Controllers\api\User\Property;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

use App\Models\Appartment;
use App\Models\AppartmentCode;

class PropertyController extends Controller
{
    public function __construct(private Appartment $appartment,
    private AppartmentCode $appartment_code){}

    public function my_property(Request $request){
        $appartment = $this->appartment_code
        ->with('appartment.type')
        ->where('type', 'owner')
        ->orWhere('type', 'renter')
        ->where('from', '<=', date('Y-m-d'))
        ->where('to', '>=', date('Y-m-d'))
        ->get()
        ->pluck('appartment');

        return response()->json([
            'appartment' => $appartment
        ]);
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

        $appartment_code = $this->appartment_code
        ->where('type', 'owner')
        ->where('village_id', $request->village_id)
        ->where('code', $request->code)
        ->orWhere('type', 'renter')
        ->where('from', '<=', date('Y-m-d'))
        ->where('to', '>=', date('Y-m-d'))
        ->where('village_id', $request->village_id)
        ->where('code', $request->code)
        ->first();
        
        if (empty($appartment_code)) {
            return response()->json([
                'errors' => 'appartment is not found'
            ]);
        }

        $appartment_code->user_id = $request->user()->id;
        $appartment_code->save();
        if ($appartment_code->type == 'owner') {
            $this->appartment
            ->where('id', $appartment_code->appartment_id)
            ->update([
                'user_id' => $request->user()->id
            ]);
        }

        return response()->json([
            'success' => 'You add data success'
        ]);
    }
}
