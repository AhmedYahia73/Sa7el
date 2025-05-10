<?php

namespace App\Http\Controllers\api\SuperAdmin\subscriper;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\SuperAdmin\SubscriperRequest;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

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
        $services = $this->service_types
        ->where('status', 1)
        ->get();
        $provider_packages = $this->packages
        ->where('type', 'provider')
        ->get();
        $village_packages = $this->packages
        ->where('type', 'village')
        ->get();
        $subscribers = $this->payments
        ->where('expire_date', '>=', date('Y-m-d'))
        ->where('status', 'approved')
        ->get()
        ->map(function($item){
            return [
                'id' => $item->id,
                'subscriber' => $item?->village?->name ?? $item?->provider?->name,
                'type' => $item?->package?->type,
                'start_date' => $item?->start_date,
                'expire_date' => $item?->expire_date,
                'payment_method' => $item?->payment_method?->name,
                'service' => $item?->service?->name,
                'package' => $item?->package?->name,
            ];
        });
        $subscribers_village = $subscribers->where('type', 'village')->values();
        $subscribers_provider = $subscribers->where('type', 'provider')->values();

        return response()->json([
            'payment_methods' => $payment_methods,
            'villages' => $villages,
            'providers' => $providers,
            'services' => $services,
            'subscribers' => $subscribers,
            'subscribers_village' => $subscribers_village,
            'subscribers_provider' => $subscribers_provider,
            'provider_packages' => $provider_packages,
            'village_packages' => $village_packages,
        ]);
    }

    public function filter(){
        $subscriber = $this->payments
        ->where('expire_date', '>=', date('Y-m-d'))
        ->where('status', 'approved')
        ->with('village', 'provider', 'package', 'payment_method', 'service')
        ->first();
        $subscriber->type = $subscriber?->package?->type;

        return response()->json([
            'subscriber' => $subscriber
        ]);
    }

    public function create(SubscriperRequest $request){
        $validator = Validator::make($request->all(), [
            'type' => 'required|in:provider,village',
        ]);
        if ($validator->fails()) { // if Validate Make Error Return Message Error
            return response()->json([
                'error' => $validator->errors(),
            ],400);
        }
        $user = [];
        if ($request->type == 'provider') {
            $user = $this->provider
            ->where('id', $request->provider_id)
            ->first();
        }
        elseif($request->type == 'village'){
            $user = $this->village
            ->where('id', $request->village_id)
            ->first();
        }
        $package = $this->packages
        ->where('id', $request->package_id)
        ->first();
        $amount = $package->price;
        if (empty($user?->package_id)) {
            $amount += $package->feez;
        }
        $amount -= $package->discount;
        $subscripeRequest = $request->validated();
        $subscripeRequest['discount'] = $package->discount;
        $subscripeRequest['start_date'] = date('Y-m-d');
        $subscripeRequest['expire_date'] = Carbon::now()->addYear()->format('Y-m-d');
        $subscripeRequest['amount'] = $amount;
        $subscripeRequest['status'] = 'approved';
        $payments = $this->payments
        ->create($subscripeRequest);
        $user->from = $payments->start_date;
        $user->to = $payments->expire_date;
        $user->package_id = $payments->package_id;
        $user->save();

        return response()->json([
            'success' => 'you add subscriber success'
        ]);
    }

    public function modify(SubscriperRequest $request, $id){
        $user = [];
        $old_package = null;
        $subscripeRequest = $request->validated();
        $payments = $this->payments
        ->where('id', $id)
        ->first();
        if ($payments->type == 'provider') {
            $user = $this->provider
            ->where('id', $request->provider_id)
            ->first();
        }
        elseif($payments->type == 'village'){
            $user = $this->village
            ->where('id', $request->village_id)
            ->first();
        }
        $old_package = $this->packages
        ->where('id', $user?->package_id)
        ->first();
        $old_amount = 0;
        if (!empty($old_package)) {
            $old_amount = $old_package->price - $old_package->discount;
        }
        $package = $this->packages
        ->where('id', $request->package_id)
        ->first();
        $amount = $package->price;
        if ($old_amount < $payments->amount) {
            $amount += $package->feez;
        }
        $amount -= $package->discount;
        $subscripeRequest['discount'] = $package->discount;
        $subscripeRequest['amount'] = $amount;
        $subscripeRequest['status'] = 'approved';
        $payments->update($subscripeRequest);
        $user->package_id = $payments->package_id;
        $user->save();

        return response()->json([
            'success' => 'you update subscriber success'
        ]);
    }

    public function delete($id){ 
        $payments = $this->payments
        ->where('id', $id)
        ->first();
        if ($payments->type == 'provider') {
            $user = $this->provider
            ->where('id', $payments->provider_id)
            ->first();
        }
        elseif($payments->type == 'village'){
            $user = $this->village
            ->where('id', $payments->village_id)
            ->first();
        }
        $old_package = $this->packages
        ->where('id', $user?->package_id)
        ->first();
        $old_amount = 0;
        if (!empty($old_package)) {
            $old_amount = $old_package->price - $old_package->discount;
        }
        if ($old_amount < $payments->amount) {
            $user->package_id = null;
            $user->from = null;
            $user->to = null;
        }
        else{
            $user->to = Carbon::now()->subDay()->format('Y-m-d');
        }
        $user->save();

        return response()->json([
            'success' => 'you delete subscriber success'
        ]);
    }
}
