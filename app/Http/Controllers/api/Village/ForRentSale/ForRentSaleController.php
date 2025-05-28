<?php

namespace App\Http\Controllers\api\Village\ForRentSale;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request; 

use App\Models\Offer;

class ForRentSaleController extends Controller
{
    public function __construct(private Offer $offers){}

    public function view(Request $request){
        $offers = $this->offers
        ->whereHas('offer_status', function($query){
            $query->where('rent_status', 1)
            ->orWhere('sale_status', 1);
        })
        ->where('village_id', $request->user()->village_id)
        ->with('owner', 'appartment')
        ->get()
        ->map(function($item){
            $type_offer = null;
            if ($item->offer_status->sale_status && $item->offer_status->rent_status) {
                $type_offer = 'Sale & Rent';
            }
            elseif ($item->offer_status->sale_status) {
                $type_offer = 'Sale';
            }
            elseif ($item->offer_status->rent_status) {
                $type_offer = 'Rent';
            }
            return [
                'id' => $item->id,
                'village' => $item?->village?->name,
                'image' => $item?->village?->image_link,
                'cover_image' => $item?->village?->cover_image_link,
                'owner' => $item?->owner?->name,
                'owner_phone' => $item?->owner?->phone,
                'unit' => $item?->appartment?->unit,
                'unit' => $item?->appartment?->unit,
                'description' => $item->description,
                'type_offer' => $type_offer,
                'price_day' => $item->price_day,
                'price_month' => $item->price_month,
                'price' => $item->price,
            ];
        });

        return response()->json([
            'offers' => $offers,
        ]);
    }

}
