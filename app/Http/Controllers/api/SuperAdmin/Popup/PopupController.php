<?php

namespace App\Http\Controllers\api\SuperAdmin\Popup;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\trait\TraitImage;

use App\Models\Popup;
use App\Models\Village;

class PopupController extends Controller
{
    public function __construct(private Popup $popup, private Village $village){}
    use TraitImage;

    public function view()
    {
        $popups = $this->popup
            ->with('village')
            ->get()
            ->map(function($item){
                return [
                    "id" => $item->id,
                    "title" => $item->title,
                    "description" => $item->description,
                    "image" => $item->image_link,
                    "all" => $item->all,
                    "status" => $item->status,
                    "gender" => $item->gender,
                    "age_from" => $item->age_from,
                    "age_to" => $item->age_to,
                    "start_date" => $item->start_date,
                    "end_date" => $item->end_date,
                    "village" => $item?->village?->name,
                ];
            }); 

        return response()->json([
            'popups'   => $popups, 
        ]);
    }

    public function lists()
    { 

        $villages = $this->village
            ->where('status', 1)
            ->get()
            ->map(function($item){
                return [
                    "id" => $item->id,
                    "name" => $item->name,
                ];
            });

        return response()->json([ 
            'villages' => $villages,
        ]);
    }

    public function show($id)
    {
        $popup = $this->popup
            ->with('village')
            ->where('id', $id)
            ->first();

        if (empty($popup)) {
            return response()->json([
                'errors' => 'popup not found',
            ], 400);
        }

        return response()->json([
            'popup' => $popup,
        ]);
    }

    public function create(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title'          => 'required|string',
            'image'          => 'required|image',
            'description'    => 'required|string',
            'ar_title'       => 'nullable|string',
            'ar_image'       => 'nullable|image',
            'ar_description' => 'nullable|string',
            'village_id'     => 'nullable|exists:villages,id',
            'status'         => 'required|boolean',
            "gender"         => "required|in:all,male,female",
            "age_from"       => "sometimes|numeric",
            "age_to"         => "sometimes|numeric",
            "start_date"     => "required|date",
            "end_date"       => "required|date",
        ]);

        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors(),
            ], 400);
        }
 
        if ($request->village_id) {
            $exists = $this->popup
                ->where('village_id', $request->village_id)
                ->where("status", 1)
                ->where("start_date", "<=", date("Y-m-d"))
                ->where("end_date", ">=", date("Y-m-d"))
                ->count();

            if ($exists >= 2) {
                return response()->json([
                    'errors' => 'This village already has a popup',
                ], 400);
            }
        }
        else {
            $exists = $this->popup
                ->where('all', true)
                ->where("status", 1)
                ->where("start_date", "<=", date("Y-m-d"))
                ->where("end_date", ">=", date("Y-m-d"))
                ->count();

            if ($exists >= 2) {
                return response()->json([
                    'errors' => 'This village already has a popup',
                ], 400);
            }
        }

        $image_path    = $this->upload($request, 'image', 'images/popups');
        if($request->ar_image){
            $ar_image_path = $this->upload($request, 'ar_image', 'images/popups');
        }
 
        $all = empty($request->village_id) ? true : false;
        $this->popup->create([
            'village_id'     => $request->village_id,
            'title'          => $request->title,
            'description'    => $request->description,
            'image'          => $image_path,
            'ar_title'       => $request->ar_title,
            'ar_description' => $request->ar_description,
            'ar_image'       => $ar_image_path ?? null,
            'all'            => $all,
            'status'         => $request->status ?? true,
            'gender'         => $request->gender,
            'age_from'       => $request->age_from ?? null,
            'age_to'         => $request->age_to ?? null,
            'start_date'     => $request->start_date,
            'end_date'       => $request->end_date,
        ]);

        return response()->json([
            'success' => 'You add popup success',
        ]);
    }

    public function modify(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'title'          => 'required|string',
            'image'          => 'nullable|image',
            'description'    => 'required|string',
            'ar_title'       => 'nullable|string',
            'ar_image'       => 'nullable|image',
            'ar_description' => 'nullable|string',
            'village_id'     => 'nullable|exists:villages,id',
            'status'         => 'required|boolean',
            "gender"         => "required|in:all,male,female",
            "age_from"       => "sometimes|numeric",
            "age_to"       => "sometimes|numeric",
            "start_date"     => "required|date",
            "end_date"       => "required|date",
        ]);

        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors(),
            ], 400);
        }

        $popup = $this->popup->where('id', $id)->first();

        if (empty($popup)) {
            return response()->json([
                'errors' => 'popup not found',
            ], 400);
        }
 
        if ($request->village_id) {
            $exists = $this->popup
                ->where('village_id', $request->village_id)
                ->where('id', '!=', $id)
                ->where("status", 1)
                ->where("start_date", "<=", date("Y-m-d"))
                ->where("end_date", ">=", date("Y-m-d"))
                ->count();

            if ($exists >= 2) {
                return response()->json([
                    'errors' => 'This village already has a popup',
                ], 400);
            }
        }

        if($request->image){
            $image_path    = $this->update_image($request, $popup->image, 'image', 'images/popups');
        }
        if($request->ar_image){
            $ar_image_path = $this->update_image($request, $popup->ar_image, 'ar_image', 'images/popups');
        }

        $all = empty($request->village_id) ? true : false;

        $popup->update([
            'village_id'     => $request->village_id,
            'title'          => $request->title,
            'description'    => $request->description,
            'image'          => $image_path ?? $popup->image,
            'ar_title'       => $request->ar_title,
            'ar_description' => $request->ar_description,
            'ar_image'       => $ar_image_path ?? $popup->ar_image,
            'all'            => $all,
            'status'         => $request->status ?? $popup->status,
            'gender'         => $request->gender,
            'age_from'       => $request->age_from ?? null,
            'age_to'         => $request->age_to ?? null,
            'start_date'     => $request->start_date,
            'end_date'       => $request->end_date,
        ]);

        return response()->json([
            'success' => 'You update popup success',
        ]);
    }

    public function delete($id)
    {
        $popup = $this->popup->where('id', $id)->first();

        if (empty($popup)) {
            return response()->json([
                'errors' => 'popup not found',
            ], 400);
        }

        $this->deleteImage($popup->image);
        $this->deleteImage($popup->ar_image);
        $popup->delete();

        return response()->json([
            'success' => 'You delete popup success',
        ]);
    }

    public function status(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'status' => 'required|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors(),
            ], 400);
        }

        $popup = $this->popup->where('id', $id)->first();

        if (empty($popup)) {
            return response()->json([
                'errors' => 'popup not found',
            ], 400);
        }

        if (!empty($popup->village_id)) {
            $exists = $this->popup
                ->where('village_id', $popup->village_id)
                ->where("status", 1)
                ->count();

            if ($exists >= 2) {
                return response()->json([
                    'errors' => 'This village already has a popup',
                ], 400);
            }
        }
        else {
            $exists = $this->popup
                ->where('all', true)
                ->where("status", 1)
                ->count();

            if ($exists >= 2) {
                return response()->json([
                    'errors' => 'This village already has a popup',
                ], 400);
            }
        }
        $popup->update([
            'status' => $request->status,
        ]);

        return response()->json([
            'success' => $request->status ? 'active' : 'banned',
        ]);
    }
}
