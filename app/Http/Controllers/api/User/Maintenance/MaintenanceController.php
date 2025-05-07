<?php

namespace App\Http\Controllers\api\User\Maintenance;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\trait\image;

use App\Models\Maintenance;
use App\Models\MaintenanceType;

class MaintenanceController extends Controller
{
    public function __construct(private Maintenance $maintenance,
    private MaintenanceType $maintenance_type){}

    public function maintenance_request(Request $request){
        $validator = Validator::make($request->all(), [
            'appartment_id' => 'required|exists:appartments,id',
            'maintenance_type_id' => 'required|exists:maintenance_types,id',
            'description' => 'nullable',
            'image' => 'nullable',
            'status' => 'required|boolean',
        ]);
        if ($validator->fails()) { // if Validate Make Error Return Message Error
            return response()->json([
                'error' => $validator->errors(),
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
