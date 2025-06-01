<?php

namespace App\Http\Controllers\api\SuperAdmin\Mall;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\SuperAdmin\MallRequest;
use Illuminate\Support\Facades\Validator;
use App\trait\image;

use App\Models\Zone;
use App\Models\Mall;
use App\Models\Provider;

class MallController extends Controller
{
    public function __construct(private Mall $mall, private Provider $provider
    , private Zone $zones){}
    use image;

    public function view(){
        $mall = $this->mall
        ->with(['translations', 'zone'])
        ->get();
        $zones = $this->zones
        ->get();

        return response()->json([
            'malls' => $mall,
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

        $mall = $this->mall
        ->where('id', $id)
        ->first();
        if (empty($mall->image)) {
            $image_path = $this->upload($request, 'image', 'images/mall_image');
            $mall->update([
                'image' => $image_path
            ]);
        } 
        else {
            $image_path = $this->update_image($request, $mall->image ,'image', 'images/mall_image');
            $mall->update([
                'image' => $image_path
            ]);
        }
        

        return response()->json([
            'success' => 'You add image success'
        ]);
    }

    public function mall($id){
        $mall = $this->mall
        ->with(['translations', 'zone']) 
        ->where('id', $id)
        ->first();

        return response()->json([
            'mall' => $mall,
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
        
        $mall = $this->mall
        ->where('id', $id)
        ->update([
            'status' => $request->status
        ]);

        return response()->json([
            'success' => $request->status ? 'active' : 'banned'
        ]);
    }

    public function create(MallRequest $request){
        // name, description, status, zone_id, open_from, open_to
        // ar_name, ar_description, image
        $MallRequest = $request->validated();
        if (!is_string($request->image)) {
            $image_path = $this->upload($request, 'image', 'images/malls');
            $MallRequest['image'] = $image_path;
        }
        $mall = $this->mall
        ->create($MallRequest);
        $mall_translations = [[ 
            'locale' => 'en',
            'key' => 'name',
            'value' => $request->name,
        ]];
        if (!empty($request->ar_name)) {
            $mall_translations[] = [ 
                'locale' => 'ar',
                'key' => 'name',
                'value' => $request->ar_name,
            ];
        }
        if (!empty($request->description)) {
            $mall_translations[] = [ 
                'locale' => 'en',
                'key' => 'description',
                'value' => $request->description,
            ];
        }
        if (!empty($request->ar_description)) {
            $mall_translations[] = [ 
                'locale' => 'ar',
                'key' => 'description',
                'value' => $request->ar_description,
            ];
        }
        $mall->translations()->createMany($mall_translations);

        return response()->json([
            'success' => 'You add data success'
        ]);
    }

    public function modify(MallRequest $request, $id){
        // name, description, status, zone_id, open_from, open_to
        // ar_name, ar_description, image
        $MallRequest = $request->validated();
        $mall = $this->mall
        ->where('id', $id)
        ->first();
        if (empty($mall)) {
            return response()->json([
                'errors' => 'mall not found'
            ], 400);
        }
        if (!is_string($request->image)) {
            $image_path = $this->update_image($request, $mall->image, 'image', 'images/malls');
            $MallRequest['image'] = $image_path;
        }
        $mall
        ->update($MallRequest);
        $mall_translations = [[ 
            'locale' => 'en',
            'key' => 'name',
            'value' => $request->name,
        ]];
        if (!empty($request->ar_name)) {
            $mall_translations[] = [ 
                'locale' => 'ar',
                'key' => 'name',
                'value' => $request->ar_name,
            ];
        }
        if (!empty($request->description)) {
            $mall_translations[] = [ 
                'locale' => 'en',
                'key' => 'description',
                'value' => $request->description,
            ];
        }
        if (!empty($request->ar_description)) {
            $mall_translations[] = [ 
                'locale' => 'ar',
                'key' => 'description',
                'value' => $request->ar_description,
            ];
        }
        $mall->translations()->delete();
        $mall->translations()->createMany($mall_translations);

        return response()->json([
            'success' => 'You update data success'
        ]);
    }

    public function delete($id){
        $mall = $this->mall
        ->where('id', $id)
        ->first();
        if (empty($mall)) {
            return response()->json([
                'errors' => 'mall not found'
            ], 400);
        }
        $mall->translations()->delete();
        $this->deleteImage($mall->image);
        $mall->delete();

        return response()->json([
            'success' => 'You delete data success'
        ]);
    }

 // _____________________________________________________________

    public function provider_mall(Request $request){
        $validator = Validator::make($request->all(), [
            'mall_id' => 'required|exists:malls,id',
        ]);
        if ($validator->fails()) { // if Validate Make Error Return Message Error
            return response()->json([
                'errors' => $validator->errors(),
            ],400);
        }
        $provider = $this->provider
        ->with(['translations', 'service', 'package', 'zone'])
        ->where('mall_id', $request->mall_id)
        ->get();

        return response()->json([
            'provider' => $provider,
        ]);
    }

}
