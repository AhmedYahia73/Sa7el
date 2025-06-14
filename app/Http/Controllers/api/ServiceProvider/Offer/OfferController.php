<?php

namespace App\Http\Controllers\api\ServiceProvider\Offer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\SuperAdmin\MallRequest;
use Illuminate\Support\Facades\Validator;
use App\trait\TraitImage;

use App\Models\ProviderOffer; 

class OfferController extends Controller
{
    public function __construct(private ProviderOffer $provider_offer){}
    use TraitImage;

    public function view(){ 
        $provider_offer = $this->provider_offer
        ->get();

        return response()->json([ 
            'provider_offer' => $provider_offer,
        ]);
    }

    public function status(Request $request, $id){
        $validator = Validator::make($request->all(), [
            'status' => 'required|boolean',
        ]);
        if ($validator->fails()) { // if Validate Make Error Return Message Error
            return response()->json([
                'errors' => $validator->errors(),
            ],400);
        }
        
        $provider_offer = $this->provider_offer
        ->where('id', $id)
        ->update([
            'status' => $request->status
        ]);

        return response()->json([
            'success' => $request->status ? 'active' : 'banned'
        ]);
    }

    public function create(Request $request){
        $validator = Validator::make($request->all(), [
            'description' => 'sometimes',
            'image' => 'required',
            'status' => 'required|boolean',
        ]);
        if ($validator->fails()) { // if Validate Make Error Return Message Error
            return response()->json([
                'errors' => $validator->errors(),
            ],400);
        }

        $provider_offer = $this->provider_offer
        ->where('provider_id', $request->user()->provider_id)
        ->get();
        if (count($provider_offer) > 2) {
            return response()->json([
                'errors' => 'You add more than 3 offers you should know your limit 3 offers'
            ], 400);
        }
        $offerRequest = $request->validated();
        $offerRequest['provider_id'] = $request->user()->provider_id;
        if (!is_string($request->image)) {
            $image_path = $this->upload($request, 'image', 'provider/images/offer');
            $offerRequest['image'] = $image_path;
        }
        $provider_offer = $this->provider_offer
        ->create($offerRequest);

        return response()->json([
            'success' => 'You add data success'
        ]);
    }

    public function modify(Request $request, $id){
        $offerRequest = $request->validated();
        $provider_offer = $this->provider_offer
        ->where('id', $id)
        ->first();
        if (empty($provider_offer)) {
            return response()->json([
                'errors' => 'offer not found'
            ], 400);
        }
        if (!is_string($request->image)) {
            $image_path = $this->update_image($request, $provider_offer->image, 'image', 'provider/images/offer');
            $offerRequest['image'] = $image_path;
        }
        $provider_offer
        ->update($offerRequest);

        return response()->json([
            'success' => 'You update data success'
        ]);
    }

    public function delete($id){
        $provider_offer = $this->provider_offer
        ->where('id', $id)
        ->first();
        if (empty($provider_offer)) {
            return response()->json([
                'errors' => 'offer not found'
            ], 400);
        }
        $this->deleteImage($provider_offer->image);
        $provider_offer->delete();

        return response()->json([
            'success' => 'You delete data success'
        ]);
    }

}
