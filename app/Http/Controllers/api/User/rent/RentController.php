<?php

namespace App\Http\Controllers\api\User\rent;

use App\Http\Controllers\Controller;
use App\Models\Appartment;
use App\Models\AppartmentCode;
use App\Models\VillageSetting;
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
                'errors' => 'You are blocked to enter this appartment'
            ],400);
        }
        $rents = $this->appartment_code
        ->where('village_id', $request->village_id)
        ->where('appartment_id', $request->appartment_id)
        ->where('owner_id', $request->user()->id)
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
            'image' => 'required',
            // 'image' => 'required|array',
            // 'image.*' => 'required',
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
                'errors' => 'renter must be less than ' . $renter_limit 
            ],400);
        }
        if(empty($appartment) || !$appartment->rent_code_status || !$appartment->all_status){
            return response()->json([
                'errors' => 'You are blocked to enter this appartment'
            ],400);
        } 
        $appartment_code = $this->appartment_code
        ->where('from', '<=', $request->from)
        ->where('to', '>=', $request->from)
        ->where('to', '<=', $request->to)
        ->where("appartment_id", $request->appartment_id)
        ->orWhere('from', '>=', $request->from)
        ->where('to', '<=', $request->to)
        ->where("appartment_id", $request->appartment_id)
        ->orWhere('from', '>=', $request->from)
        ->where('from', '<=', $request->to)
        ->where('to', '>=', $request->to)
        ->where("appartment_id", $request->appartment_id)
        ->first();
        if (!empty($appartment_code)) {
            return response()->json([
                'errors' => 'Unit is rented from ' . $appartment_code->from . 
                ' to ' . $appartment_code->to
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

        // foreach ($request->image as $item) {
        //     $image_path =$this->storeBase64Image($item, '/images/rent/id');
        //     $rentRequest['image'][] = $image_path;
        // }
        $image_path =$this->upload($request, "image", '/images/rent/id');
        $rentRequest['image'] = [$image_path];
        for($i = 0; $i < $request->people; $i++ ){
            $this->appartment_code
            ->create($rentRequest);
        }
       // /rent/add
        return response()->json([
            'success' => $code
        ]);
    }

    public function max_people(Request $request){
        $validator = Validator::make($request->all(), [
            'appartment_id' => 'required|exists:appartments,id',
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
            'code' => 'required'
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
            'success' => "You delete code success"
        ]);
    }

    public function delete_user(Request $request){
        $validator = Validator::make($request->all(), [
            'appartment_id' => 'required|exists:appartments,id', 
            'code' => 'required',
            'user_id' => 'required|exists:users,id',
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
            'success' => "You delete code success"
        ]);
    }
}
