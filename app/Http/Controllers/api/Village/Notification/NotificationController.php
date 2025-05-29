<?php

namespace App\Http\Controllers\api\Village\Notification;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

use App\Models\Maintenance;
use App\Models\ProblemReport;

class NotificationController extends Controller
{
    public function __construct(private Maintenance $maintenance,
    private ProblemReport $problem_report){}
    
    public function notification(Request $request){
        $validator = Validator::make($request->all(), [
            'maintenance' => 'required|numeric',
            'problem_report' => 'required|numeric',
        ]);
        if ($validator->fails()) { // if Validate Make Error Return Message Error
            return response()->json([
                'errors' => $validator->errors(),
            ],400);
        }
        $maintenance = $this->maintenance
        ->where('village_id', $request->user()->village_id)
        ->count();
        $problem_report = $this->problem_report
        ->where('village_id', $request->user()->village_id)
        ->count();

        return response()->json([
            'maintenance_notification' => $maintenance - $request->maintenance,
            'problem_report_notification' => $problem_report - $request->problem_report,
            'new_maintenance' => $maintenance,
            'new_problem_report' => $problem_report,
        ]);
    }
}
