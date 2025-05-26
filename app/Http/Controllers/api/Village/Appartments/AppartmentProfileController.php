<?php

namespace App\Http\Controllers\api\Village\Appartments;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Appartment;

class AppartmentProfileController extends Controller
{
    public function __construct(private Appartment $appartment){}

    public function profile_unit(Request $request, $id){
        $appartment = $this->appartment
        ->where('id', $id)
        ->first();
        $owners = $appartment->appartment_code
        ->where('type', 'owner')->values()
        ->map(function($item){
            return [
                'name' => $item?->user?->name,
                'email' => $item?->user?->email,
                'phone' => $item?->user?->phone,
                'image' => $item?->user?->image_link,
            ];
        });
        $renters = $appartment->appartment_code
        ->where('type', 'renter')->values()
        ->map(function($item){
            return [
                'rent_from' => $item->from,
                'rent_to' => $item->to,
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
}
