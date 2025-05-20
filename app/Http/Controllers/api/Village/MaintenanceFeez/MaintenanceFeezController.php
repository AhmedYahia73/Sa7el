<?php

namespace App\Http\Controllers\api\Village\MaintenanceFeez;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

use App\Models\MaintenanceFeez;
use App\Models\AppartmentMaintenanceFeez;

class MaintenanceFeezController extends Controller
{
    public function __construct(private MaintenanceFeez $maintenance_fees,
    private AppartmentMaintenanceFeez $appartment_maintenance){}

    public function view(Request $request){
        $maintenance_fees = $this->maintenance_fees
        ->where('village_id', $request->user()->village_id)
        ->with(['appartments' => function($query){
            $query->with('users', 'appartment_unit');
        }])
        ->get()
        ->map(function($item){
            $total = $item->price * $item->village->units->count();
            $unpaid = $item->village->units->count() - 
            $item->appartments->where('status',  'paid')->count();
            $paid = $item->appartments->sum('paid');
            $users_paid = $item->appartments->where('status', 'paid');
            $users_unpaid = $item->village->units->whereNotIn('id', $users_paid->pluck('appartment_id'));
            $users_unpaid = $users_unpaid->map(function($element) use($item){
                if (count($element?->maintenance) > 0) {
                    $maintenance = $element->maintenance->where('maintenance_id', $item->id)->first();
                    $paid = $maintenance->paid;
                    $total = $item->price;
                } 
                else {
                    $paid = 0;
                    $total = $item->price;
                }
                return [
                    'unit' => $element->unit,
                    'unit_type' => $element?->type?->name,
                    'user_name' => $element?->user?->name,
                    'paid' => $paid,
                    'total' => $total,
                ];
            });
            return [
                'id' => $item->id,
                'name' => $item->name,
                'total' => $total,
                'paid' => $paid,
                'remain' => $total - $paid,
                'unpaid' => $unpaid,
                'users_unpaid' => $users_unpaid,
            ];
        });

        return response()->json([
            'maintenance_fees' =>$maintenance_fees,
            'total' =>$maintenance_fees->sum('total'),
            'paid' =>$maintenance_fees->sum('paid'),
            'remain' =>$maintenance_fees->sum('remain'),
        ]);
    }

    public function view_year(Request $request){
        $validator = Validator::make($request->all(), [
            'year' => 'required|numeric',
        ]);
        if ($validator->fails()) { // if Validate Make Error Return Message Error
            return response()->json([
                'errors' => $validator->errors(),
            ],400);
        }
        
        $maintenance_fees = $this->maintenance_fees
        ->where('village_id', $request->user()->village_id)
        ->where('year', $request->year)
        ->with(['appartments' => function($query){
            $query->with('users', 'appartment_unit');
        }])
        ->get()
        ->map(function($item){
            $total = $item->price * $item->village->units->count();
            $unpaid = $item->village->units->count() - 
            $item->appartments->where('status',  'paid')->count();
            $paid = $item->appartments->sum('paid');
            $users_paid = $item->appartments->where('status', 'paid');
            $users_unpaid = $item->village->units->whereNotIn('id', $users_paid->pluck('appartment_id'));
            $users_unpaid = $users_unpaid->map(function($element) use($item){
                if (count($element?->maintenance) > 0) {
                    $maintenance = $element->maintenance->where('maintenance_id', $item->id)->first();
                    $paid = $maintenance->paid;
                    $total = $item->price;
                } 
                else {
                    $paid = 0;
                    $total = $item->price;
                }
                return [
                    'unit' => $element->unit,
                    'unit_type' => $element?->type?->name,
                    'user_name' => $element?->user?->name,
                    'paid' => $paid,
                    'total' => $total,
                ];
            });
            return [
                'id' => $item->id,
                'name' => $item->name,
                'total' => $total,
                'paid' => $paid,
                'remain' => $total - $paid,
                'unpaid' => $unpaid,
                'users_unpaid' => $users_unpaid,
            ];
        });

        return response()->json([
            'maintenance_fees' =>$maintenance_fees,
            'total' =>$maintenance_fees->sum('total'),
            'paid' =>$maintenance_fees->sum('paid'),
            'remain' =>$maintenance_fees->sum('remain'),
        ]);
    }

    public function maintenanace_users(Request $request, $id){
        $maintenance_fees = $this->maintenance_fees
        ->where('village_id', $request->user()->village_id)
        ->where('id', $id)
        ->with(['appartments' => function($query){
            $query->with('users', 'appartment_unit');
        }])
        ->get()
        ->map(function($item){
            $total = $item->price * $item->village->units->count();
            $unpaid = $item->village->units->count() - 
            $item->appartments->where('status',  'paid')->count();
            $paid = $item->appartments->sum('paid');
            $users_paid = $item->appartments->where('status', 'paid');
            $users_unpaid = $item->village->units->whereNotIn('id', $users_paid->pluck('appartment_id'));
            $users_unpaid = $users_unpaid->map(function($element) use($item){
                if (count($element?->maintenance) > 0) {
                    $maintenance = $element->maintenance->where('maintenance_id', $item->id)->first();
                    $paid = $maintenance->paid;
                    $total = $item->price;
                } 
                else {
                    $paid = 0;
                    $total = $item->price;
                }
                return [
                    'unit' => $element->unit,
                    'unit_type' => $element?->type?->name,
                    'user_name' => $element?->user?->name,
                    'paid' => $paid,
                    'total' => $total,
                ];
            });
            return [
                'id' => $item->id,
                'name' => $item->name,
                'total' => $total,
                'paid' => $paid,
                'remain' => $total - $paid,
                'unpaid' => $unpaid,
                'users_unpaid' => $users_unpaid,
            ];
        });

        return response()->json([
            'users' =>$maintenance_fees[0]->users_unpaid, 
        ]);
    }

    public function add_payment(Request $request){
        $validator = Validator::make($request->all(), [
            'maintenance_feez_id' => 'required|exists:maintenance_feezs,id',
            'appartment_id' => 'required|exists:appartments,id',
            'user_id' => 'required|exists:users,id',
            'paid' => 'required|numeric'
        ]);
        if ($validator->fails()) { // if Validate Make Error Return Message Error
            return response()->json([
                'errors' => $validator->errors(),
            ],400);
        }

        $maintenance_fees = $this->maintenance_fees
        ->where('id', $request->maintenance_feez_id)
        ->first();
        $appartment_maintenance = $this->appartment_maintenance
        ->where('appartment_id', $request->appartment_id)
        ->where('maintenance_id', $request->maintenance_feez_id)
        ->where('user_id', $request->user_id)
        ->first();
        if (empty($appartment_maintenance)) {
            $this->appartment_maintenance
            ->create([
                'appartment_id' => $request->appartment_id,
                'maintenance_id' => $request->maintenance_feez_id,
                'user_id' => $request->user_id,
                'paid' => $request->paid,
                'total' => $maintenance_fees->price,
            ]);
        } 
        else {
            $appartment_maintenance
            ->update([
                'paid' => $request->paid + $appartment_maintenance->paid,
            ]);
        }
        return response()->json([
            'success' => 'You payment success'
        ]);
    }
}
