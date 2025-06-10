<?php

namespace App\Http\Controllers\api\SuperAdmin\village;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\trait\TraitImage;

use App\Models\Village;

class VillageCoverController extends Controller
{
    public function __construct(private Village $village){}
    use TraitImage;

    public function view($id){
        $village = $this->village
        ->select('cover_image', 'id')
        ->where('id', $id)
        ->first();

        return response()->json([
            'village' => $village
        ]);
    } 

    public function create(Request $request, $id){
        $validator = Validator::make($request->all(), [
            'cover_image' => 'required', 
        ]);
        if ($validator->fails()) { // if Validate Make Error Return Message Error
            return response()->json([
                'errors' => $validator->errors(),
            ],400);
        }

        $village = $this->village
        ->where('id', $id)
        ->first();
        if (empty($village->cover_image)) {
            $image_path = $this->upload($request, 'cover_image', 'images/village_cover_image');
            $village->update([
                'cover_image' => $image_path
            ]);
        } 
        else {
            $image_path = $this->update_image($request, $village->cover_image ,'cover_image', 'images/village_cover_image');
            $village->update([
                'cover_image' => $image_path
            ]);
        }
        

        return response()->json([
            'success' => 'You add image success'
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
        $this->deleteImage($village->cover_image);
        $village->update([
            'cover_image' => null
        ]);

        return response()->json([
            'success' => 'You delete data success'
        ]);
    }
}
