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
use App\Models\ServiceProvider;

class SubscriperController extends Controller
{
    public function __construct(private Payment $payments,
    private PaymentMethod $payment_methods, private Village $village,
    private Package $packages, private ServiceType $service_types
    , private Provider $provider, private ServiceProvider $maintenance_provider){}

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
        $maintenance_provider = $this->maintenance_provider
        ->where('status', 1)
        ->get();
        $services = $this->service_types
        ->where('status', 1)
        ->get();
        $provider_packages = $this->packages
        ->where('type', 'provider')
        ->get();
        $maintenance_provider_packages = $this->packages
        ->where('type', 'maintenance_provider')
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
                'village' => $item->village,
                'provider' => $item->provider,
                'package' => $item->package,
                'payment_method_item' => $item->payment_method,
                'service_item' => $item->service,
                'maintenance_provider' => $item->maintenance_provider,
            ];
        });
        $subscribers_village = $subscribers->where('type', 'village')->values();
        $subscribers_provider = $subscribers->where('type', 'provider')->values();
        $subscribers_maintenance_provider = $subscribers->where('type', 'maintenance_provider')->values();

        return response()->json([
            'payment_methods' => $payment_methods,
            'villages' => $villages,
            'providers' => $providers,
            'maintenance_provider' => $maintenance_provider,
            'services' => $services,
            'subscribers' => $subscribers,
            'subscribers_village' => $subscribers_village,
            'subscribers_provider' => $subscribers_provider,
            'subscribers_maintenance_provider' => $subscribers_maintenance_provider,
            'provider_packages' => $provider_packages,
            'village_packages' => $village_packages,
            'maintenance_provider_packages' => $maintenance_provider_packages,
            
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
            'type' => 'required|in:provider,village,maintenance_provider',
        ]);
        if ($validator->fails()) { // if Validate Make Error Return Message Error
            return response()->json([
                'errors' => $validator->errors(),
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
        elseif($request->type == 'maintenance_provider'){
            $user = $this->maintenance_provider
            ->where('id', $request->maintenance_provider_id)
            ->first();
        } 
        if ($user->from <= date('Y-m-d') && $user->to >= date('Y-m-d')) {
            return response()->json([
                'errors' => 'Village is subscribed with our'
            ]);
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
        elseif($request->type == 'maintenance_provider'){
            $user = $this->maintenance_provider
            ->where('id', $request->maintenance_provider_id)
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
        $payments->delete();

        return response()->json([
            'success' => 'you delete subscriber success'
        ]);
    }
}
