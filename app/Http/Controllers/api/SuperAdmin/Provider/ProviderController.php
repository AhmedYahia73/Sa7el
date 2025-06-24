<?php

namespace App\Http\Controllers\api\SuperAdmin\Provider;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\SuperAdmin\ProviderRequest;
use Illuminate\Support\Facades\Validator;
use App\trait\TraitImage;

use App\Models\Provider;
use App\Models\ServiceType;
use App\Models\Village;
use App\Models\Zone;

class ProviderController extends Controller
{
    public function __construct(private Provider $provider,
    private ServiceType $services_types, private Village $villages,
    private Zone $zones){}
    use TraitImage;

    public function view(){
        $provider = $this->provider
        ->with(['translations', 'service', 'package', 'zone',
        'super_admin:id,name'])
        ->get();
        $services_types = $this->services_types
        ->where('status', 1)
        ->get();
        $villages = $this->villages
        ->where('status', 1)
        ->get();
        $zones = $this->zones
        ->where('status', 1)
        ->get();

        return response()->json([
            'providers' => $provider,
            'services_types' => $services_types,
            'villages' => $villages,
            'zones' => $zones,
        ]);
    }

    public function update_profile_image(Request $request, $id){
        $validator = Validator::make($request->all(), [
            'image' => 'required', 
        ]);
        if ($validator->fails()) { // if Validate Make Error Return Message Error
            return response()->json([
                'errors' => $validator->errors(),
            ],400);
        }

        $provider = $this->provider
        ->where('id', $id)
        ->first();
        if (empty($provider->image)) {
            $image_path = $this->upload($request, 'image', 'images/provider_image');
            $provider->update([
                'image' => $image_path
            ]);
        } 
        else {
            $image_path = $this->update_image($request, $provider->image ,'image', 'images/provider_image');
            $provider->update([
                'image' => $image_path
            ]);
        }
        

        return response()->json([
            'success' => 'You add image success'
        ]);
    }

    public function provider($id){
        $provider = $this->provider
        ->with(['translations', 'service', 'package', 'village'])
        ->where('id', $id)
        ->first();

        return response()->json([
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
        
        $provider = $this->provider
        ->where('id', $id)
        ->update([
            'status' => $request->status
        ]);

        return response()->json([
            'success' => $request->status ? 'active' : 'banned'
        ]);
    }

    public function create(ProviderRequest $request){
        // service_id, name, description, phone, status, location, village_id
        // ar_name, ar_description, image, open_from, open_to, zone_id, location_map
        $validator = Validator::make($request->all(), [
            'image' => 'required', 
        ]);
        if ($validator->fails()) { // if Validate Make Error Return Message Error
            return response()->json([
                'errors' => $validator->errors(),
            ],400);
        }
        $providerRequest = $request->validated();
        if (!empty($request->image)) {
            $image_path = $this->storeBase64Image($request->image, 'images/providers');
            $providerRequest['image'] = $image_path;
        }
        $providerRequest['admin_id'] = $request->user()->id;
        $provider = $this->provider
        ->create($providerRequest);
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

    public function modify(ProviderRequest $request, $id){
        // service_id, name, description, phone, status, location, village_id
        // ar_name, ar_description, image, zone_id, location_map
        $providerRequest = $request->validated();
        $provider = $this->provider
        ->where('id', $id)
        ->first();
        if (empty($provider)) {
            return response()->json([
                'errors' => 'provider not found'
            ], 400);
        }
        if (!empty($request->image)) {
            $image_path = $this->storeBase64Image($request->image, 'images/providers');
            $this->deleteImage($provider->image);
            $providerRequest['image'] = $image_path;
        }
        $provider
        ->update($providerRequest);
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
