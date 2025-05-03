<?php

namespace App\Http\Controllers\api\SuperAdmin\subscriper;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Payment;
use App\Models\PaymentMethod;
use App\Models\Village;
use App\Models\Provider;
use App\Models\Package;
use App\Models\ServiceType;

class SubscriperController extends Controller
{
    public function __construct(private Payment $payments,
    private PaymentMethod $payment_methods, private Village $village,
    private Package $packages, private ServiceType $service_types
    , private Provider $provider){}

    public function view(){
        $payment_methods = $this->payment_methods
        ->where('status', 1)
        ->get();
        $villages = $this->village
        ->where('status', 1)
        ->get();
        $providers = $this->provider
        ->where('status', 1)
        ->get();
        $subscribers = $this->payments
        ->where('expire_date', '>=', date('Y-m-d'))
        ->where('status', 'approved')
        ->get()
        ->map(function($item){
            return [       
                'subscriber' => $item?->village?->name ?? $item?->provider?->name,
                'type' => $item?->package?->type,
                'start_date' => $item?->start_date,
                'expire_date' => $item?->expire_date,
                'payment_method' => $item?->payment_method?->name,
                'service' => $item?->service?->name,
            ];
        });

        return response()->json([
            'payment_methods' => $payment_methods,
            'villages' => $villages,
            'providers' => $providers,
            'subscribers' => $subscribers,
        ]);
    }

    public function filter(){
        
    }

    public function create(){
        
    }

    public function modify(){
        
    }

    public function delete(){
        
    }
}
