<?php

namespace App\Http\Controllers\api\MaintenanceProvider\Gallery;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\trait\TraitImage;

use App\Models\MaintenanceProviderGallery;
use App\Models\ServiceProvider;

class GalleryController extends Controller
{
    public function __construct(private MaintenanceProviderGallery $provider_gallary,
    private ServiceProvider $provider){}
    use TraitImage;

    public function view(Request $request){
        $provider_gallary = $this->provider_gallary
        ->where('provider_id', $request->user()->maintenance_provider_id)
        ->get();
        $provider = $this->provider
        ->where('id', $request->user()->maintenance_provider_id)
        ->first();

        return response()->json([
            'provider_gallary' => $provider_gallary,
            'provider' => $provider,
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

    public function create(Request $request){
        $validator = Validator::make($request->all(), [
            'image' => 'required',
            'status' => 'required|boolean',
        ]);
        if ($validator->fails()) { // if Validate Make Error Return Message Error
            return response()->json([
                'errors' => $validator->errors(),
            ],400);
        }

        $image_path = $this->upload($request, 'image', 'provider/images/provider_gallary');
        $this->provider_gallary
        ->create([
            'image' => $image_path,
            'status' => $request->status,
            'provider_id' => $request->user()->maintenance_provider_id,
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
                'errors' => 'provider not found'
            ], 400);
        }
        $this->deleteImage($provider_gallary->image);
        $provider_gallary->delete();

        return response()->json([
            'success' => 'You delete data success'
        ]);
    }
}
