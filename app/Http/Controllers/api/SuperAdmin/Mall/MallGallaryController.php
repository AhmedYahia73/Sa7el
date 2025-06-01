<?php

namespace App\Http\Controllers\api\SuperAdmin\Mall;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\trait\image;

use App\Models\MallGallery;

class MallGallaryController extends Controller
{
    public function __construct(private MallGallery $mall_gallery){}
    use image;

    public function view($id){
        $mall_gallery = $this->mall_gallery
        ->where('mall_id', $id)
        ->get();

        return response()->json([
            'mall_gallery' => $mall_gallery
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
        $this->mall_gallery
        ->where('id', $id)
        ->update([
            'status' => $request->status
        ]);

        return response()->json([
            'success' => $request->status ? 'active' : 'banned'
        ]);
    }

    public function create(Request $request, $id){
        $validator = Validator::make($request->all(), [
            'image' => 'required',
            'status' => 'required|boolean',
        ]);
        if ($validator->fails()) { // if Validate Make Error Return Message Error
            return response()->json([
                'errors' => $validator->errors(),
            ],400);
        }

        $image_path = $this->upload($request, 'image', 'images/mall_gallery');
        $this->mall_gallery
        ->create([
            'image' => $image_path,
            'status' => $request->status,
            'mall_id' => $id
        ]);

        return response()->json([
            'success' => 'You add image success'
        ]);
    }

    public function delete($id){
        $mall_gallery = $this->mall_gallery
        ->where('id', $id)
        ->first();
        if (empty($mall_gallery)) {
            return response()->json([
                'errors' => 'village not found'
            ], 400);
        }
        $this->deleteImage($mall_gallery->image);
        $mall_gallery->delete();

        return response()->json([
            'success' => 'You delete data success'
        ]);
    }
}
