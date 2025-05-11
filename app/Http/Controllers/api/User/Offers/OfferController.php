<?php

namespace App\Http\Controllers\api\User\Offers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

use App\Models\Offer;

class OfferController extends Controller
{
    public function __construct(private Offer $offers){}

    public function view(Request $request){
        $validator = Validator::make($request->all(), [
            'village_id' => 'required|exists:villages,id',
        ]);
        if ($validator->fails()) { // if Validate Make Error Return Message Error
            return response()->json([
                'errors' => $validator->errors(),
            ],400);
        }
        $rent = $this->offers
        ->select('id', 'village_id', 'owner_id', 'appartment_id', 'price_day', 'price_month')
        ->where('type', 'rent')
        ->where('village_id', $request->village_id)
        ->with('village:id,name', 'owner:id,name,email,phone', 'appartment:id,unit,image,number_floors')
        ->get();
        $sale = $this->offers
        ->select('id', 'village_id', 'owner_id', 'appartment_id', 'price')
        ->where('type', 'sale')
        ->where('village_id', $request->village_id)
        ->get();

        return response()->json([
            'rent' => $rent,
            'sale' => $sale,
        ]);
    }

    public function add_rent(Request $request){
        $validator = Validator::make($request->all(), [
            'village_id' => 'required|exists:villages,id',
            'appartment_id' => 'required|exists:appartments,id',
            'price_day' => 'required|numeric',
            'price_month' => 'required|numeric',
        ]);
        if ($validator->fails()) { // if Validate Make Error Return Message Error
            return response()->json([
                'errors' => $validator->errors(),
            ],400);
        }
        $rentRequest = $validator->validated();
        $rentRequest['owner_id'] = $request->user()->id;
        $rentRequest['type'] = 'rent';
        $this->offers->create($rentRequest);

        return response()->json([
            'success' => 'You add data success'
        ]);
    }

    public function add_sale(Request $request){
        $validator = Validator::make($request->all(), [
            'village_id' => 'required|exists:villages,id',
            'appartment_id' => 'required|exists:appartments,id',
            'price' => 'required|numeric',
        ]);
        if ($validator->fails()) { // if Validate Make Error Return Message Error
            return response()->json([
                'errors' => $validator->errors(),
            ],400);
        }
        $rentRequest = $validator->validated();
        $rentRequest['owner_id'] = $request->user()->id;
        $rentRequest['type'] = 'sale';
        $this->offers->create($rentRequest);

        return response()->json([
            'success' => 'You add data success'
        ]);
    }

    public function update_rent(Request $request, $id){
        $validator = Validator::make($request->all(), [
            'village_id' => 'required|exists:villages,id',
            'appartment_id' => 'required|exists:appartments,id',
            'price_day' => 'required|numeric',
            'price_month' => 'required|numeric',
        ]);
        if ($validator->fails()) { // if Validate Make Error Return Message Error
            return response()->json([
                'errors' => $validator->errors(),
            ],400);
        }
        $rentRequest = $validator->validated();
        $this->offers
        ->where('id', $id)
        ->update($rentRequest);

        return response()->json([
            'success' => 'You update data success'
        ]);
    }

    public function update_sale(Request $request, $id){
        $validator = Validator::make($request->all(), [
            'village_id' => 'required|exists:villages,id',
            'appartment_id' => 'required|exists:appartments,id',
            'price' => 'required|numeric',
        ]);
        if ($validator->fails()) { // if Validate Make Error Return Message Error
            return response()->json([
                'errors' => $validator->errors(),
            ],400);
        }
        $saleRequest = $validator->validated();
        $this->offers
        ->where('id', $id)
        ->update($saleRequest);

        return response()->json([
            'success' => 'You update data success'
        ]);
    }

    public function delete(Request $request, $id){
        $this->offers
        ->where('id', $id)
        ->delete();

        return response()->json([
            'success' => 'You delete success'
        ]);
    }
}
