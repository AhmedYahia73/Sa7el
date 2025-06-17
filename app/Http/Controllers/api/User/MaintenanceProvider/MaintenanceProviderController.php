<?php

namespace App\Http\Controllers\api\User\MaintenanceProvider;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

use App\Models\MaintenanceType;

class MaintenanceProviderController extends Controller
{
    public function __construct(private MaintenanceType $maintenance_type){}

    public function view(Request $request){
        $maintenance_type = $this->maintenance_type
        ->with('maintenance_provider')
        ->where('status', 1)
        ->get();

        return response()->json([
            'maintenance_type' => $maintenance_type
        ]);
    }
}
