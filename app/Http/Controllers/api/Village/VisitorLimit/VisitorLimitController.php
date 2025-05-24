<?php

namespace App\Http\Controllers\api\Village\VisitorLimit;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

use App\Models\VisitorLimit;

class VisitorLimitController extends Controller
{
    public function __construct(private VisitorLimit $visitor_limit){}
    use image;

    public function view(Request $request){
        $visitor_limit = $this->visitor_limit
        ->where('village_id', $request->user()->village_id)
        ->orderByDesc('id')
        ->first();

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

    public function modify(Request $request, $id){
        // guest, worker, delivery,
        $validator = Validator::make($request->all(), [
            'guest' => 'required|numeric',
            'worker' => 'required|numeric',
            'delivery' => 'required|numeric', 
        ]);
        if ($validator->fails()) { // if Validate Make Error Return Message Error
            return response()->json([
                'errors' => $validator->errors(),
            ],400);
        }
        $visitorRequest = $validator->validated();
        $visitor_limit = $this->visitor_limit
        ->where('id', $id)
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

    public function delete($id){
        $visitor_limit = $this->visitor_limit
        ->where('id', $id)
        ->first();
        if (empty($visitor_limit)) {
            return response()->json([
                'errors' => 'visitor_limit not found'
            ], 400);
        }
        $visitor_limit->delete();

        return response()->json([
            'success' => 'You delete data success'
        ]);
    }
}
