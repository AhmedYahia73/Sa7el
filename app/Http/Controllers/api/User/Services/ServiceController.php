<?php

namespace App\Http\Controllers\api\User\Services;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\ServiceType;

class ServiceController extends Controller
{
    public function __construct(private ServiceType $services){}

    public function view(){
        $services = $this->services
        ->with(['providers'])
        ->get();

        return response()->json([
            'services' => $services
        ]);
    }
}
