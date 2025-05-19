<?php

namespace App\Http\Controllers\api\Village\Posts;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\trait\image;

use App\Models\Post;

class PostsController extends Controller
{
    public function __construct(private Post $post){}
    use image;

    public function view(Request $request){
        $post = $this->post
        ->where('village_id', $request->user()->village_id)
        ->get();

        return response()->json([
            'post' => $post,
        ]);
    } 

    public function create(Request $request){
        // image, description
        $validator = Validator::make($request->all(), [
            'image' => 'sometimes',
            'description' => 'sometimes',
        ]);
        if ($validator->fails()) { // if Validate Make Error Return Message Error
            return response()->json([
                'errors' => $validator->errors(),
            ],400);
        }
        $postRequest = $validator->validated();
        $postRequest['village_id'] = $request->user()->village_id;
        $postRequest['admin_id'] = $request->user()->id;
        if ($request->has('image')) {
            $image_path = $this->upload($request, 'image', '/village/post');
            $postRequest['image'] = $image_path;
        } 
        $post = $this->post
        ->create($postRequest);
      
        return response()->json([
            'success' => 'You add data success'
        ]);
    }

    public function modify(Request $request, $id){
        // image, description
        $validator = Validator::make($request->all(), [
            'email' => 'unique:post_men,email,' . $id,
            'phone' => 'unique:post_men,phone,' . $id,
        ]);
        if ($validator->fails()) { // if Validate Make Error Return Message Error
            return response()->json([
                'errors' => $validator->errors(),
            ],400);
        }
        $postRequest = $validator->validated();
        $post = $this->post
        ->where('id', $id)
        ->where('village_id', $request->user()->village_id)
        ->first();
        $postRequest['admin_id'] = $request->user()->id;
        if ($request->image && !is_string($request->image)) {
            $image_path = $this->update_image($request, $post->image, 'image', '/village/post');
            $postRequest['image'] = $image_path;
        }
        $post->update($postRequest); 
      
        return response()->json([
            'success' => 'You update data success'
        ]);
    }

    public function delete($id){
        $post = $this->post
        ->where('id', $id)
        ->first();
        if (empty($post)) {
            return response()->json([
                'errors' => 'post not found'
            ], 400);
        }
        $this->deleteImage($post->image);
        $post->delete();

        return response()->json([
            'success' => 'You delete data success'
        ]);
    }
}
