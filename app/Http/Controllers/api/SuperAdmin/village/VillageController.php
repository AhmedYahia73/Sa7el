<?php

namespace App\Http\Controllers\api\SuperAdmin\village;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\SuperAdmin\VillageRequest;
use Illuminate\Support\Facades\Validator;
use App\trait\image;

use App\Models\Zone;
use App\Models\Village;
use App\Models\Appartment;

class VillageController extends Controller
{
    public function __construct(private Village $village
    , private Zone $zones, private Appartment $appartment){}
    use image;

    public function view(){
        $village = $this->village
        ->with(['translations', 'zone'])
        ->withCount('population', 'units')
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

    public function village($id){
        $village = $this->village
        ->with(['translations', 'zone'])
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
        // ar_name, ar_description, image
        $villageRequest = $request->validated();
        if (!is_string($request->image)) {
            $image_path = $this->upload($request, 'image', 'images/villages');
            $villageRequest['image'] = $image_path;
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

        return response()->json([
            'success' => 'You add data success'
        ]);
    }

    public function modify(VillageRequest $request, $id){
       // name, description, status, zone_id, location
        // ar_name, ar_description, image
        $villageRequest = $request->validated();
        $village = $this->village
        ->where('id', $id)
        ->first();
        if (empty($village)) {
            return response()->json([
                'errors' => 'village not found'
            ], 400);
        }
        if (!is_string($request->image)) {
            $image_path = $this->update_image($request, $village->image, 'image', 'images/villages');
            $villageRequest['image'] = $image_path;
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
}
