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
        ->with('owner', 'appartment', 'user')
        ->where('type', 'renter')
        ->whereNotNull('user_id')
        ->where('village_id', $request->user()->village_id)
        ->orderByDesc('id')
        ->get();

        return response()->json([
            'rents' => $rents,
        ]);
    }
}
