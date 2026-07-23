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
        // 1. التحقق من المدخلات
        $validator = Validator::make($request->all(), [
            'village_id' => 'required|exists:villages,id',
            'search'     => 'nullable|string|max:255',
            'from'       => 'nullable|date',
            'to'         => 'nullable|date',
            'locale'     => 'in:ar,en',
        ]);

        if ($validator->fails()) { 
            $firstError = $validator->errors()->first();
            return response()->json([
                'errors' => $firstError,
            ], 400);
        }

        $query = $this->posts
            ->where('village_id', $request->village_id)
            ->withCount('love')
            ->with(['images', 'village', 'my_love']); // تم استيراد العلاقات المستخدمة في التحويل

        if($request->from){
            $query->whereDate("created_at", ">=", $request->from);
        }
        if($request->to){
            $query->whereDate("created_at", "<=", $request->to);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            
            $query->where(function ($q) use ($search) {
                $q->where('description', 'like', "%{$search}%")
                ->orWhereHas('admin', function ($adminQuery) use ($search) {
                    $adminQuery->where('name', 'like', "%{$search}%");
                });
            });
        }

        $paginatedPosts = $query->orderByDesc('id')->paginate($request->get('per_page', 15));

        $formattedPosts = collect($paginatedPosts->items())->map(function($item) {
            return [
                'id'          => $item->id,
                'image'       => $item->images->pluck('image_link'),
                'description' => $item->description,
                'love_count'  => $item->love_count,
                'admin_name'  => $item->village?->name,
                'my_love'     => $item->my_love->count() > 0 ? 1 : 0, // تم تحسينها لتعمل مع الـ Collection المرفقة مباشرة
                'user_name'   => !empty($item->village) ? $item->village?->name : $item->admin?->name,
                'user_image'  => !empty($item->village) ? $item->village?->logo ? 
                $item->village?->logo_link : $item->village?->image_link : $item->admin?->image_link,
            ];
        });

        return response()->json([
            'posts'      => $formattedPosts,
            'pagination' => [
                'current_page' => $paginatedPosts->currentPage(),
                'last_page'    => $paginatedPosts->lastPage(),
                'per_page'     => $paginatedPosts->perPage(),
                'total'        => $paginatedPosts->total(),
            ]
        ]);
    }

    public function react(Request $request){
        $validator = Validator::make($request->all(), [
            'post_id' => 'required|exists:posts,id',
            'react' => 'required|boolean',
            'locale' => 'in:ar,en',
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
            'success' => $request->locale == "en" ? 'You react success' : 'تم التفاعل بنجاح'
        ]);
    }
}
