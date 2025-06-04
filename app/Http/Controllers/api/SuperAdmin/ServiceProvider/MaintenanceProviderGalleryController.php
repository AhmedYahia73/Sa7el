<?php

namespace App\Http\Controllers\api\SuperAdmin\ServiceProvider;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\trait\image;

use App\Models\MaintenanceProviderGallery;

class MaintenanceProviderGalleryController extends Controller
{
    public function __construct(private MaintenanceProviderGallery $provider_gallary){}
    use image;

    public function view($id){
        $provider_gallary = $this->provider_gallary
        ->where('provider_id', $id)
        ->get();

        return response()->json([
            'provider_gallary' => $provider_gallary
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
        $this->provider_gallary
        ->where('id', $id)
        ->update([
            'status' => $request->status
        ]);

        return response()->json([
            'success' => $request->status ? 'active' : 'banned'
        ]);
    }

    public function create(Request $request, $id){
        $validator = Validator::make($request->all(), [
            'image' => 'required',
            'status' => 'required|boolean',
        ]);
        if ($validator->fails()) { // if Validate Make Error Return Message Error
            return response()->json([
                'errors' => $validator->errors(),
            ],400);
        }

        $image_path = $this->upload($request, 'image', 'images/maintenance_provider_gallary');
        $this->provider_gallary
        ->create([
            'image' => $image_path,
            'status' => $request->status,
            'provider_id' => $id
        ]);

        return response()->json([
            'success' => 'You add image success'
        ]);
    }

    public function delete($id){
        $provider_gallary = $this->provider_gallary
        ->where('id', $id)
        ->first();
        if (empty($provider_gallary)) {
            return response()->json([
                'errors' => 'village not found'
            ], 400);
        }
        $this->deleteImage($provider_gallary->image);
        $provider_gallary->delete();

        return response()->json([
            'success' => 'You delete data success'
        ]);
    }
}
