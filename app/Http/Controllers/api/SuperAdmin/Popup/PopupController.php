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
            ->get();

        $villages = $this->village
            ->where('status', 1)
            ->get();

        return response()->json([
            'popups'   => $popups,
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
            'status'         => 'nullable|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors(),
            ], 400);
        }

        // إذا تم تحديد قرية، تحقق إنه مفيش popup تانية لنفس القرية
        if ($request->village_id) {
            $exists = $this->popup
                ->where('village_id', $request->village_id)
                ->exists();

            if ($exists) {
                return response()->json([
                    'errors' => 'This village already has a popup',
                ], 400);
            }
        }

        $image_path    = $this->upload($request, 'image', 'images/popups');
        $ar_image_path = $this->upload($request, 'ar_image', 'images/popups');

        // لو مفيش village_id → all = true، لو في village_id → all = false
        $all = empty($request->village_id) ? true : false;

        // لو all = true، مفيش قرية لها popup → نزيل village_id أو نحطها null
        // لكن المايجريشن بيطلب village_id (foreign key) — هنحتاج نعدله أو نستخدم قرية dummy
        // بنفترض إن المايجريشن سيتم تعديله ليكون nullable
        $this->popup->create([
            'village_id'     => $request->village_id,
            'title'          => $request->title,
            'description'    => $request->description,
            'image'          => $image_path,
            'ar_title'       => $request->ar_title,
            'ar_description' => $request->ar_description,
            'ar_image'       => $ar_image_path,
            'all'            => $all,
            'status'         => $request->status ?? true,
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
            'status'         => 'nullable|boolean',
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

        // إذا تم تحديد قرية، تحقق إنه مفيش popup تانية لنفس القرية (غير الحالية)
        if ($request->village_id) {
            $exists = $this->popup
                ->where('village_id', $request->village_id)
                ->where('id', '!=', $id)
                ->exists();

            if ($exists) {
                return response()->json([
                    'errors' => 'This village already has a popup',
                ], 400);
            }
        }

        $image_path    = $this->update_image($request, $popup->image, 'image', 'images/popups');
        $ar_image_path = $this->update_image($request, $popup->ar_image, 'ar_image', 'images/popups');

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

        $popup->update([
            'status' => $request->status,
        ]);

        return response()->json([
            'success' => $request->status ? 'active' : 'banned',
        ]);
    }
}
