<?php

namespace App\Http\Controllers\api\SuperAdmin\ServiceProvider;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\SuperAdmin\ServiceProviderRequest;
use Illuminate\Support\Facades\Validator;
use App\trait\image;

use App\Models\ServiceProvider;
use App\Models\MaintenanceType;
use App\Models\Village;

class ServiceProviderController extends Controller
{
    public function __construct(private ServiceProvider $provider,
    private MaintenanceType $maintenance_types, private Village $villages){}
    use image;

    public function view(){
        $provider = $this->provider
        ->with(['translations', 'maintenance', 'package'])
        ->get();
        $maintenance_types = $this->maintenance_types
        ->get();
        $villages = $this->villages
        ->where('status', 1)
        ->get();

        return response()->json([
            'providers' => $provider,
            'maintenance_types' => $maintenance_types,
            'villages' => $villages,
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
        
        $provider = $this->provider
        ->where('id', $id)
        ->update([
            'status' => $request->status
        ]);

        return response()->json([
            'success' => $request->status ? 'active' : 'banned'
        ]);
    }

    public function create(ServiceProviderRequest $request){
        // service_id, name, description, phone, status, location, village_id
        // ar_name, ar_description, image, open_from, open_to
        $ServiceProviderRequest = $request->validated();
        if (!is_string($request->image)) {
            $image_path = $this->upload($request, 'image', 'images/service_providers');
            $ServiceProviderRequest['image'] = $image_path;
        }
        $provider = $this->provider
        ->create($ServiceProviderRequest);
        $provider_translations = [[ 
            'locale' => 'en',
            'key' => 'name',
            'value' => $request->name,
        ]];
        if (!empty($request->ar_name)) {
            $provider_translations[] = [ 
                'locale' => 'ar',
                'key' => 'name',
                'value' => $request->ar_name,
            ];
        }
        if (!empty($request->description)) {
            $provider_translations[] = [ 
                'locale' => 'en',
                'key' => 'description',
                'value' => $request->description,
            ];
        }
        if (!empty($request->ar_description)) {
            $provider_translations[] = [ 
                'locale' => 'ar',
                'key' => 'description',
                'value' => $request->ar_description,
            ];
        }
        $provider->translations()->createMany($provider_translations);

        return response()->json([
            'success' => 'You add data success'
        ]);
    }

    public function modify(ServiceProviderRequest $request, $id){
        // service_id, name, description, phone, status, location, village_id
        // ar_name, ar_description, image
        $ServiceProviderRequest = $request->validated();
        $provider = $this->provider
        ->where('id', $id)
        ->first();
        if (empty($provider)) {
            return response()->json([
                'errors' => 'provider not found'
            ], 400);
        }
        if (!is_string($request->image)) {
            $image_path = $this->update_image($request, $provider->image, 'image', 'images/service_providers');
            $ServiceProviderRequest['image'] = $image_path;
        }
        $provider
        ->update($ServiceProviderRequest);
        $provider_translations = [[ 
            'locale' => 'en',
            'key' => 'name',
            'value' => $request->name,
        ]];
        if (!empty($request->ar_name)) {
            $provider_translations[] = [ 
                'locale' => 'ar',
                'key' => 'name',
                'value' => $request->ar_name,
            ];
        }
        if (!empty($request->description)) {
            $provider_translations[] = [ 
                'locale' => 'en',
                'key' => 'description',
                'value' => $request->description,
            ];
        }
        if (!empty($request->ar_description)) {
            $provider_translations[] = [ 
                'locale' => 'ar',
                'key' => 'description',
                'value' => $request->ar_description,
            ];
        }
        $provider->translations()->delete();
        $provider->translations()->createMany($provider_translations);

        return response()->json([
            'success' => 'You update data success'
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
        $provider->translations()->delete();
        $this->deleteImage($provider->image);
        $provider->delete();

        return response()->json([
            'success' => 'You delete data success'
        ]);
    }
}
