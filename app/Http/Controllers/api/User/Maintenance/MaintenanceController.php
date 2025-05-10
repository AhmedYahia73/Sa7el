<?php

namespace App\Http\Controllers\api\User\Maintenance;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\trait\image;

use App\Models\Maintenance;
use App\Models\MaintenanceType;
use App\Models\AppartmentCode;

class MaintenanceController extends Controller
{
    public function __construct(private Maintenance $maintenance,
    private MaintenanceType $maintenance_type, private AppartmentCode $appartment_code){}
    use image;
    
    public function maintenance_lists(Request $request){
        $validator = Validator::make($request->all(), [
            'local' => 'required|in:en,ar',
            'village_id' => 'required|exists:villages,id',
        ]);
        if ($validator->fails()) { // if Validate Make Error Return Message Error
            $firstError = $validator->errors()->first();
            return response()->json([
                'errors' => $firstError,
            ],400);
        }
        $appartment = $this->appartment_code
        ->with(['appartment' => function($query){
            $query->with('type', 'village');
        }])
        ->where('type', 'owner')
        ->orWhere('type', 'renter')
        ->where('from', '<=', date('Y-m-d'))
        ->where('to', '>=', date('Y-m-d'))
        ->get()
        ->pluck('appartment')
        ->map(function($item) use($request){
            return [
                'id' => $item->id,
                'unit' => $item->unit,
                'image' => $item->image_link,
                'village' => $item->village->name,
                'number_floors' => $item->number_floors,
                'type' => $request->local == 'en' ? $item?->type?->name : 
                $item?->type?->ar_name ?? $item?->type?->name,
            ];
        });

        $maintenance_type = $this->maintenance_type
        ->where('status', 1)
        ->whereHas('village', function($query) use($request) {
            return $query->where('villages.id', $request->village_id);
        })
        ->get()
        ->map(function($item) use($request){
            return [
                'id' => $item->id,
                'name' => $request->local == 'en' ? $item->name : $item->ar_name ?? $item->name
            ];
        });

        return response()->json([
            'appartment' => $appartment,
            'maintenance_type' => $maintenance_type,
        ]);
    }
    
    public function maintenance_request(Request $request){
        $validator = Validator::make($request->all(), [
            'appartment_id' => 'required|exists:appartments,id',
            'maintenance_type_id' => 'required|exists:maintenance_types,id',
            'description' => 'nullable',
            'image' => 'nullable',
            'status' => 'required|boolean',
            'village_id' => 'required|exists:villages,id',
        ]);
        if ($validator->fails()) { // if Validate Make Error Return Message Error
            $firstError = $validator->errors()->first();
            return response()->json([
                'errors' => $firstError,
            ],400);
        }

        $maintenanceRequest = $validator->validated();
        if ($request->has('image')) {
            $image_path =$this->upload($request, 'image', '/images/maintenance_request');
            $maintenanceRequest['image'] = $image_path;
        }
        $maintenanceRequest['user_id'] = $request->user()->id;
        $this->maintenance
        ->create($maintenanceRequest);

        return response()->json([
            'success' => 'You add data success'
        ]);
    }
}
