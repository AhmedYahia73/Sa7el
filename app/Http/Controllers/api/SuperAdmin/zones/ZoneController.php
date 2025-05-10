<?php

namespace App\Http\Controllers\api\SuperAdmin\zones;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Http\Requests\SuperAdmin\ZoneRequest;
use App\trait\image;

use App\Models\Zone;

class ZoneController extends Controller
{
    public function __construct(private Zone $zone){}
    use image;

    public function view(){
        $zone = $this->zone
        ->with('translations')
        ->get();

        return response()->json([
            'zones' => $zone,
        ]);
    }

    public function zone($id){
        $zone = $this->zone
        ->where('id', $id)
        ->first();

        return response()->json([
            'zone' => $zone,
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
        
        $zone = $this->zone
        ->where('id', $id)
        ->update([
            'status' => $request->status
        ]);

        return response()->json([
            'success' => $request->status ? 'active' : 'banned'
        ]);
    }

    public function create(ZoneRequest $request){
        // name, description, status
        // ar_name, ar_description, image
        $zoneRequest = $request->validated();
        if (!is_string($request->image)) {
            $image_path = $this->upload($request, 'image', 'images/zones');
            $zoneRequest['image'] = $image_path;
        }
        $zone = $this->zone
        ->create($zoneRequest);
        $zone_translations = [[ 
            'locale' => 'en',
            'key' => 'name',
            'value' => $request->name,
        ]];
        if (!empty($request->ar_name)) {
            $zone_translations[] = [ 
                'locale' => 'ar',
                'key' => 'name',
                'value' => $request->ar_name,
            ];
        }
        if (!empty($request->description)) {
            $zone_translations[] = [ 
                'locale' => 'en',
                'key' => 'description',
                'value' => $request->description,
            ];
        }
        if (!empty($request->ar_description)) {
            $zone_translations[] = [ 
                'locale' => 'ar',
                'key' => 'description',
                'value' => $request->ar_description,
            ];
        }
        $zone->translations()->createMany($zone_translations);

        return response()->json([
            'success' => 'You add data success'
        ]);
    }

    public function modify(ZoneRequest $request, $id){
        // name, description, status
        // ar_name, ar_description
        $zoneRequest = $request->validated();
        $zone = $this->zone
        ->where('id', $id)
        ->first();
        if (empty($zone)) {
            return response()->json([
                'errors' => 'zone not found'
            ], 400);
        }
        if (!is_string($request->image)) {
            $image_path = $this->update_image($request, $zone->image, 'image', 'images/zones');
            $zoneRequest['image'] = $image_path;
        }
        $zone
        ->update($zoneRequest);
        $zone_translations = [[ 
            'locale' => 'en',
            'key' => 'name',
            'value' => $request->name,
        ]];
        if (!empty($request->ar_name)) {
            $zone_translations[] = [ 
                'locale' => 'ar',
                'key' => 'name',
                'value' => $request->ar_name,
            ];
        }
        if (!empty($request->description)) {
            $zone_translations[] = [ 
                'locale' => 'en',
                'key' => 'description',
                'value' => $request->description,
            ];
        }
        if (!empty($request->ar_description)) {
            $zone_translations[] = [ 
                'locale' => 'ar',
                'key' => 'description',
                'value' => $request->ar_description,
            ];
        }
        $zone->translations()->delete();
        $zone->translations()->createMany($zone_translations);

        return response()->json([
            'success' => 'You update data success'
        ]);
    }

    public function delete($id){
        $zone = $this->zone
        ->where('id', $id)
        ->first();
        if (empty($zone)) {
            return response()->json([
                'errors' => 'zone not found'
            ], 400);
        }
        $zone->translations()->delete();
        $this->deleteImage($zone->image);
        $zone->delete();

        return response()->json([
            'success' => 'You delete data success'
        ]);
    }
}
