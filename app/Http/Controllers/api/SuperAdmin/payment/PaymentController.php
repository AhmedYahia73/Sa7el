<?php

namespace App\Http\Controllers\api\SuperAdmin\payment;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

use App\Models\Payment;
use App\Models\Village;
use App\Models\Provider;
use App\Models\ServiceProvider;

class PaymentController extends Controller
{
    public function __construct(private Payment $payments,
    private ServiceProvider $service_provider,
    private Village $village, private Provider $provider,){}

    public function view(){
        $pending_payments = $this->payments
        ->with('payment_method:id,name', 'package:id,name', 'service:id,name', 'village:id,name', 'provider:id,name')
        ->where('status', 'pending')
        ->get();
        $history_payments = $this->payments
        ->with('payment_method:id,name', 'package:id,name', 'service:id,name', 'village:id,name', 'provider:id,name')
        ->where('status', '!=', 'pending')
        ->get();

        return response()->json([
            'pending_payments' => $pending_payments,
            'history_payments' => $history_payments,
        ]);
    }

    public function approve(Request $request, $id){
        $payment = $this->payments
        ->where('id', $id)
        ->first();
        $nextYear = Carbon::now()->addYear();
        if ($payment->type == 'village') {
            $village = $this->village
            ->where('id', $payment->village_id)
            ->update([
                'package_id' => $payment->package_id ,
                'from' => date('Y-m-d'),
                'to' => $nextYear,
            ]);
        }
        elseif($payment->type == 'provider'){
            $provider = $this->provider
            ->where('id', $payment->provider_id)
            ->update([
                'package_id' => $payment->package_id ,
                'from' => date('Y-m-d'),
                'to' => $nextYear,
            ]);
        }

        $payment->update([
            'status' => 'approved'
        ]);

        return response()->json([
            'success' => 'You approve success'
        ]);
    }

    public function reject(Request $request, $id){
        $validator = Validator::make($request->all(), [
            'rejected_reason' => 'required',
        ]);
        if ($validator->fails()) { // if Validate Make Error Return Message Error
            return response()->json([
                'errors' => $validator->errors(),
            ],400);
        }
        $this->payments
        ->where('id', $id)
        ->update([
            'rejected_reason' => $request->rejected_reason,
            'status' => 'rejected',
        ]);

        return response()->json([
            'success' => 'You reject success'
        ]);
    }
}
