<?php

namespace App\Http\Controllers\api\Village\Maintenance;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Maintenance;

class MaintenanceController extends Controller
{
    public function __construct(private Maintenance $maintenance){}

    public function view(Request $request){
        $maintenance = $this->maintenance
        ->where('village_id', $request->user()->village_id)
        ->with('maintenance_type', 'appartment', 'user')
        ->get();
        $completed = $maintenance
        ->where('status', 1)
        ->values();
        $pending = $maintenance
        ->where('status', 0)
        ->values();

        return response()->json([
            'completed' => $completedm,
            'pending' => $pending,
        ]);
    }
}
