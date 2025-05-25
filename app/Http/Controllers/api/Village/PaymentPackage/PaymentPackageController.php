<?php

namespace App\Http\Controllers\api\Village\PaymentPackage;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\trait\image;

use App\Models\Payment;

class PaymentPackageController extends Controller
{
    public function __construct(private Payment $payment){}
    use image;

    public function payment(Request $request){
        $validator = Validator::make($request->all(), [
            'payment_method_id' => 'required',
            'package_id' => 'required',
            'amount' => 'required',
            'discount' => 'required',
        ]);
        if ($validator->fails()) { // if Validate Make Error Return Message Error
            $firstError = $validator->errors()->first();
            return response()->json([
                'errors' => $firstError,
            ],400);
        }

        $paymentRequest = $validator->validated();
        $paymentRequest['type'] = 'village';
        $paymentRequest['status'] = 'pending';
        $paymentRequest['village_id'] = $request->user()->village_id;
        if (!empty($request->receipt)){
            $image_path = $this->upload($request, 'receipt', 'images/payment_receipt');
            $paymentRequest['receipt'] = $image_path;
        }
        $this->payment
        ->create($paymentRequest);

        return response()->json([
            'success' => 'You upload data success'
        ]);
    }
}
