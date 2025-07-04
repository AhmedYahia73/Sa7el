<?php

namespace App\Http\Controllers\api\MaintenanceProvider\Invoice;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Carbon\Carbon;

use App\Models\Package;
use App\Models\Payment;
use App\Models\ServiceProvider;

class InvoicesController extends Controller
{
    public function __construct(private Payment $payment, 
    private Package $package,
    private ServiceProvider $provider){}

    public function view(Request $request){
        $package = null; 
        $provider = $this->provider
        ->where('id', $request->user()->maintenance_provider_id)
        ->with(['village:id,name', 'maintenance:id,name'])
        ->first();
        $date = Carbon::now()->addMonth();
        
        if ($provider->to <= $date) {
            $package = $this->package
            ->where('id', $provider->package_id)
            ->first();
        }
        $new_invoices = $this->payment
        ->where('maintenance_provider_id', $request->user()->maintenance_provider_id)
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
            'maintenance_provider' => $provider,
            'invoices' => $new_invoices,
        ]);
    }
}
