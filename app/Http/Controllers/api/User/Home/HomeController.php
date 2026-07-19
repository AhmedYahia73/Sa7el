<?php

namespace App\Http\Controllers\api\User\Home;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

use App\Models\Village;
use App\Models\Popup;
use App\Models\InsideGate;

class HomeController extends Controller
{
    public function village($id){
        $village = Village::
        where("id", $id)
        ->first();

        return response()->json([
            "logo" => $village->logo_link,
            "name" => $village->name,
        ]);
    }

    public function inside_gate_beach(Request $request){
        $validator = Validator::make($request->all(), [
            'locale' => 'required|in:ar,en',
        ]);

        if ($validator->fails()) { 
            return response()->json([
                'errors' => $validator->errors(),
            ], 400);
        }

        $locale = $request->locale;
        $beach = InsideGate::
        where("village_id", auth()->user()->village_id)
        ->where("type", "beach")
        ->get()
        ->map(function($item) use($locale){
            return [
                "id" => $item->id,
                "name" => $locale == "en" ?
                $item->name : $item->ar_name,
            ];
        });

        return response()->json([
            "beaches" => $beach, 
        ]);
    }

    public function inside_gate_pool(Request $request){
        $validator = Validator::make($request->all(), [
            'locale' => 'required|in:ar,en',
        ]);

        if ($validator->fails()) { 
            return response()->json([
                'errors' => $validator->errors(),
            ], 400);
        }

        $locale = $request->locale;
        $pool = InsideGate::
        where("village_id", auth()->user()->village_id)
        ->where("type", "pool")
        ->get()
        ->map(function($item) use($locale){
            return [
                "id" => $item->id,
                "name" => $locale == "en" ?
                $item->name : $item->ar_name,
            ];
        });

        return response()->json([
            "pools" => $pool, 
        ]);
    }

    public function popup_all(Request $request){
        $validator = Validator::make($request->all(), [
            'locale' => 'required|in:ar,en',
        ]);

        if ($validator->fails()) { 
            return response()->json([
                'errors' => $validator->errors(),
            ], 400);
        }

        $locale = $request->locale;
        
        // 1. إصلاح تسمية المتغير وحساب العمر بشكل صحيح
        $birthDate = auth()->user()->birthDate;
        $my_age = $birthDate ? Carbon::parse($birthDate)->age : null;

        // 2. بناء الاستعلام الأساسي
        $popupQuery = Popup::where("all", 1)
            ->where("status", 1)
            ->where("start_date", "<=", date("Y-m-d"))
            ->where("end_date", ">=", date("Y-m-d"))
            ->where(function($query){
                $query->where("gender", "all")
                      ->orWhere("gender", auth()->user()->gender);
            });

        // 3. لوجيك العمر المظبوط والآمن
        $popupQuery->where(function($query) use ($my_age) {
            if ($my_age !== null) {
                // إذا كان عمر المستخدم معروفاً:
                $query->where(function($subQuery) use ($my_age) {
                    $subQuery->where("age_from", "<=", $my_age)
                             ->where("age_to", ">=", $my_age);
                })
                ->orWhere(function($subQuery) use ($my_age) {
                    $subQuery->where("age_from", "<=", $my_age)
                             ->whereNull("age_to");
                })
                ->orWhere(function($subQuery) use ($my_age) {
                    $subQuery->whereNull("age_from")
                             ->where("age_to", ">=", $my_age);
                })
                // أو بوب اب عامة تماماً بدون شروط عمر
                ->orWhere(function($subQuery) {
                    $subQuery->whereNull("age_from")
                             ->whereNull("age_to");
                });
            } else {
                // إذا كان عمر المستخدم غير معروف (null):
                // يرى فقط البوب اب العامة التي لا تشترط سن معين (العمر من وإلى فارغين)
                $query->whereNull("age_from")
                      ->whereNull("age_to");
            }
        });

        $popups = $popupQuery->get()
        ->map(function($popup) use($locale){
            return [
                "title" => $locale == "en" ? $popup->title : ($popup->ar_title ?? $popup->title),
                "description" => $locale == "en" ? $popup->description : ($popup->ar_description ?? $popup->description),
                "image" => $locale == "en" ? $popup->image_link : ($popup->ar_image_link ?? $popup->image_link),
            ];
        });

        return response()->json([
            "popups" => $popups
        ]);
    }

    public function village_popup(Request $request){
        $validator = Validator::make($request->all(), [
            'locale' => 'required|in:ar,en',
            'village_id' => "required|exists:villages,id"
        ]);
        if ($validator->fails()) { // if Validate Make Error Return Message Error
            return response()->json([
                'errors' => $validator->errors(),
            ],400);
        } 


        $locale = $request->locale;
        
        // 1. إصلاح تسمية المتغير وحساب العمر بشكل صحيح
        $birthDate = auth()->user()->birthDate;
        $my_age = $birthDate ? Carbon::parse($birthDate)->age : null;

        // 2. بناء الاستعلام الأساسي
        $popupQuery = Popup::where("all", 1)
            ->where("status", 1)
            ->where("start_date", "<=", date("Y-m-d"))
            ->where("end_date", ">=", date("Y-m-d"))
            ->where("village_id", $request->village_id)
            ->where(function($query){
                $query->where("gender", "all")
                      ->orWhere("gender", auth()->user()->gender);
            });

        // 3. لوجيك العمر المظبوط والآمن
        $popupQuery->where(function($query) use ($my_age) {
            if ($my_age !== null) {
                // إذا كان عمر المستخدم معروفاً:
                $query->where(function($subQuery) use ($my_age) {
                    $subQuery->where("age_from", "<=", $my_age)
                             ->where("age_to", ">=", $my_age);
                })
                ->orWhere(function($subQuery) use ($my_age) {
                    $subQuery->where("age_from", "<=", $my_age)
                             ->whereNull("age_to");
                })
                ->orWhere(function($subQuery) use ($my_age) {
                    $subQuery->whereNull("age_from")
                             ->where("age_to", ">=", $my_age);
                })
                // أو بوب اب عامة تماماً بدون شروط عمر
                ->orWhere(function($subQuery) {
                    $subQuery->whereNull("age_from")
                             ->whereNull("age_to");
                });
            } else {
                // إذا كان عمر المستخدم غير معروف (null):
                // يرى فقط البوب اب العامة التي لا تشترط سن معين (العمر من وإلى فارغين)
                $query->whereNull("age_from")
                      ->whereNull("age_to");
            }
        });

        $popups = $popupQuery->get()
        ->map(function($popup) use($locale){
            return [
                "title" => $locale == "en" ? $popup->title : ($popup->ar_title ?? $popup->title),
                "description" => $locale == "en" ? $popup->description : ($popup->ar_description ?? $popup->description),
                "image" => $locale == "en" ? $popup->image_link : ($popup->ar_image_link ?? $popup->image_link),
            ];
        });

        return response()->json([
            "popups" => $popups
        ]);
    }
}
