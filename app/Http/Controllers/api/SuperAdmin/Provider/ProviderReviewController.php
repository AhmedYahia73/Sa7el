<?php

namespace App\Http\Controllers\api\SuperAdmin\Provider;

use App\Http\Controllers\Controller;
use App\Models\ProviderReview;
use App\Models\User;
use App\Models\Provider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ProviderReviewController extends Controller
{

    public function show_reviews(Request $request){
        $validator = Validator::make($request->all(), [
            "provider_id" => "required|exists:providers,id"
        ]);

        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors()->first(),
            ], 400);
        }

        // تم تعديل throught إلى through الصحيحة
        $reviews = ProviderReview::where("provider_id", $request->provider_id)
            ->with("user")
            ->paginate(10)
            ->through(function($item){
                return [
                    "id"         => $item->id,
                    "rate"       => $item->rate,
                    "comment"    => $item->comment,
                    "user_name"  => $item->user?->name,
                    "image_link" => $item->user?->image_link,
                    "phone"      => $item->user?->phone,
                ];
            }); 

        return response()->json([
            "reviews" => $reviews
        ]);
    }

    public function provider_lists(Request $request){
        // تم تعديل throught إلى through الصحيحة
        $validator = Validator::make($request->all(), [
            "search" => "sometimes"
        ]); 
        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors()->first(),
            ], 400);
        }
        
        $providers = Provider::
        where("status", true)
        ->when(request()->search, function($query){
            $query->where("name", "like", "%" . request()->search . "%");
        })
        ->get()
        ->paginate(10)
        ->through(function($item){
            return [
                "id"         => $item->id,
                "name"       => $item->name,
            ];
        }); 

        return response()->json([
            "providers" => $providers
        ]);
    }

    public function user_lists(Request $request){
        // تم تعديل throught إلى through الصحيحة
        $validator = Validator::make($request->all(), [
            "search" => "sometimes"
        ]); 
        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors()->first(),
            ], 400);
        }
        
        $users = User::
        where("status", true)
        ->when(request()->search, function($query){
            $query->where("name", "like", "%" . request()->search . "%");
        })
        ->get()
        ->paginate(10)
        ->through(function($item){
            return [
                "id"         => $item->id,
                "name"       => $item->name,
            ];
        }); 

        return response()->json([
            "users" => $users
        ]);
    }

    public function review_item($id){
        // تم تعديل throught إلى through الصحيحة
        $reviews = ProviderReview::
            where("id", $id) 
            ->first(); 

        return response()->json([
            "review_item" => $reviews
        ]);
    }

    public function add(Request $request){
        $validator = Validator::make($request->all(), [
            "rate" => "required|numeric|min:1|max:5",
            "comment" => "required|string",
            "provider_id" => "required|exists:providers,id",
            "user_id" => "required|exists:users,id",
        ]); 
        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors()->first(),
            ], 400);
        }
        ProviderReview::create([
            "rate" => $request->rate,
            "comment" => $request->comment,
            "provider_id" => $request->provider_id,
            "user_id" => $request->user_id,
        ]);

        return response()->json([
            "success" => "You add data success"
        ]);
    }

    public function update(Request $request, $id){
        $validator = Validator::make($request->all(), [
            "rate" => "required|numeric|min:1|max:5",
            "comment" => "required|string", 
        ]); 
        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors()->first(),
            ], 400);
        }
        ProviderReview::where("id", $id)
        ->update([
            "rate" => $request->rate,
            "comment" => $request->comment
        ]);

        return response()->json([
            "success" => "You update data success"
        ]);
    }

    public function delete_review($id){
        ProviderReview::where("id", $id)
        ->delete();

        return response()->json([
            "success" => "You delete data success"
        ]);
    }
}
