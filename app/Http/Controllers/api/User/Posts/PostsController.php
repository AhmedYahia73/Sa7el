<?php

namespace App\Http\Controllers\api\User\Posts;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

use App\Models\Post;

class PostsController extends Controller
{
    public function __construct(private Post $posts){}

    public function view(Request $request){
        $validator = Validator::make($request->all(), [
            'village_id' => 'required|exists:villages,id',
        ]);
        if ($validator->fails()) { // if Validate Make Error Return Message Error
            $firstError = $validator->errors()->first();
            return response()->json([
                'errors' => $firstError,
            ],400);
        }
        $posts = $this->posts
        ->where('village_id', $request->village_id)
        ->withCount('love')
        ->orderByDesc('id')
        ->get()
        ->map(function($item){
            return [
                'id' => $item->id,
                'image' => $item->images,
                'description' => $item->description,
                'love_count' => $item->love_count,
                'my_love' => count($item->my_love) > 0 ? 1 : 0,
                'user_name' => empty($item->admin) ? $item?->village?->name : $item?->admin?->name,
                'user_image' => empty($item->admin) ? $item?->village?->image_link : $item?->admin?->image_link,
            ];
        });

        return response()->json([
            'posts' => $posts
        ]);
    }

    public function react(Request $request){
        $validator = Validator::make($request->all(), [
            'post_id' => 'required|exists:posts,id',
            'react' => 'required|boolean',
        ]);
        if ($validator->fails()) { // if Validate Make Error Return Message Error
            $firstError = $validator->errors()->first();
            return response()->json([
                'errors' => $firstError,
            ],400);
        }

        if ($request->react) {
            $request->user()->love()->attach($request->post_id);
        } else {
            $request->user()->love()->detach($request->post_id);
        }
        
        return response()->json([
            'success' => 'You react success'
        ]);
    }
}
