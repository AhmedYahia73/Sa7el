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
        ->get();
 
        $visitor_visits_village_count = [
            'guest' => $visits_village->where('type', 'visitor')
            ->where('visitor_type', 'guest')->count(),
            'worker' => $visits_village->where('type', 'visitor')
            ->where('visitor_type', 'worker')->count(),
            'delivery' => $visits_village->where('type', 'visitor')
            ->where('visitor_type', 'delivery')->count(),
        ];
        $owner_visits_village = $visits_village->where('type', 'owner')
        ->count();
        $renter_visits_village = $visits_village->where('type', 'renter')
        ->count();

        return response()->json([
            'units_count' => $units_count,
            'maintenance_request_count' => $maintenance_request_count,
            'problem_report_count' => $problem_report_count,
            'rents_count' => $rents_count,
            'users_beach' => $users_beach,
            'users_pool' => $users_pool,
            'visits_village' => $visits_village,
            'owner_visits_village_count' => $owner_visits_village,
            'visitor_visits_village_count' => $visitor_visits_village_count,
            'renter_visits_village_count' => $renter_visits_village,
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
        ->where('village_id', $request->user()->village_id)
        ->with(['user:id,name,email,phone', 'beach:id,name']);
        $users_pool = $this->user_pool
        ->where('village_id', $request->user()->village_id)
        ->with(['user:id,name,email,phone', 'pool:id,name']);
        $visits_village = $this->visit_village
        ->where('village_id', $request->user()->village_id)
        ->with(['user:id,name,email,phone', 'gate:id,name', 
        'appartment:id,unit,location']);
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
        $visitor_visits_village = [
            'guest' => $visits_village->where('type', 'visitor')
            ->where('visitor_type', 'guest')->values(),
            'worker' => $visits_village->where('type', 'visitor')
            ->where('visitor_type', 'worker')->values(),
            'delivery' => $visits_village->where('type', 'visitor')
            ->where('visitor_type', 'delivery')->values(),
        ];
        $visitor_visits_village_count = [
            'guest' => $visits_village->where('type', 'visitor')
            ->where('visitor_type', 'guest')->count(),
            'worker' => $visits_village->where('type', 'visitor')
            ->where('visitor_type', 'worker')->count(),
            'delivery' => $visits_village->where('type', 'visitor')
            ->where('visitor_type', 'delivery')->count(),
        ];
        $owner_visits_village = $visits_village->where('type', 'owner')
        ->values();
        $renter_visits_village = $visits_village->where('type', 'renter')
        ->values();

        return response()->json([ 
            'users_beach' => $users_beach,
            'users_pool' => $users_pool,
            'owner_visits_village' => $owner_visits_village,
            'renter_visits_village' => $renter_visits_village,
            'visitor_visits_village' => $visitor_visits_village,
            'users_beach_count' => $users_beach->count(),
            'users_pool_count' => $users_pool->count(),
            'owner_visits_village_count' => $owner_visits_village->count(),
            'visitor_visits_village_count' => $visitor_visits_village_count,
            'renter_visits_village_count' => $renter_visits_village->count(),
        ]);
    }
}
