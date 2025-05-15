<?php

namespace App\Http\Controllers\api\User\Offers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\trait\image;

use App\Models\Offer;
use App\Models\OfferImage;

class OfferController extends Controller
{
    public function __construct(private Offer $offers, private OfferImage $offer_image){}
    use image;

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
        ->select('id', 'village_id', 'owner_id', 'appartment_id', 'price_day', 'price_month', 'description')
        ->where('type', 'rent')
        ->where('village_id', $request->village_id)
        ->with('village:id,name', 'owner:id,name,email,phone', 'appartment:id,unit,image,number_floors')
        ->get();
        $sale = $this->offers
        ->select('id', 'village_id', 'owner_id', 'appartment_id', 'price', 'description')
        ->where('type', 'sale')
        ->where('village_id', $request->village_id)
        ->get();

        return response()->json([
            'rent' => $rent,
            'sale' => $sale,
        ]);
    }

    public function appartment_image(Request $request){
        $validator = Validator::make($request->all(), [
            'appartment_id' => 'required|exists:appartments,id',
        ]);
        if ($validator->fails()) { // if Validate Make Error Return Message Error
            return response()->json([
                'errors' => $validator->errors(),
            ],400);
        }
        $offer_image = $this->offer_image
        ->where('appartment_id', $request->appartment_id)
        ->get();
        $offers = $this->offers
        ->where('appartment_id', $request->appartment_id)
        ->where('owner_id', $request->user()->id)
        ->orderByDesc('id')
        ->first();
        $rent_status = $offers?->status_offer == 'rent' ? 1: 0;
        $sale_status = $offers?->status_offer == 'sale' ? 1: 0;

        return response()->json([
            'offer_images' => $offer_image,
            'rent_status' => $rent_status,
            'sale_status' => $sale_status,
        ]);
    }

    public function offer_status(Request $request, $id){
        $validator = Validator::make($request->all(), [
            'status_offer' => 'required|in:rent,sale',
        ]);
        if ($validator->fails()) { // if Validate Make Error Return Message Error
            return response()->json([
                'errors' => $validator->errors(),
            ],400);
        }
        $offers = $this->offers
        ->where('id', $id)
        ->update([
            'status_offer' => $request->status_offer
        ]);

        return response()->json([
            'success' => $request->status_offer,
        ]);
    }

    public function appartment(Request $request){
        $validator = Validator::make($request->all(), [
            'appartment_id' => 'required|exists:appartments,id',
            'type' => 'in:rent,sale|required'
        ]);
        if ($validator->fails()) { // if Validate Make Error Return Message Error
            return response()->json([
                'errors' => $validator->errors(),
            ],400);
        }
        $offers = $this->offers
        ->where('type', $request->type)
        ->where('appartment_id', $request->appartment_id)
        ->where('owner_id', $request->user()->id)
        ->orderByDesc('id')
        ->first();

        return response()->json([
            'offer' => $offers,
        ]);
    }

    public function upload_appartment_image(Request $request){
        $validator = Validator::make($request->all(), [
            'appartment_id' => 'required|exists:appartments,id',
            'village_id' => 'required|exists:villages,id',
            'image' => 'required'
        ]);
        if ($validator->fails()) { // if Validate Make Error Return Message Error
            return response()->json([
                'errors' => $validator->errors(),
            ],400);
        }

        $image_path =$this->storeBase64Image($request->image, '/images/offer/appartment');
        
        $offer_image = $this->offer_image
        ->create([
            'village_id' => $request->village_id,
            'owner_id' => $request->user()->id,
            'appartment_id' => $request->appartment_id,
            'image' => $image_path,
        ]);

        return response()->json([
            'success' => 'you add data success',
        ]);
    }

    public function add_rent(Request $request){
        $validator = Validator::make($request->all(), [
            'village_id' => 'required|exists:villages,id',
            'appartment_id' => 'required|exists:appartments,id',
            'price_day' => 'required|numeric',
            'price_month' => 'required|numeric',
            'description' => 'sometimes',
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
            'description' => 'sometimes',
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
            'description' => 'sometimes',
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
            'description' => 'sometimes',
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
