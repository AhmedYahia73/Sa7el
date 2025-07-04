<?php

namespace App\Http\Controllers\api\User\Offers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\trait\TraitImage;

use App\Models\Offer;
use App\Models\OfferImage;
use App\Models\OfferStatus;

class OfferController extends Controller
{
    public function __construct(private Offer $offers, private OfferImage $offer_image,
    private OfferStatus $offer_status){}
    use TraitImage;

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
        ->with('village:id,name', 'owner:id,name,email,phone', 'appartment:id,unit,location')
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
        $offer_status = $this->offer_status
        ->where('appartment_id', $request->appartment_id)
        ->first();
        $rent_status = $offer_status?->status_offer == 'rent' ? 1: 0;
        $sale_status = $offer_status?->status_offer == 'sale' ? 1: 0;

        return response()->json([
            'offer_images' => $offer_image,
            'rent_status' => $rent_status,
            'sale_status' => $sale_status,
        ]);
    }

    public function offer_status(Request $request, $id){
        $validator = Validator::make($request->all(), [
            'status' => 'required|boolean',
        ]);
        if ($validator->fails()) { // if Validate Make Error Return Message Error
            return response()->json([
                'errors' => $validator->errors(),
            ],400);
        }
        $offers = $this->offers
        ->where('id', $id)
        ->update([
            'status' => $request->status
        ]);
        

        return response()->json([
            'success' => $request->status ? 'active' : 'banned',
        ]);
    }

    public function appartment_offer(Request $request){
        $validator = Validator::make($request->all(), [
            'appartment_id' => 'required|exists:appartments,id',
        ]);
        if ($validator->fails()) { // if Validate Make Error Return Message Error
            return response()->json([
                'errors' => $validator->errors(),
            ],400);
        } 
        $rent = $this->offers
        ->where('type', 'rent')
        ->where('appartment_id', $request->appartment_id)
        ->where('owner_id', $request->user()->id)
        ->orderByDesc('id')
        ->first();
        $sale = $this->offers
        ->where('type', 'sale')
        ->where('appartment_id', $request->appartment_id)
        ->where('owner_id', $request->user()->id)
        ->orderByDesc('id')
        ->first(); 

        return response()->json([ 
            'rent' => $rent,
            'sale' => $sale,
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
