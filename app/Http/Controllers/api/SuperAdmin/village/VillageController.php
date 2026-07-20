<?php

namespace App\Http\Controllers\api\SuperAdmin\village;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\SuperAdmin\VillageRequest;
use Illuminate\Support\Facades\Validator;
use App\Http\Requests\Village\SecuirtyRequest;
use App\trait\TraitImage;

use App\Models\Zone;
use App\Models\Village;
use App\Models\Appartment;
use App\Models\SecurityMan;
use App\Models\ZoneVillage;
use App\Models\InsideGate;
use App\Models\Gate;
use App\Models\Beach;
use App\Models\Pools;

class VillageController extends Controller
{
    public function __construct(private Village $village
    , private Zone $zones, private Appartment $appartment, private SecurityMan $security){}
    use TraitImage;

    public function view(){
        $village = $this->village
        ->with(['translations', 'zone'])
        ->withCount('population', 'units', 'providers', 'maintenance_providers')
        ->get();
        $zones = $this->zones
        ->get();

        return response()->json([
            'villages' => $village,
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

        $village = $this->village
        ->where('id', $id)
        ->first();
        if (empty($village->image)) {
            $image_path = $this->upload($request, 'image', 'images/village_image');
            $village->update([
                'image' => $image_path
            ]);
        } 
        else {
            $image_path = $this->update_image($request, $village->image ,'image', 'images/village_image');
            $village->update([
                'image' => $image_path
            ]);
        }
        

        return response()->json([
            'success' => 'You add image success'
        ]);
    }

    public function village_zones($id){
        $zone_village = ZoneVillage::
        where("village_id", $id)
        ->get()
        ->map(function($item){
            return [
                "id" => $item->id,
                "name" => $item->name,
            ];
        });

        return response()->json([
            "zone_village" => $zone_village
        ]);
    }

    public function village($id){
        $village = $this->village
        ->with(['translations', 'zone', 'village_zones'])
        ->withCount('population', 'units')
        ->where('id', $id)
        ->first();

        return response()->json([
            'village' => $village,
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
        
        $village = $this->village
        ->where('id', $id)
        ->update([
            'status' => $request->status
        ]);

        return response()->json([
            'success' => $request->status ? 'active' : 'banned'
        ]);
    }

    public function create(VillageRequest $request){
        // name, description, status, zone_id, location
        // ar_name, ar_description, image, location_map
        // logo
        $validator = Validator::make($request->all(), [
            'image' => 'required|base64image', 
            'logo' => 'required|base64image',
            'zones' => 'array',
            'zones.*.name' => 'required|array',
            'zones.*.name.en' => 'required',
            'zones.*.name.ar' => 'required',
            'zones.*.description' => 'required|array',
            'zones.*.description.en' => 'required',
            'zones.*.description.ar' => 'required',
            'zones.*.lat' => 'required|numeric',
            'zones.*.lng' => 'required|numeric',
        ]);
        if ($validator->fails()) { // if Validate Make Error Return Message Error
            return response()->json([
                'errors' => $validator->errors(),
            ],400);
        }
        $villageRequest = $request->validated();
        if (!empty($request->image)) {
            $image_path = $this->storeBase64Image($request->image, 'images/villages');
            $villageRequest['image'] = $image_path;
        }
        if (!empty($request->logo)) {
            $logo_path = $this->storeBase64Image($request->logo, 'images/villages');
            $villageRequest['logo'] = $logo_path;
        }
        $village = $this->village
        ->create($villageRequest);
        $village_translations = [[ 
            'locale' => 'en',
            'key' => 'name',
            'value' => $request->name,
        ]];
        if (!empty($request->ar_name)) {
            $village_translations[] = [ 
                'locale' => 'ar',
                'key' => 'name',
                'value' => $request->ar_name,
            ];
        }
        if (!empty($request->description)) {
            $village_translations[] = [ 
                'locale' => 'en',
                'key' => 'description',
                'value' => $request->description,
            ];
        }
        if (!empty($request->ar_description)) {
            $village_translations[] = [ 
                'locale' => 'ar',
                'key' => 'description',
                'value' => $request->ar_description,
            ];
        }
        $village->translations()->createMany($village_translations);
        if($request->zones){
            foreach ($request->zones as $item) {
                ZoneVillage::create([
                    'name' => $item['name'],
                    'description' => $item['description'],
                    'lat' => $item['lat'],
                    'lng' => $item['lng'],
                    'village_id' => $village->id,
                ]);
            }
        }

        return response()->json([
            'success' => 'You add data success'
        ]);
    }

    public function modify(VillageRequest $request, $id){
       // name, description, status, zone_id, location
        // ar_name, ar_description, image, location_map
        $validator = Validator::make($request->all(), [
            'image' => 'nullable|base64image',
            "units_num" => ['required', 'numeric'],
            'logo' => 'sometimes|base64image', 
            'deleted_zones' => 'array', 
            'deleted_zones.*' => 'exists:zone_villages,id', 
            'zones' => 'array', 
            'zones.*.name' => 'required|array',
            'zones.*.name.en' => 'required',
            'zones.*.name.ar' => 'required',
            'zones.*.description' => 'required|array',
            'zones.*.description.en' => 'required',
            'zones.*.description.ar' => 'required',
            'zones.*.lat' => 'required|numeric',
            'zones.*.lng' => 'required|numeric',
        ]);
        if ($validator->fails()) { // if Validate Make Error Return Message Error
            return response()->json([
                'errors' => $validator->errors(),
            ],400);
        }
        $villageRequest = $request->validated();
        $villageRequest['units_num'] = $request->units_num;
        $village = $this->village
        ->where('id', $id)
        ->first();
        if (empty($village)) {
            return response()->json([
                'errors' => 'village not found'
            ], 400);
        }
        if (!empty($request->image)) {
            $image_path = $this->storeBase64Image($request->image, 'images/villages');
            $this->deleteImage($village->image); 
            $villageRequest['image'] = $image_path;
        }
        if (!empty($request->logo)) {
            $logo_path = $this->storeBase64Image($request->logo, 'images/villages');
            $this->deleteImage($village->logo); 
            $villageRequest['logo'] = $logo_path;
        }
        $village
        ->update($villageRequest);
        $village_translations = [[ 
            'locale' => 'en',
            'key' => 'name',
            'value' => $request->name,
        ]];
        if (!empty($request->ar_name)) {
            $village_translations[] = [ 
                'locale' => 'ar',
                'key' => 'name',
                'value' => $request->ar_name,
            ];
        }
        if (!empty($request->description)) {
            $village_translations[] = [ 
                'locale' => 'en',
                'key' => 'description',
                'value' => $request->description,
            ];
        }
        if (!empty($request->ar_description)) {
            $village_translations[] = [ 
                'locale' => 'ar',
                'key' => 'description',
                'value' => $request->ar_description,
            ];
        }
        $village->translations()->delete();
        $village->translations()->createMany($village_translations);
        if($request->zones){
            foreach ($request->zones as $item) {
                ZoneVillage::create([
                    'name' => $item['name'],
                    'description' => $item['description'],
                    'lat' => $item['lat'],
                    'lng' => $item['lng'],
                    'village_id' => $village->id,
                ]);
            }
        }
        if($request->deleted_zones){
            ZoneVillage::
            whereIn("id", $request->deleted_zones)
            ->delete();
        }
        return response()->json([
            'success' => 'You update data success'
        ]);
    }

    public function delete($id){
        $village = $this->village
        ->where('id', $id)
        ->first();
        if (empty($village)) {
            return response()->json([
                'errors' => 'village not found'
            ], 400);
        }
        $village->translations()->delete();
        $this->deleteImage($village->image);
        $village->delete();

        return response()->json([
            'success' => 'You delete data success'
        ]);
    }

    public function village_units(Request $request){
        $validator = Validator::make($request->all(), [
            'village_id' => 'required|exists:villages,id',
        ]);
        if ($validator->fails()) { // if Validate Make Error Return Message Error
            return response()->json([
                'errors' => $validator->errors(),
            ],400);
        }
        $units = $this->appartment
        ->where('village_id', $request->village_id)
        ->get()
        ->map(function($item){
            return [
                'id' => $item?->id,
                'name' => $item?->user?->name,
                'phone' => $item?->user?->phone,
                'type_unit' => $item?->type?->name,
                'unit_name' => $item?->unit,
            ];
        });

        return response()->json([
            'units' => $units,
        ]);
    }

    public function village_units_delete(Request $request){
        $validator = Validator::make($request->all(), [
            'appartment_ids' => 'required|array',
            'appartment_ids.*' => 'required|exists:appartments,id',
        ]);
        if ($validator->fails()) { // if Validate Make Error Return Message Error
            return response()->json([
                'errors' => $validator->errors(),
            ],400);
        }
        $units = $this->appartment
        ->whereIn('id', $request->appartment_ids)
        ->delete();

        return response()->json([
            'success' => 'You delete data success',
        ]);
    }

    public function invoice(Request $request, $id){
        
    }

    public function gate_keeper(Request $request, $id){
        $gate_keeper = SecurityMan::
        where("village_id", $id)
        ->get()
        ->map(function($item){
            return [
                "id" => $item->id,
                "name" => $item->name,
                "image" => $item->image_link,
                "email" => $item->email,
                "phone" => $item->phone,
                "status" => $item->status,
                "type" => $item->type,
            ];
        });

        return response()->json([
            "gate_keeper" => $gate_keeper
        ]);
    } 
    
    public function gate_keeper_lists($id){
        $inside_gates = InsideGate::
        where("status", 1)
        ->where("village_id", $id)
        ->get()
        ->map(function($item){
            return [
                "id" => $item->id,
                "name" => $item->name,
                "village_id" => $item->village_id, 
            ];
        });
        $gates = Gate::
        where("status", 1)
        ->where("village_id", $id)
        ->get()
        ->map(function($item){
            return [
                "id" => $item->id,
                "name" => $item->name,
                "village_id" => $item->village_id, 
            ];
        });
        $beaches = Beach::
        where("status", 1)
        ->where("village_id", $id)
        ->get()
        ->map(function($item){
            return [
                "id" => $item->id,
                "name" => $item->name,
                "village_id" => $item->village_id, 
            ];
        });
        $pools = Pools::
        where("status", 1)
        ->where("village_id", $id)
        ->get()
        ->map(function($item){
            return [
                "id" => $item->id,
                "name" => $item->name,
                "village_id" => $item->village_id, 
            ];
        });

        return response()->json([
            "inside_gates" => $inside_gates,
            "gates" => $gates,
            "beaches" => $beaches,
            "pools" => $pools,
        ]);
    }
    
    public function gate_keeper_item($id){
        $gate_keeper = SecurityMan::
        where("id", $id)
        ->with("pool:id,name", "beach:id,name", "gate:id,name", "inside_gates:id,name")
        ->get()
        ->map(function($item){
            return [
                "id" => $item->id,
                "name" => $item->name,
                "image" => $item->image_link,
                "email" => $item->email,
                "phone" => $item->phone,
                "status" => $item->status,
                "type" => $item->type,
                "gate_visitors" => $item->gate_visitors,
                "gate_entrance" => $item->gate_entrance,
                "village_id" => $item->village_id,
                "pool" => $item->pool,
                "beach" => $item->beach,
                "gate" => $item->gate,
                "inside_gates" => $item->inside_gates,
            ];
        });
 
        return response()->json([
            "gate_keeper" => $gate_keeper
        ]);
    }

    public function gate_keeper_status(Request $request, $id){
        $validator = Validator::make($request->all(), [
            'status' => 'required|boolean',
        ]);
        if ($validator->fails()) { // if Validate Make Error Return Message Error
            return response()->json([
                'errors' => $validator->errors(),
            ],400);
        }
        
        $security = $this->security
        ->where('id', $id)
        ->update([
            'status' => $request->status
        ]);

        return response()->json([
            'success' => $request->status ? 'active' : 'banned'
        ]);
    }

    public function gate_keeper_create(SecuirtyRequest $request){
        // name, password, image
        // email, phone, type, status
        $validator = Validator::make($request->all(), [
            'image' => 'required',
            'password' => 'required',
            'email' => 'unique:security_men,email',
            'phone' => 'unique:security_men,phone',
            'pool_ids' => 'array',
            'beach_ids' => 'array',
            'gate_ids' => 'array',
            'inside_gate_ids' => 'array',
            'inside_gate_ids.*' => 'required|exists:inside_gates,id',
            'pool_ids.*' => 'required|exists:pools,id',
            'beach_ids.*' => 'required|exists:beaches,id',
            'gate_ids.*' => 'required|exists:gates,id',
            "gate_visitors" => "required|boolean",
            "gate_entrance" => "required|boolean",
            "village_id" => 'required|exists:villages,id',
        ]);
        if ($validator->fails()) { // if Validate Make Error Return Message Error
            return response()->json([
                'errors' => $validator->errors(),
            ],400);
        }
        $village = Village::
        where("id", $request->village_id)
        ->first();
        $security_num = $village?->package?->security_num ?? 0;
        $security_count = $this->security
        ->where('village_id', $request->village_id)
        ->count();
        if ($security_num <= $security_count) {
            return response()->json([
                'errors' => 'You don’t have a package, so you should subscribe.'
            ], 400);
        }
        $securityRequest = $request->validated(); 
        $securityRequest['password'] = $request->password;
        $image_path = $this->upload($request, 'image', '/village/security');
        $securityRequest['image'] = $image_path;
        $security = $this->security
        ->create($securityRequest);
        $security->pool()->sync($request->pool_ids);
        $security->beach()->sync($request->beach_ids);
        $security->gate()->sync($request->gate_ids);
        $security->inside_gates()->sync($request->inside_gate_ids);
      
        return response()->json([
            'success' => 'You add data success'
        ]);
    }

    public function gate_keeper_modify(SecuirtyRequest $request, $id){
        // name, password, image
        // email, phone, type, status
        $validator = Validator::make($request->all(), [
            'email' => 'unique:security_men,email,' . $id,
            'phone' => 'unique:security_men,phone,' . $id,
            'pool_ids' => 'array',
            'beach_ids' => 'array',
            'gate_ids' => 'array',
            'inside_gate_ids' => 'array',
            'inside_gate_ids.*' => 'required|exists:inside_gates,id',
            'pool_ids.*' => 'required|exists:pools,id',
            'beach_ids.*' => 'required|exists:beaches,id',
            'gate_ids.*' => 'required|exists:gates,id',
            "gate_visitors" => "required|boolean",
            "gate_entrance" => "required|boolean",
        ]);
        if ($validator->fails()) { // if Validate Make Error Return Message Error
            return response()->json([
                'errors' => $validator->errors(),
            ],400);
        }
        $securityRequest = $request->validated();
        $security = $this->security
        ->where('id', $id) 
        ->first();
        if (empty($security)) {
            return response()->json([
                'errors' => 'security not found'
            ], 400);
        }
        if ($request->image && !is_string($request->image)) {
            $image_path = $this->update_image($request, $security->image, 'image', '/village/security');
            $securityRequest['image'] = $image_path;
        }
        if (!empty($request->password)) {
            $securityRequest['password'] = bcrypt($request->password);
        }
        $security->update($securityRequest);
        $security->pool()->sync($request->pool_ids);
        $security->beach()->sync($request->beach_ids);
        $security->gate()->sync($request->gate_ids);
        $security->inside_gates()->sync($request->inside_gate_ids);

        return response()->json([
            'success' => 'You update data success'
        ]);
    }

    public function gate_keeper_delete($id){
        $security = $this->security
        ->where('id', $id)
        ->first();
        if (empty($security)) {
            return response()->json([
                'errors' => 'security not found'
            ], 400);
        }
        $this->deleteImage($security->image);
        $security->delete();

        return response()->json([
            'success' => 'You delete data success'
        ]);
    }
}
