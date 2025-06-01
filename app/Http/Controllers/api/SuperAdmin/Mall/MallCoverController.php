<?php

namespace App\Http\Controllers\api\SuperAdmin\Mall;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\trait\image;

use App\Models\Mall;

class MallCoverController extends Controller
{
    public function __construct(private Mall $mall){}
    use image;

    public function view($id){
        $mall = $this->mall
        ->select('cover_image', 'id')
        ->where('id', $id)
        ->first();

        return response()->json([
            'mall' => $mall
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

        $mall = $this->mall
        ->where('id', $id)
        ->first();
        if (empty($mall->cover_image)) {
            $image_path = $this->upload($request, 'cover_image', 'images/mall_cover_image');
            $mall->update([
                'cover_image' => $image_path
            ]);
        } 
        else {
            $image_path = $this->update_image($request, $mall->cover_image ,'cover_image', 'images/mall_cover_image');
            $mall->update([
                'cover_image' => $image_path
            ]);
        }
        

        return response()->json([
            'success' => 'You add image success'
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
        $this->deleteImage($mall->cover_image);
        $mall->update([
            'cover_image' => null
        ]);

        return response()->json([
            'success' => 'You delete data success'
        ]);
    }
}
