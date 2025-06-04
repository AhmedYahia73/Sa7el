<?php

namespace App\Http\Controllers\api\User\MaintenanceFeez;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\trait\image;

use App\Models\MaintenanceFeez;
use App\Models\AppartmentMaintenanceFeez;
use App\Models\PaymentMaintenanceRequest;

class MaintenanceFeezController extends Controller
{
    public function __construct(private MaintenanceFeez $maintenance_fees,
    private AppartmentMaintenanceFeez $appartment_maintenance,
    private PaymentMaintenanceRequest $payment_request){}
    use image;

    public function view(Request $request){
        $validator = Validator::make($request->all(), [
            'village_id' => 'required|exists:villages,id',
            'appartment_id' => 'required|exists:appartments,id',
        ]);
        if ($validator->fails()) { // if Validate Make Error Return Message Error
            return response()->json([
                'errors' => $validator->errors(),
            ],400);
        }
        
        $maintenance_fees = $this->maintenance_fees
        ->where('village_id', $request->village_id)
        ->with(['appartments' => function($query) use($request){
            $query->with('users', 'appartment_unit');
        }])
        ->get()
        ->map(function($item) use($request){
            $total = $item->price;
            $my_appartment = $item->appartments
            ->where('appartment_id', $request->appartment_id)
            ->sortByDesc('id')
            ->first();

            return [
                'id' => $item->id,
                'year' => $item->year,
                'name' => $item->name,
                'total' => $total,
                'paid' => $my_appartment?->paid,
                'remain' => $my_appartment?->remain ?? $total, 
                'status' => $my_appartment?->status,
            ];
        });

        return response()->json([
            'maintenance_fees' =>$maintenance_fees, 
            'fees_unpaid' =>$maintenance_fees->where('status', 'unpaid')->values(), 
            'fees_paid' =>$maintenance_fees->where('status', 'paid')->values(), 
        ]);
    }
    
    public function view_year(Request $request){
        $validator = Validator::make($request->all(), [
            'village_id' => 'required|exists:villages,id',
            'appartment_id' => 'required|exists:appartments,id',
            'year' => 'required|numeric',
        ]);
        if ($validator->fails()) { // if Validate Make Error Return Message Error
            return response()->json([
                'errors' => $validator->errors(),
            ],400);
        }
        
        $maintenance_fees = $this->maintenance_fees
        ->where('village_id', $request->village_id)
        ->where('year', $request->year)
        ->with(['appartments' => function($query) use($request){
            $query->with('users', 'appartment_unit');
        }])
        ->get()
        ->map(function($item) use($request){
            $total = $item->price;
            $my_appartment = $item->appartments
            ->sortByDesc('id')
            ->where('appartment_id', $request->appartment_id)
            ->first();
            return [
                'id' => $item->id,
                'name' => $item->name,
                'total' => $total,
                'paid' => $my_appartment?->paid,
                'remain' => $my_appartment?->remain, 
                'status' => $my_appartment?->status,
            ];
        });

        return response()->json([
            'maintenance_fees' =>$maintenance_fees, 
        ]);
    }

    public function make_payment_request(Request $request){
        $validator = Validator::make($request->all(), [
            'village_id' => 'required|exists:villages,id',
            'maintenance_feez_id' => 'required|exists:maintenance_feezs,id',
            'appartment_id' => 'required|exists:appartments,id',
            'paid' => 'required|numeric',
            'receipt' => 'required',
        ]);
        if ($validator->fails()) { // if Validate Make Error Return Message Error
            return response()->json([
                'errors' => $validator->errors(),
            ],400);
        }
        $paymentRequest = $validator->validated();
        $paymentRequest['user_id'] = $request->user()->id;
        $paymentRequest['status'] = 'pending';
        if ($request->has('receipt')) {
            $image_path =$this->storeBase64Image($request->receipt, '/images/payment_request');
            $paymentRequest['receipt'] = $image_path;
        }
        $this->payment_request
        ->create($paymentRequest);
        
        return response()->json([
            'success' => 'You make request success'
        ]);
    }
}
