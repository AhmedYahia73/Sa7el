<?php

namespace App\Http\Controllers\api\Village\Appartments;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

use App\Models\Appartment;
use App\Models\AppartmentCode;

class AppartmentProfileController extends Controller
{
    public function __construct(private Appartment $appartment,
    private AppartmentCode $appartment_code){}

    public function profile_unit(Request $request, $id){
        $appartment = $this->appartment
        ->where('id', $id)
        ->first();
        $owners = $appartment->appartment_code
        ->where('type', 'owner')->values()
        ->map(function($item){
            return [
                'id' => $item->id,
                'name' => $item?->user?->name,
                'email' => $item?->user?->email,
                'phone' => $item?->user?->phone,
                'user_type' => $item->user_type,
                'image' => $item?->user?->image_link,
            ];
        });
        $renters = $appartment->appartment_code
        ->where('type', 'renter')->values()
        ->map(function($item){
            return [
                'id' => $item->id,
                'rent_from' => $item->from,
                'rent_to' => $item->to,
                'user_type' => $item->user_type,
                'renter_name' => $item?->user?->name,
                'renter_email' => $item?->user?->email,
                'renter_phone' => $item?->user?->phone,
                'renter_image' => $item?->user?->image_link,
                'owner_name' => $item?->owner?->name,
                'owner_email' => $item?->owner?->email,
                'owner_phone' => $item?->owner?->phone,
                'owner_image' => $item?->owner?->image_link,
            ];
        });

        return response()->json([
            'appartment' => $appartment,
            'owners' => $owners,
            'renters' => $renters,
        ]);
    }

    public function update_user_type(Request $request, $id){
        $validator = Validator::make($request->all(), [
            'user_type' => ['required', 'in:follower,super'],  
        ]);
        if ($validator->fails()) { // if Validate Make Error Return Message Error
            return response()->json([
                'errors' => $validator->errors(),
            ],400);
        }
        
        $appartment_code = $this->appartment_code
        ->where('id', $id)
        ->first();
        if ($request->user_type == 'super') {
            $this->appartment_code
            ->where('code', $appartment_code->code)
            ->whereNotNull('code')
            ->update([
                'user_type' => 'follower'
            ]);
        }
        $appartment_code->update([
            'user_type' => $request->user_type
        ]);

        return response()->json([
            'success' => 'You update data success'
        ]);
    }
}
