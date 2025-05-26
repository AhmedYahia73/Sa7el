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
    }
}
