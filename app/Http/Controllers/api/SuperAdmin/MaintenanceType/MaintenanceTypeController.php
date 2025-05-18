<?php

namespace App\Http\Controllers\api\SuperAdmin\MaintenanceType;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\trait\image;

use App\Models\MaintenanceType;

class MaintenanceTypeController extends Controller
{
    public function __construct(private MaintenanceType $maintenance_type){}
    use image;

    public function view(){
        $maintenance_type = $this->maintenance_type
        ->with('translations')
        ->get();

        return response()->json([
            'maintenance_types' => $maintenance_type,
        ]);
    }

    public function status(Request $request, $id){
        $validator = Validator::make($request->all(), [
            'status' => 'required|boolean',
        ]);
        if ($validator->fails()) { // if Validate Make Error Return Message Error
            return response()->json([
                'errors' => $validator->errors(),
            ],400);
        }
        
        $maintenance_type = $this->maintenance_type
        ->where('id', $id)
        ->update([
            'status' => $request->status
        ]);

        return response()->json([
            'success' => $request->status ? 'active' : 'banned'
        ]);
    }

    public function create(Request $request){
        // name, image, status,
        // ar_name
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'image' => 'required',
            'status' => 'required|boolean',
        ]);
        if ($validator->fails()) { // if Validate Make Error Return Message Error
            return response()->json([
                'errors' => $validator->errors(),
            ],400);
        }
        $maintenanceRequest = $validator->validated();
        if (!is_string($request->image)) {
            $image_path = $this->upload($request, 'image', 'images/maintenance_type');
            $maintenanceRequest['image'] = $image_path;
        }
        $maintenance = $this->maintenance_type
        ->create($maintenanceRequest);
        $maintenance_translations = [[ 
            'locale' => 'en',
            'key' => 'name',
            'value' => $request->name,
        ]];
        if (!empty($request->ar_name)) {
            $maintenance_translations[] = [ 
                'locale' => 'ar',
                'key' => 'name',
                'value' => $request->ar_name,
            ];
        }
        $maintenance->translations()->createMany($maintenance_translations);

        return response()->json([
            'success' => 'You add data success'
        ]);
    }

    public function modify(Request $request, $id){
        // name, image, status,
        // ar_name
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'image' => 'required',
            'status' => 'required|boolean',
        ]);
        if ($validator->fails()) { // if Validate Make Error Return Message Error
            return response()->json([
                'errors' => $validator->errors(),
            ],400);
        }
        $maintenanceRequest = $validator->validated();
        $maintenance = $this->maintenance_type
        ->where('id', $id)
        ->first();
        if (empty($maintenance)) {
            return response()->json([
                'errors' => 'maintenance not found'
            ], 400);
        }
        if (!is_string($request->image)) {
            $image_path = $this->update_image($request, $maintenance->image, 'image', 'images/maintenance_type');
            $maintenanceRequest['image'] = $image_path;
        }
        $maintenance
        ->update($maintenanceRequest);
        $maintenance_translations = [[ 
            'locale' => 'en',
            'key' => 'name',
            'value' => $request->name,
        ]];
        if (!empty($request->ar_name)) {
            $maintenance_translations[] = [ 
                'locale' => 'ar',
                'key' => 'name',
                'value' => $request->ar_name,
            ];
        }
        $maintenance->translations()->delete();
        $maintenance->translations()->createMany($maintenance_translations);

        return response()->json([
            'success' => 'You update data success'
        ]);
    }

    public function delete($id){
        $maintenance = $this->maintenance_type
        ->where('id', $id)
        ->first();
        if (empty($maintenance)) {
            return response()->json([
                'errors' => 'maintenance not found'
            ], 400);
        }
        $maintenance->translations()->delete();
        $this->deleteImage($maintenance->image);
        $maintenance->delete();

        return response()->json([
            'success' => 'You delete data success'
        ]);
    }  
}
