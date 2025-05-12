<?php

namespace App\Http\Controllers\api\User\rent;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\trait\image;

use App\Models\AppartmentCode;

class RentController extends Controller
{
    public function __construct(private AppartmentCode $appartment_code){}
    use image;

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

        $rents = $this->appartment_code
        ->where('village_id', $request->village_id)
        ->where('appartment_id', $request->appartment_id)
        ->where('user_id', $request->user()->id)
        ->with('appartment')
        ->get();

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
            'image' => 'required'
        ]);
        if ($validator->fails()) { // if Validate Make Error Return Message Error
            $firstError = $validator->errors()->first();
            return response()->json([
                'errors' => $firstError,
            ],400);
        }
        $rentRequest = $validator->validated();
        $rentRequest['user_id'] = $request->user()->id;
        $rentRequest['type'] = 'renter';
        $image_path =$this->storeBase64Image($request->image, '/images/rent/id');
        $rentRequest['image'] = $image_path;
        do {
            $code = mt_rand(1000000, 9999999); // Always 7 digits
        } while ($this->appartment_code::where('code', $code)->exists()); 
        $rentRequest['code'] = $code;
        

        $this->appartment_code
        ->create($rentRequest);
        
        return response()->json([
            'success' => $code
        ]);
    }
}
