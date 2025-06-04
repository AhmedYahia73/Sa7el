<?php

namespace App\Http\Controllers\api\Village\PaymentRequest;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

use App\Models\PaymentMaintenanceRequest;
use App\Models\AppartmentMaintenanceFeez;

class PaymentRequestController extends Controller
{
    public function __construct(private PaymentMaintenanceRequest $payment_request,
    private AppartmentMaintenanceFeez $appartment_fees){}

    public function view(Request $request){
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
        $payment_request = $this->payment_request
        ->where('id', $id)
        ->where('village_id', $request->user()->village_id)
        ->first();
        if (empty($payment_request)) {
            return response()->json([
                'errors' => 'id is wrong',
            ], 400);
        }
        $payment_request->update([
            'status' => $request->status
        ]);
        if ($request->status == 'accepted') {
            $appartment_fees = $this->appartment_fees
            ->where('appartment_id', $payment_request->appartment_id)
            ->where('maintenance_id', $payment_request->maintenance_feez_id)
            ->first();
            if (empty($appartment_fees)) {
                $this->appartment_fees
                ->create([
                    'appartment_id' => $payment_request->appartment_id,
                    'maintenance_id' => $payment_request->maintenance_feez_id,
                    'user_id' => $payment_request->user_id,
                    'paid' => $payment_request->paid,
                    'total' => $payment_request?->maintenance?->price,
                ]);
            } 
            else {
                $appartment_fees
                ->update([
                    'appartment_id' => $payment_request->appartment_id,
                    'maintenance_id' => $payment_request->maintenance_feez_id,
                    'user_id' => $payment_request->user_id,
                    'paid' => $payment_request->paid,
                    'total' => $payment_request?->maintenance?->price,
                ]);
            }
            
        }
        
        return response()->json([
            'success' => $request->status,
        ]);
    }
}
