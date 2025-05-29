<?php

namespace App\Http\Controllers\api\Village\Notification;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Maintenance;
use App\Models\ProblemReport;

class NotificationController extends Controller
{
    public function __construct(private MaintenanceType $maintenance_type,
    private ProblemReport $problem_report){}
    
    public function notification(Request $request){

    }
}
