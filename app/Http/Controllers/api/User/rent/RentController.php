<?php

namespace App\Http\Controllers\api\User\rent;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\trait\TraitImage;

use App\Models\AppartmentCode;

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

        $rents = $this->appartment_code
        ->where('village_id', $request->village_id)
        ->where('appartment_id', $request->appartment_id)
        ->where('owner_id', $request->user()->id)
        ->where('type', 'renter')
        ->where('to', '>', date('Y-m-d'))
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
        $appartment_code = $this->appartment_code
        ->where('from', '<=', $request->from)
        ->where('to', '>=', $request->from)
        ->where('to', '<=', $request->to)
        ->orWhere('from', '>=', $request->from)
        ->where('to', '<=', $request->to)
        ->orWhere('from', '>=', $request->from)
        ->where('from', '<=', $request->to)
        ->where('to', '>=', $request->to)
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
