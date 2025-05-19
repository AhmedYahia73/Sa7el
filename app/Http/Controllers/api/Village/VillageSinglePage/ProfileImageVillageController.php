<?php

namespace App\Http\Controllers\api\Village\VillageSinglePage;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\trait\image;

use App\Models\Village;

class ProfileImageVillageController extends Controller
{
    public function __construct(private Village $village){}
    use image;

    public function view(Request $request){
        $village = $this->village
        ->select('image', 'id')
        ->where('id', $request->user()->village_id)
        ->first();

        return response()->json([
            'village' => $village
        ]);
    } 

    public function create(Request $request){
        $validator = Validator::make($request->all(), [
            'image' => 'required', 
        ]);
        if ($validator->fails()) { // if Validate Make Error Return Message Error
            return response()->json([
                'errors' => $validator->errors(),
            ],400);
        }

        $village = $this->village
        ->where('id', $request->user()->village_id)
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

    public function delete(Request $request){
        $village = $this->village
        ->where('id', $request->user()->village_id)
        ->first();
        if (empty($village)) {
            return response()->json([
                'errors' => 'village not found'
            ], 400);
        }
        $this->deleteImage($village->image);
        $village->update([
            'image' => null
        ]);

        return response()->json([
            'success' => 'You delete data success'
        ]);
    }
}
