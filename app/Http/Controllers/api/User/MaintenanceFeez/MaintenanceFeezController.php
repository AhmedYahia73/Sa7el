<?php

namespace App\Http\Controllers\api\User\MaintenanceFeez;

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
            ->where('appartment_id', $request->appartment_id);

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
            'total' =>$maintenance_fees->sum('total'),
            'paid' =>$maintenance_fees->sum('paid'),
            'remain' =>$maintenance_fees->sum('remain'),
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
            ->where('appartment_id', $request->appartment_id);
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
            'total' =>$maintenance_fees->sum('total'),
            'paid' =>$maintenance_fees->sum('paid'),
            'remain' =>$maintenance_fees->sum('remain'),
        ]);
    }

}
