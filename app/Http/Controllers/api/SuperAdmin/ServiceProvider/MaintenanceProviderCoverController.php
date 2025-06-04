<?php

namespace App\Http\Controllers\api\SuperAdmin\ServiceProvider;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\trait\image;

use App\Models\ServiceProvider;

class MaintenanceProviderCoverController extends Controller
{
    public function __construct(private ServiceProvider $provider){}
    use image;

    public function view($id){
        $provider = $this->provider
        ->select('cover_image', 'id')
        ->where('id', $id)
        ->first();

        return response()->json([
            'provider' => $provider
        ]);
    } 

    public function create(Request $request, $id){
        $validator = Validator::make($request->all(), [
            'cover_image' => 'required', 
        ]);
        if ($validator->fails()) { // if Validate Make Error Return Message Error
            return response()->json([
                'errors' => $validator->errors(),
            ],400);
        }

        $provider = $this->provider
        ->where('id', $id)
        ->first();
        if (empty($provider->cover_image)) {
            $image_path = $this->upload($request, 'cover_image', 'images/maintenance_provider_cover_image');
            $provider->update([
                'cover_image' => $image_path
            ]);
        } 
        else {
            $image_path = $this->update_image($request, $provider->cover_image ,'cover_image', 'images/maintenance_provider_cover_image');
            $provider->update([
                'cover_image' => $image_path
            ]);
        }
        

        return response()->json([
            'success' => 'You add image success'
        ]);
    }

    public function delete($id){
        $provider = $this->provider
        ->where('id', $id)
        ->first();
        if (empty($provider)) {
            return response()->json([
                'errors' => 'provider not found'
            ], 400);
        }
        $this->deleteImage($provider->cover_image);
        $provider->update([
            'cover_image' => null
        ]);

        return response()->json([
            'success' => 'You delete data success'
        ]);
    }
}
