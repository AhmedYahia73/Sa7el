<?php

namespace App\Http\Controllers\api\Village\Home;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

use App\Models\Appartment;
use App\Models\Maintenance;
use App\Models\ProblemReport;
use App\Models\AppartmentCode;
use App\Models\UserBeach;
use App\Models\UserPool;
use App\Models\VisitVillage;

class HomeController extends Controller
{
    public function __construct(private Appartment $units,
    private Maintenance $maintenance_request, private ProblemReport $problem_report,
    private AppartmentCode $rent, private UserBeach $user_beach, 
    private UserPool $user_pool, private VisitVillage $visit_village){}

    public function view(Request $request){
        $units_count = $this->units
        ->where('village_id', $request->user()->village_id)
        ->count();
        $maintenance_request_count = $this->maintenance_request
        ->where('village_id', $request->user()->village_id)
        ->count();
        $problem_report_count = $this->problem_report
        ->where('village_id', $request->user()->village_id)
        ->count();
        $rents_count = $this->rent
        ->where('village_id', $request->user()->village_id)
        ->where('from', '<=', date('Y-m-d'))
        ->where('to', '>=', date('Y-m-d'))
        ->distinct('code')
        ->count('code');
        $users_beach = $this->user_beach
        ->where('village_id', $request->user()->village_id)
        ->whereDate('updated_at', date('Y-m-d'))
        ->count();
        $users_pool = $this->user_pool
        ->where('village_id', $request->user()->village_id)
        ->whereDate('updated_at', date('Y-m-d'))
        ->count();
        $visits_village = $this->visit_village
        ->where('village_id', $request->user()->village_id)
        ->whereDate('updated_at', date('Y-m-d'))
        ->count();

        return response()->json([
            'units_count' => $units_count,
            'maintenance_request_count' => $maintenance_request_count,
            'problem_report_count' => $problem_report_count,
            'rents_count' => $rents_count,
            'users_beach' => $users_beach,
            'users_pool' => $users_pool,
            'visits_village' => $visits_village,
        ]);
    }

    public function filter(Request $request){
        $validator = Validator::make($request->all(), [
            'date_from' => 'date',
            'date_to' => 'date',
        ]);
        if ($validator->fails()) { // if Validate Make Error Return Message Error
            return response()->json([
                'errors' => $validator->errors(),
            ],400);
        }

        $users_beach = $this->user_beach
        ->where('village_id', $request->user()->village_id);
        $users_pool = $this->user_pool
        ->where('village_id', $request->user()->village_id);
        $visits_village = $this->visit_village
        ->where('village_id', $request->user()->village_id);
        if ($request->date_from) {
            $users_beach = $users_beach
            ->whereDate('updated_at', '>=', $request->date_from);
            $users_pool = $users_pool
            ->whereDate('updated_at', '>=', $request->date_from);
            $visits_village = $visits_village
            ->whereDate('updated_at', '>=', $request->date_from);
        }
        if ($request->date_to) {
            $users_beach = $users_beach
            ->whereDate('updated_at', '>=', $request->date_to);
            $users_pool = $users_pool
            ->whereDate('updated_at', '>=', $request->date_to);
            $visits_village = $visits_village
            ->whereDate('updated_at', '>=', $request->date_to);
        }
        $users_beach = $users_beach
        ->get();
        $users_pool = $users_pool
        ->get();
        $visits_village = $visits_village
        ->get();

        return response()->json([ 
            'users_beach' => $users_beach,
            'users_pool' => $users_pool,
            'visits_village' => $visits_village,
            
            'users_beach_count' => $users_beach->count(),
            'users_pool_count' => $users_pool->count(),
            'visits_village_count' => $visits_village->count(),
        ]);
    }
}
