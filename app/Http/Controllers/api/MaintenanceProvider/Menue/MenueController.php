<?php

namespace App\Http\Controllers\api\MaintenanceProvider\Menue;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\trait\TraitImage;

use App\Models\MProviderMenue;
use App\Models\ServiceProvider;

class MenueController extends Controller
{
    public function __construct(private MProviderMenue $provider_menue,
    private ServiceProvider $provider){}
    use TraitImage;

    public function view(Request $request){
        $provider_menue = $this->provider_menue
        ->where('m_provider_id', $request->user()->maintenance_provider_id)
        ->get();
        $provider = $this->provider
        ->where('id', $request->user()->maintenance_provider_id)
        ->first();

        return response()->json([
            'provider_menue' => $provider_menue,
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
        $this->provider_menue
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

        $image_path = $this->upload($request, 'image', 'maintenance_provider/images/provider_menue');
        $this->provider_menue
        ->create([
            'image' => $image_path,
            'status' => $request->status,
            'm_provider_id' => $request->user()->maintenance_provider_id,
        ]);

        return response()->json([
            'success' => 'You add image success'
        ]);
    }

    public function delete($id){
        $provider_menue = $this->provider_menue
        ->where('id', $id)
        ->first();
        if (empty($provider_menue)) {
            return response()->json([
                'errors' => 'provider not found'
            ], 400);
        }
        $this->deleteImage($provider_menue->image);
        $provider_menue->delete();

        return response()->json([
            'success' => 'You delete data success'
        ]);
    }
}
