<?php

namespace App\Http\Controllers\api\Village\Posts;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\trait\TraitImage;

use App\Models\Post;
use App\Models\PostImage;

class PostsController extends Controller
{
    public function __construct(private Post $post,
    private PostImage $post_image){}
    use TraitImage;

    public function view(Request $request){
        $post = $this->post
        ->with('images')
        ->where('village_id', $request->user()->village_id)
        ->get();

        return response()->json([
            'post' => $post,
        ]);
    } 

    public function create(Request $request){
        // images[], description
        $validator = Validator::make($request->all(), [
            'images' => 'sometimes|array',
            'description' => 'sometimes',
        ]);
        if ($validator->fails()) { // if Validate Make Error Return Message Error
            return response()->json([
                'errors' => $validator->errors(),
            ],400);
        }
        $postRequest = $validator->validated();
        $postRequest['description'] = $request->description;
        $postRequest['village_id'] = $request->user()->village_id;
        $postRequest['admin_id'] = $request->user()->id;
        $post = $this->post
        ->create($postRequest);
        if ($request->has('images')) {
            foreach ($request->images as $item) {
                $image_path = $this->uploadFile($item, '/village/post', count($request->images));
                $this->post_image
                ->create([
                    'image' => $image_path,
                    'post_id' => $post->id,
                ]);
            }
        } 
      
        return response()->json([
            'success' => 'You add data success'
        ]);
    }

    public function modify(Request $request, $id){
        // images[], description, images_id[]
        $validator = Validator::make($request->all(), [
            'images' => 'sometimes|array',
            'images_id' => 'sometimes|array',
            'description' => 'sometimes',
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
        $this->post_image
        ->whereNotIn('id', $request->images_id ?? [])
        ->where('post_id', $id)
        ->delete();
        $postRequest['admin_id'] = $request->user()->id;
        if ($request->images) {
            foreach ($request->images as $item) {
                $image_path = $this->uploadFile($item, '/village/post', count($request->images));
                $this->post_image
                ->create([
                    'image' => $image_path,
                    'post_id' => $id,
                ]);
            }
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
        $post->delete();

        return response()->json([
            'success' => 'You delete data success'
        ]);
    }
}
