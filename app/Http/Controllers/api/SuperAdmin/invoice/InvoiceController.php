<?php

namespace App\Http\Controllers\api\SuperAdmin\invoice;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

use App\Models\Package;
use App\Models\Village;
use App\Models\Payment;
use App\Models\ServiceProvider;
use App\Models\Provider;

class InvoiceController extends Controller
{
    public function __construct(private Village $village,
    private Payment $payment, private Package $package,
    private ServiceProvider $maintenance_provider, 
    private Provider $provider){}

    public function invoice_village(Request $request, $id){
        $package = null; 
        $village = $this->village
        ->where('id', $id)
        ->with(['zone:id,name'])
        ->first();
        $date = Carbon::now()->addMonth();
        
        if ($village->to <= $date) {
            $package = $this->package
            ->where('id', $village->package_id)
            ->first();
        }
        $new_invoices = $this->payment
        ->where('village_id', $id)
        ->where('status', 'approved')
        ->get()
        ->map(function($item){
            return [
                'name' => $item?->package?->name,
                'description' => $item?->package?->description,
                'amount' => $item->amount,
                'discount' => $item->discount,
                'total_before_discount' => $item->amount + $item->discount,
                'status' => 'paid'
            ];
        });
        if (!empty($package)) {
            $new_invoices->push([
                'name' => $package?->name,
                'description' => $package?->description,
                'amount' => empty($village->package_id) ? $package->price + $package->feez - $package->discount : $package->price - $package->discount,
                'discount' => $package->discount,
                'total_before_discount' => empty($village->package_id) ? $package->price + $package->feez + $package->discount : $package->price + $package->discount,
                'status' => 'unpaid'
            ]);
        }

        return response()->json([ 
            'village' => $village,
            'invoices' => $new_invoices,
        ]);
    }

    public function invoice_provider(Request $request, $id){
        $package = null; 
        $provider = $this->provider
        ->where('id', $id)
        ->with(['zone:id,name'])
        ->first();
        $date = Carbon::now()->addMonth();
        
        if ($provider->to <= $date) {
            $package = $this->package
            ->where('id', $provider->package_id)
            ->first();
        }
        $new_invoices = $this->payment
        ->where('provider_id', $id)
        ->where('status', 'approved')
        ->get()
        ->map(function($item){
            return [
                'name' => $item?->package?->name,
                'description' => $item?->package?->description,
                'amount' => $item->amount,
                'discount' => $item->discount,
                'total_before_discount' => $item->amount + $item->discount,
                'status' => 'paid'
            ];
        });
        if (!empty($package)) {
            $new_invoices->push([
                'name' => $package?->name,
                'description' => $package?->description,
                'amount' => empty($provider->package_id) ? $package->price + $package->feez - $package->discount : $package->price - $package->discount,
                'discount' => $package->discount,
                'total_before_discount' => empty($provider->package_id) ? $package->price + $package->feez + $package->discount : $package->price + $package->discount,
                'status' => 'unpaid'
            ]);
        }

        return response()->json([ 
            'provider' => $provider,
            'invoices' => $new_invoices,
        ]);
    }

    // public function invoice_village(Request $request, $id){
    //     $package = null; 
    //     $village = $this->village
    //     ->where('id', $id)
    //     ->with(['zone:id,name'])
    //     ->first();
    //     $date = Carbon::now()->addMonth();
        
    //     if ($village->to <= $date) {
    //         $package = $this->package
    //         ->where('id', $village->package_id)
    //         ->first();
    //     }
    //     $new_invoices = $this->payment
    //     ->where('village_id', $id)
    //     ->where('status', 'approved')
    //     ->get()
    //     ->map(function($item){
    //         return [
    //             'name' => $item?->package?->name,
    //             'description' => $item?->package?->description,
    //             'amount' => $item->amount,
    //             'discount' => $item->discount,
    //             'total_before_discount' => $item->amount + $item->discount,
    //             'status' => 'paid'
    //         ];
    //     });
    //     if (!empty($package)) {
    //         $new_invoices->push([
    //             'name' => $package?->name,
    //             'description' => $package?->description,
    //             'amount' => empty($village->package_id) ? $package->price + $package->feez - $package->discount : $package->price - $package->discount,
    //             'discount' => $package->discount,
    //             'total_before_discount' => empty($village->package_id) ? $package->price + $package->feez + $package->discount : $package->price + $package->discount,
    //             'status' => 'unpaid'
    //         ]);
    //     }

    //     return response()->json([ 
    //         'village' => $village,
    //         'invoices' => $new_invoices,
    //     ]);
    // }

    // public function view(){ 
    //     $allow_time = $this->settings
    //     ->where('name', 'allow_time')
    //     ->first();
    //     if (empty($allow_time)) {
    //         $data = [
    //             'days' => 0,
    //             'fine' => 0
    //         ];
    //         $data = json_encode($data);
    //         $allow_time = $this->settings
    //         ->create([
    //             'name' => 'allow_time',
    //             'value' => $data,
    //         ]);
    //     }
    //     $allow_time = json_decode($allow_time->value);

    //     return response()->json([
    //         'allow_time' => $allow_time
    //     ]);
    // }

    // public function modify(Request $request){
    //     // days, fine
    //     $validator = Validator::make($request->all(), [
    //         'days' => 'required|numeric',
    //         'fine' => 'required|numeric',
    //     ]);
    //     if ($validator->fails()) { // if Validate Make Error Return Message Error
    //         return response()->json([
    //             'errors' => $validator->errors(),
    //         ],400);
    //     }
    //     $allow_time = $this->settings
    //     ->where('name', 'allow_time')
    //     ->first();
    //     $data = [
    //         'days' => $request->days,
    //         'fine' => $request->fine,
    //     ];
    //     $data = json_encode($data);
    //     if (empty($allow_time)) {
    //         $allow_time = $this->settings
    //         ->create([
    //             'name' => 'allow_time',
    //             'value' => $data,
    //         ]);
    //     }
    //     else{
    //         $allow_time->update([
    //             'name' => 'allow_time',
    //             'value' => $data,
    //         ]);
    //     }
    //     $allow_time = json_decode($allow_time->value);

    //     return response()->json([
    //         'allow_time' => $allow_time
    //     ]);
    // }
}
