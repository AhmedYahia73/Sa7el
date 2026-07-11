<?php

namespace App\Http\Controllers\api\SuperAdmin\Provider;

use App\Http\Controllers\Controller;
use App\Models\ProviderReview;
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

    public function delete_review($id){
        ProviderReview::where("id", $id)
        ->delete();

        return response()->json([
            "success" => "You delete data success"
        ]);
    }
}
