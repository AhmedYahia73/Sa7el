<?php

namespace App\Http\Controllers\api\Village\Notification;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

use App\Models\Maintenance;
use App\Models\ProblemReport;
use App\Models\Notification;

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

    public function notification_items(Request $request){
        $notifications = Notification::
        where("type", "admin")
        ->where("village_id", $request->user()->village_id)
        ->paginate(10)
        ->through(function($item){
            return [
                "id" => $item->id,
                "notification" => $item->notification,
                "is_read" => $item->is_read,
            ];
        });

        return response()->json([
            "notifications" => $notifications
        ]);
    }

    public function is_read(Request $request){
        $validator = Validator::make($request->all(), [
            'items' => 'required|array', 
            'items.*' => 'exists:notifications,id', 
        ]);
        if ($validator->fails()) { // if Validate Make Error Return Message Error
            return response()->json([
                'errors' => $validator->errors(),
            ],400);
        }

        Notification::
        whereIn("id", $request->items)
        ->where("village_id", $request->user()->village_id)
        ->update([
            "is_read" => true
        ]);

        return response()->json([
            "success" => "You update data success"
        ]);
    }
}
