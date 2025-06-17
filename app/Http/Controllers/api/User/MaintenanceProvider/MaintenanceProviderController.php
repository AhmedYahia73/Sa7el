<?php

namespace App\Http\Controllers\api\User\MaintenanceProvider;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\ServiceProvider;

class MaintenanceProviderController extends Controller
{
    public function __construct(private ServiceProvider $service_provider){}

    public function view(Request $request){
        $service_providers = $this->service_provider
        ;
    }
}
