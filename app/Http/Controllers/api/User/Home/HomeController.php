<?php

namespace App\Http\Controllers\api\User\Home;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Village;

class HomeController extends Controller
{
    public function village($id){
        $village = Village::
        where("id", $id)
        ->first();

        return response()->json([
            "logo" => $village->logo_link,
            "logo" => $village->name,
        ]);
    }
}
