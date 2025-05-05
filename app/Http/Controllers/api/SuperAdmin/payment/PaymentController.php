<?php

namespace App\Http\Controllers\api\SuperAdmin\payment;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Payment;

class PaymentController extends Controller
{
    public function __construct(private Payment $payments){}

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
}
