<?php

namespace App\Http\Controllers\api\SuperAdmin\service_type;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\trait\image;

use App\Models\ServiceType;

class ServiceTypeController extends Controller
{
    public function __construct(private ServiceType $service_type){}
    use image;

    public function view(){
        $service_type = $this->service_type
        ->with('translations')
        ->get();

        return response()->json([
            'service_types' => $service_type,
        ]);
    }

    public function service_type($id){
        $service_type = $this->service_type
        ->where('id', $id)
        ->first();

        return response()->json([
            'service_type' => $service_type,
        ]);
    }

    public function status(Request $request, $id){
        $validator = Validator::make($request->all(), [
            'status' => 'required|boolean',
        ]);
        if ($validator->fails()) { // if Validate Make Error Return Message Error
            return response()->json([
                'error' => $validator->errors(),
            ],400);
        }
        
        $service_type = $this->service_type
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
                'error' => $validator->errors(),
            ],400);
        }
        $serviceRequest = $validator->validated();
        if (!is_string($request->image)) {
            $image_path = $this->upload($request, 'image', 'images/service_type');
            $serviceRequest['image'] = $image_path;
        }
        $service = $this->service_type
        ->create($serviceRequest);
        $service_translations = [[ 
            'locale' => 'en',
            'key' => 'name',
            'value' => $request->name,
        ]];
        if (!empty($request->ar_name)) {
            $service_translations[] = [ 
                'locale' => 'ar',
                'key' => 'name',
                'value' => $request->ar_name,
            ];
        }
        $service->translations()->createMany($service_translations);

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
                'error' => $validator->errors(),
            ],400);
        }
        $serviceRequest = $validator->validated();
        $service = $this->service_type
        ->where('id', $id)
        ->first();
        if (empty($service)) {
            return response()->json([
                'errors' => 'service not found'
            ], 400);
        }
        if (!is_string($request->image)) {
            $image_path = $this->update_image($request, $service->image, 'image', 'images/service_type');
            $serviceRequest['image'] = $image_path;
        }
        $service
        ->update($serviceRequest);
        $service_translations = [[ 
            'locale' => 'en',
            'key' => 'name',
            'value' => $request->name,
        ]];
        if (!empty($request->ar_name)) {
            $service_translations[] = [ 
                'locale' => 'ar',
                'key' => 'name',
                'value' => $request->ar_name,
            ];
        }
        $service->translations()->delete();
        $service->translations()->createMany($service_translations);

        return response()->json([
            'success' => 'You update data success'
        ]);
    }

    public function delete($id){
        $service = $this->service_type
        ->where('id', $id)
        ->first();
        if (empty($service)) {
            return response()->json([
                'errors' => 'service not found'
            ], 400);
        }
        $service->translations()->delete();
        $this->deleteImage($service->image);
        $service->delete();

        return response()->json([
            'success' => 'You delete data success'
        ]);
    }
}
