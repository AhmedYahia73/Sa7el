<?php

namespace App\Http\Controllers\api\Village\PaymentRequest;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

use App\Models\PaymentMaintenanceRequest;

class PaymentRequestController extends Controller
{
    public function __construct(private PaymentMaintenanceRequest $payment_request){}

    public function view(){
        $upcoming = $this->payment_request
        ->where('status', 'pending')
        ->with('maintenance', 'user')
        ->where('village_id', $request->user()->village_id)
        ->get();
        $history = $this->payment_request
        ->where('status', '!=', 'pending')
        ->with('maintenance:id,name', 'user:id,name,phone')
        ->where('village_id', $request->user()->village_id)
        ->get();

        return response()->json([
            'upcoming' => $upcoming,
            'history' => $history,
        ]);
    }

    public function status(Request $request, $id){
        $validator = Validator::make($request->all(), [
            'status' => 'required|in:accepted,rejected',
        ]);
        if ($validator->fails()) { // if Validate Make Error Return Message Error
            return response()->json([
                'errors' => $validator->errors(),
            ],400);
        }
        $upcoming = $this->payment_request
        ->where('id', $id)
        ->where('village_id', $request->user()->village_id)
        ->update([
            'status' => $request->status
        ]);
        
        return response()->json([
            'success' => $request->status,
        ]);
    }
}
