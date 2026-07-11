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
use App\Models\ProviderWorkHours;

class ProviderController extends Controller
{
    public function __construct(private Provider $provider,
    private ServiceType $services_types, private Village $villages,
    private Zone $zones){}
    use TraitImage;

    public function view(Request $request){
        $validator = Validator::make($request->all(), [
            'search' => 'sometimes', 
        ]);
        if ($validator->fails()) { // if Validate Make Error Return Message Error
            return response()->json([
                'errors' => $validator->errors(),
            ],400);
        }
        $provider = $this->provider
        ->with(['translations', 'service', 'package', 'zone:id,name',
        'super_admin:id,name', 'work_hours', 'zone_village'])
        ->when($request->search, function($q) use ($request) {
            $q->where(function($q) use ($request) {
                $q->where('name', 'like', "%{$request->search}%")
                  ->orWhere('phone', 'like', "%{$request->search}%")
                  ->orWhereHas('zone_village', function($q) use ($request) {
                      $q->whereRaw("JSON_EXTRACT(name, '$.en') LIKE ?", ["%{$request->search}%"])
                        ->orWhereRaw("JSON_EXTRACT(name, '$.ar') LIKE ?", ["%{$request->search}%"]);
                  })
                  ->orWhereHas('zone', function($q) use ($request) {
                      $q->where('name', 'like', "%{$request->search}%");
                  })
                  ->orWhereHas('service', function($q) use ($request) {
                      $q->where('name', 'like', "%{$request->search}%");
                  });
            });
        })
        ->paginate($request->get('per_page', 10)); 

        return response()->json([
            'providers' => $provider, 
        ]);
    }

    public function lists(Request $request){  

        $services_types = $this->services_types
        ->select("id", "name")
        ->where('status', 1)->get();
        $villages = $this->villages
        ->select("id", "name")
        ->where('status', 1)->get();
        $zones = $this->zones
        ->select("id", "name")
        ->where('status', 1)->get();

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
        ->with(['translations', 'service', 'package', 'village', 'work_hours'])
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
        $validator = Validator::make($request->all(), [
            'image'                    => 'required|base64image',
            'work_hours'               => 'sometimes|array',
            'work_hours.*.day'         => 'required_with:work_hours|in:monday,tuesday,wednesday,thursday,friday,saturday,sunday',
            'work_hours.*.from'        => 'nullable|date_format:H:i:s',
            'work_hours.*.to'          => 'nullable|date_format:H:i:s',
            'work_hours.*.is_24_hours' => 'boolean',
            'work_hours.*.is_closed'   => 'boolean',
        ]);
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 400);
        }

        $providerRequest = $request->validated();
        if (!empty($request->image)) {
            $image_path = $this->storeBase64Image($request->image, 'images/providers');
            $providerRequest['image'] = $image_path;
        }
        $providerRequest['admin_id'] = $request->user()->id;
        $provider = $this->provider->create($providerRequest);

        // work_hours
        if ($request->has('work_hours')) {
            foreach ($request->work_hours as $item) {
                ProviderWorkHours::create([
                    'provider_id' => $provider->id,
                    'day'         => $item['day'],
                    'from'        => ($item['is_24_hours'] ?? false) ? null : ($item['from'] ?? null),
                    'to'          => ($item['is_24_hours'] ?? false) ? null : ($item['to'] ?? null),
                    'is_24_hours' => $item['is_24_hours'] ?? false,
                    'is_closed'   => $item['is_closed'] ?? false,
                ]);
            }
        }

        $provider_translations = [['locale' => 'en', 'key' => 'name', 'value' => $request->name]];
        if (!empty($request->ar_name)) {
            $provider_translations[] = ['locale' => 'ar', 'key' => 'name', 'value' => $request->ar_name];
        }
        if (!empty($request->description)) {
            $provider_translations[] = ['locale' => 'en', 'key' => 'description', 'value' => $request->description];
        }
        if (!empty($request->ar_description)) {
            $provider_translations[] = ['locale' => 'ar', 'key' => 'description', 'value' => $request->ar_description];
        }
        $provider->translations()->createMany($provider_translations);

        return response()->json(['success' => 'You add data success']);
    }

    public function modify(ProviderRequest $request, $id){
        $validator = Validator::make($request->all(), [
            'image'                    => 'nullable|base64image',
            'work_hours'               => 'sometimes|array',
            'work_hours.*.day'         => 'required_with:work_hours|in:monday,tuesday,wednesday,thursday,friday,saturday,sunday',
            'work_hours.*.from'        => 'nullable|date_format:H:i:s',
            'work_hours.*.to'          => 'nullable|date_format:H:i:s',
            'work_hours.*.is_24_hours' => 'boolean',
            'work_hours.*.is_closed'   => 'boolean',
        ]);
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 400);
        }

        $providerRequest = $request->validated();
        $provider = $this->provider->where('id', $id)->first();

        if (empty($provider)) {
            return response()->json(['errors' => 'provider not found'], 400);
        }

        if (!empty($request->image)) {
            $image_path = $this->storeBase64Image($request->image, 'images/providers');
            $this->deleteImage($provider->image);
            $providerRequest['image'] = $image_path;
        }
        $provider->update($providerRequest);

        // work_hours
        if ($request->has('work_hours')) {
            foreach ($request->work_hours as $item) {
                ProviderWorkHours::updateOrCreate(
                    ['provider_id' => $provider->id, 'day' => $item['day']],
                    [
                        'from'        => ($item['is_24_hours'] ?? false) ? null : ($item['from'] ?? null),
                        'to'          => ($item['is_24_hours'] ?? false) ? null : ($item['to'] ?? null),
                        'is_24_hours' => $item['is_24_hours'] ?? false,
                        'is_closed'   => $item['is_closed'] ?? false,
                    ]
                );
            }
        }

        $provider_translations = [['locale' => 'en', 'key' => 'name', 'value' => $request->name]];
        if (!empty($request->ar_name)) {
            $provider_translations[] = ['locale' => 'ar', 'key' => 'name', 'value' => $request->ar_name];
        }
        if (!empty($request->description)) {
            $provider_translations[] = ['locale' => 'en', 'key' => 'description', 'value' => $request->description];
        }
        if (!empty($request->ar_description)) {
            $provider_translations[] = ['locale' => 'ar', 'key' => 'description', 'value' => $request->ar_description];
        }
        $provider->translations()->delete();
        $provider->translations()->createMany($provider_translations);

        return response()->json(['success' => 'You update data success']);
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
