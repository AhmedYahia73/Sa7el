<?php

namespace App\Http\Controllers\api\Village\Maintenance;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

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
            'completed' => $completed,
            'pending' => $pending,
        ]);
    }

    public function status(Request $request, $id){
       $validator = Validator::make($request->all(), [
            'status' => 'required|boolean',
        ]);
        if ($validator->fails()) { // if Validate Make Error Return Message Error
            return response()->json([
                'errors' => $validator->errors(),
            ],400);
        }

        $maintenance = $this->maintenance
        ->where('village_id', $request->user()->village_id)
        ->where('id', $id)
        ->update([
            'status' => $request->status,
        ]);

        return response()->json([
            'success' => $request->status ? 'completed': 'faild'
        ]);
    }
}
