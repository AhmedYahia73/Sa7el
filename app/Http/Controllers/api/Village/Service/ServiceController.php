<?php

namespace App\Http\Controllers\api\Village\Service;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Provider;

class ServiceController extends Controller
{
    public function __construct(private Provider $provider){}

    public function view(Request $request){
        $services = $this->provider
        ->with('service')
        ->where('village_id', $request->user()->id)
        ->get();

        return response()->json([
            'services' => $services,
        ]);
    }
}
