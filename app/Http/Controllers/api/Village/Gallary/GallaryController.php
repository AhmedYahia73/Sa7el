<?php

namespace App\Http\Controllers\api\Village\Gallary;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\trait\image;

use App\Models\VillageGallary;

class GallaryController extends Controller
{
    public function __construct(private VillageGallary $village_gallary){}
    use image;

    public function view(){
        $village_gallary = $this->village_gallary
        ->where('village_id', $request->user()->village_id)
        ->get();

        return response()->json([
            'village_gallary' => $village_gallary
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
        $this->village_gallary
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

        $image_path = $this->upload($request, 'image', 'images/village_gallary');
        $this->village_gallary
        ->create([
            'image' => $image_path,
            'status' => $request->status,
            'village_id' => $request->user()->village_id,
        ]);

        return response()->json([
            'success' => 'You add image success'
        ]);
    }

    public function delete($id){
        $village_gallary = $this->village_gallary
        ->where('id', $id)
        ->first();
        if (empty($village_gallary)) {
            return response()->json([
                'errors' => 'village not found'
            ], 400);
        }
        $this->deleteImage($village_gallary->image);
        $village_gallary->delete();

        return response()->json([
            'success' => 'You delete data success'
        ]);
    }
}
