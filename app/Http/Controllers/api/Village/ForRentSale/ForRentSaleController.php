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
        ->with('owner', 'appartment')
        ->get();

        return response()->json([
            'offers' => $offers,
        ]);
    }

}
