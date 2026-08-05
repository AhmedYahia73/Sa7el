<?php

namespace App\Http\Controllers\api\User\rent;

use App\Http\Controllers\Controller;
use App\Models\Appartment;
use App\Models\AppartmentCode;
use App\Models\VillageSetting;
use App\Models\RentImage;
use App\trait\TraitImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class RentController extends Controller
{
    public function __construct(private AppartmentCode $appartment_code){}
    use TraitImage;

    public function view(Request $request){
        $validator = Validator::make($request->all(), [
            'village_id' => 'required|exists:villages,id',
            'appartment_id' => 'required|exists:appartments,id',
            'locale' => 'in:ar,en',
        ]);
        if ($validator->fails()) { // if Validate Make Error Return Message Error
            $firstError = $validator->errors()->first();
            return response()->json([
                'errors' => $firstError,
            ],400);
        }

        $appartment = Appartment::
        where('id', $request->appartment_id)
        ->first();
        if(empty($appartment) || !$appartment->rent_code_status || !$appartment->all_status){
            return response()->json([
                'errors' => $request->locale == "ar" ? 'محظور دخولك لهذه الشقة' : 'You are blocked to enter this appartment'
            ],400);
        }
        $rents = $this->appartment_code
        ->where('village_id', $request->village_id)
        ->where('appartment_id', $request->appartment_id)
        ->where('type', 'renter')
        ->where('to', '>', date('Y-m-d'))
        ->with('appartment')
        ->get()
        ->unique("code")
        ->values();

        return response()->json([
            'rents' => $rents
        ]);
    }

    public function create(Request $request){
        $validator = Validator::make($request->all(), [
            'appartment_id' => 'required|exists:appartments,id',
            'village_id' => 'required|exists:villages,id',
            'from' => 'required|date',
            'to' => 'required|date',
            'people' => 'required|integer',
            // 'image' => 'required',
            'image' => 'required|array',
            'image.*' => 'required',
            'locale' => 'in:ar,en',
        ]);
        if ($validator->fails()) { // if Validate Make Error Return Message Error
            $firstError = $validator->errors()->first();
            return response()->json([
                'errors' => $firstError,
            ],400);
        }

        $appartment = Appartment::
        where('id', $request->appartment_id)
        ->first();
        $renter_limit = VillageSetting::
        where("village_id", $request->user()->village_id)
        ->where("appartment_type_id", $appartment?->type_id)
        ->first()?->renter_limit ?? 10;
 
        if($renter_limit < $request->people){
            return response()->json([
                'errors' => $request->locale == "ar" ? ('عدد المستأجرين يجب أن يكون أقل من ' . $renter_limit) : ('renter must be less than ' . $renter_limit)
            ],400);
        }
        if(empty($appartment) || !$appartment->rent_code_status || !$appartment->all_status){
            return response()->json([
                'errors' => $request->locale == "ar" ? 'محظور دخولك لهذه الشقة' : 'You are blocked to enter this appartment'
            ],400);
        } 
        $from = $request->from; // تاريخ ووقت البداية الجديد
        $to = $request->to;     // تاريخ ووقت النهاية الجديد

        $appartment_code = $this->appartment_code
            ->where('appartment_id', $request->appartment_id) // شرط الشقة أساسي ومستقل
            ->where(function ($query) use ($from, $to) {
                // المعادلة السحرية لمنع أي تداخل فترات (أيام أو ساعات)
                $query->where('from', '<', $to)
                    ->where('to', '>', $from);
            })
            ->first();
        if (!empty($appartment_code)) {
            return response()->json([
                'errors' => $request->locale == "ar" ? ('الوحدة مؤجرة من ' . $appartment_code->from . ' إلى ' . $appartment_code->to) : ('Unit is rented from ' . $appartment_code->from . ' to ' . $appartment_code->to)
            ], 400);
        }
        $rentRequest = $validator->validated();
        $rentRequest['owner_id'] = $request->user()->id;
        $rentRequest['type'] = 'renter';
        do {
            $code = mt_rand(1000000, 9999999); // Always 7 digits
        } while ($this->appartment_code::where('code', $code)->exists()); 
        $rentRequest['code'] = $code;
        $rentRequest['image'] = [];

        foreach ($request->image as $item) {
            $image_path =$this->storeBase64Image($item, '/images/rent/id');
            $rentRequest['image'][] = $image_path;
        }  
        for($i = 0; $i < $request->people; $i++ ){
            $this->appartment_code
            ->create($rentRequest);
        }
       // /rent/add
        return response()->json([
            'success' => $code, 
        ]);
    }

    public function max_people(Request $request){
        $validator = Validator::make($request->all(), [
            'appartment_id' => 'required|exists:appartments,id',
            'locale' => 'in:ar,en',
        ]);
        if ($validator->fails()) { // if Validate Make Error Return Message Error
            $firstError = $validator->errors()->first();
            return response()->json([
                'errors' => $firstError,
            ],400);
        }

        $appartment = Appartment::
        where('id', $request->appartment_id)
        ->first();
        $renter_limit = VillageSetting::
        where("village_id", $request->user()->village_id)
        ->where("appartment_type_id", $appartment?->type_id)
        ->first()?->renter_limit ?? 10;
 
        return response()->json([
            'max_people' => $renter_limit 
        ],400);
    }

    public function destroy(Request $request){
        $validator = Validator::make($request->all(), [
            'appartment_id' => 'required|exists:appartments,id', 
            'code' => 'required',
            'locale' => 'in:ar,en',
        ]);
        if ($validator->fails()) { // if Validate Make Error Return Message Error
            $firstError = $validator->errors()->first();
            return response()->json([
                'errors' => $firstError,
            ],400);
        }
        
        $appartment_code = $this->appartment_code 
        ->where("appartment_id", $request->appartment_id)
        ->where('code', $request->code)  
        ->delete(); 

        return response()->json([
            'success' => $request->locale == "ar" ? "تم حذف الكود بنجاح" : "You delete code success"
        ]);
    }

    public function delete_user(Request $request){
        $validator = Validator::make($request->all(), [
            'appartment_id' => 'required|exists:appartments,id', 
            'code' => 'required',
            'user_id' => 'required|exists:users,id',
            'locale' => 'in:ar,en',
        ]);
        if ($validator->fails()) { // if Validate Make Error Return Message Error
            $firstError = $validator->errors()->first();
            return response()->json([
                'errors' => $firstError,
            ],400);
        }
        
        $code = AppartmentCode::
        where("code", $request->code)
        ->where("user_id", $request->user_id)
        ->where("appartment_id", $request->appartment_id)
        ->update([
            "user_id" => null
        ]);

        return response()->json([
            'success' => $request->locale == "ar" ? "تم حذف المستخدم بنجاح" : "You delete code success"
        ]);
    }

    public function push_rent_images(Request $request){
        $validator = Validator::make($request->all(), [
            'data' => 'required|array',
            'data.*.image' => 'required|base64image',
            'data.*.description' => 'required',
            'appartment_id' => "required|exists:appartments,id",
            'locale' => 'in:ar,en',
        ]);
        if ($validator->fails()) { // if Validate Make Error Return Message Error
            $firstError = $validator->errors()->first();
            return response()->json([
                'errors' => $firstError,
            ],400);
        }
          
        if(!$this->check_renter($request->appartment_id)){
            return response()->json([
                "errors" => "Unit is rented you can't delete"
            ], 400);
        }
        $data = $request->data;
        foreach ($data as $item) {
            $image_path = $this->storeBase64Image($item['image'], 'images/rents');
            $rent = RentImage::create([
                "image" => $image_path,
                "description" => isset($item['description']) ? $item['description'] : null,
                "appartment_id" => $request->appartment_id,
            ]); 
        }

        return response()->json([
            "success" => $request->locale == "ar" ? "تم إضافة البيانات بنجاح" : "You add data success"
        ]);
    }

    public function update_rent_images(Request $request, $id){
        $validator = Validator::make($request->all(), [
            'description' => 'required', 
            'locale' => 'in:ar,en',
        ]);
        if ($validator->fails()) { // if Validate Make Error Return Message Error
            $firstError = $validator->errors()->first();
            return response()->json([
                'errors' => $firstError,
            ],400);
        }
          
        $rent = RentImage::
        where("id", $id)
        ->findOrFail( $id);
        if(!$this->check_renter($rent->appartment_id)){
            return response()->json([
                "errors" => "Unit is rented you can't delete"
            ], 400);
        }
        $rent->update([ 
            "description" => $request->description,
        ]);

        return response()->json([
            "success" => $request->locale == "ar" ? "تم تحديث البيانات بنجاح" : "You update data success"
        ]);
    }

    public function delete_rent_images(Request $request, $id){
        $validator = Validator::make($request->all(), [  
            'locale' => 'in:ar,en',
        ]);
        if ($validator->fails()) { // if Validate Make Error Return Message Error
            $firstError = $validator->errors()->first();
            return response()->json([
                'errors' => $firstError,
            ],400);
        } 

        $rent = RentImage::
        findOrFail( $id);
        if(!$this->check_renter($rent->appartment_id)){
            return response()->json([
                "errors" => "Unit is rented you can't delete"
            ], 400);
        }
        $this->deleteImage($rent->image);
        $rent->delete();

        return response()->json([
            "success" => $request->locale == "ar" ? "تم حذف البيانات بنجاح" : "You delete data success"
        ]);
    }

    private function check_renter($appartment_id){
        $appartment_code = AppartmentCode::
        where("appartment_id", $appartment_id)
        ->where("approve_rent_images", true)
        ->where("to", ">=", now())
        ->first();
        if($appartment_code){
            return false;
        }
        return true;
    }
}
