<?php

namespace App\Http\Controllers\api\Village\Rent;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\AppartmentCode;

class RentController extends Controller
{
    public function __construct(private AppartmentCode $rents){}

    public function view(Request $request){
        $rents = $this->rents
        ->with('owner', 'appartment')
        ->where('type', 'renter')
        ->get();

        return response()->json([
            'rents' => $rents,
        ]);
    }
}
