<?php

namespace App\Http\Controllers\api\Village\VisitorLimit;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

use App\Models\VisitorLimit;

class VisitorLimitController extends Controller
{
    public function __construct(private VisitorLimit $visitor_limit){} 

    public function view(Request $request){
        $visitor_limit = $this->visitor_limit
        ->where('village_id', $request->user()->village_id)
        ->orderByDesc('id')
        ->first();
        if (empty($visitor_limit)) { 
            $visitor_limit = $this->visitor_limit
            ->create([
                'village_id' => $request->user()->village_id,
                'renter_guest' => 1,
                'renter_worker' => 1,
                'renter_delivery' => 1,
                'guest' => 1,
                'worker' => 1,
                'delivery' => 1,
            ]);
        }

        return response()->json([
            'visitor_limit' => $visitor_limit,
        ]);
    }

    public function create(Request $request){
        // guest, worker, delivery,
        $validator = Validator::make($request->all(), [
            'guest' => 'required|numeric',
            'worker' => 'required|numeric',
            'delivery' => 'required|numeric',
            'renter_guest' => 'required|numeric',
            'renter_worker' => 'required|numeric',
            'renter_delivery' => 'required|numeric',
        ]);
        if ($validator->fails()) { // if Validate Make Error Return Message Error
            return response()->json([
                'errors' => $validator->errors(),
            ],400);
        }

        $visitorRequest = $validator->validated();
        $visitorRequest['village_id'] = $request->user()->village_id;
        $visitor_limit = $this->visitor_limit
        ->create($visitorRequest);
      
        return response()->json([
            'success' => 'You add data success'
        ]);
    }

    public function modify(Request $request){
        // guest, worker, delivery,
        $validator = Validator::make($request->all(), [
            'guest' => 'required|numeric',
            'worker' => 'required|numeric',
            'delivery' => 'required|numeric',
            'renter_guest' => 'required|numeric',
            'renter_worker' => 'required|numeric',
            'renter_delivery' => 'required|numeric',
        ]);
        if ($validator->fails()) { // if Validate Make Error Return Message Error
            return response()->json([
                'errors' => $validator->errors(),
            ],400);
        }
        $visitorRequest = $validator->validated();
        $visitor_limit = $this->visitor_limit 
        ->where('village_id', $request->user()->village_id)
        ->first();
        if (empty($visitor_limit)) {
            return response()->json([
                'errors' => 'visitor_limit not found'
            ], 400);
        } 
        $visitor_limit->update($visitorRequest);
   
        return response()->json([
            'success' => 'You update data success'
        ]);
    }
}
