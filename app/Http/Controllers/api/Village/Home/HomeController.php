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
use Carbon\Carbon;

class HomeController extends Controller
{
    public function __construct(private Appartment $units,
    private Maintenance $maintenance_request, private ProblemReport $problem_report,
    private AppartmentCode $rent, private UserBeach $user_beach, 
    private UserPool $user_pool, private VisitVillage $visit_village){}

    public function view(Request $request)
    {
        $villageId = $request->user()->village_id;
        $today = now()->today();

        $units_count = $this->units
            ->where('village_id', $villageId)
            ->count();

        $maintenance_request_count = $this->maintenance_request
            ->where('village_id', $villageId)
            ->count();

        $problem_report_count = $this->problem_report
            ->where('village_id', $villageId)
            ->count();

        $rents_count = $this->rent
            ->where('village_id', $villageId)
            ->where('from', '<=', date('Y-m-d'))
            ->where('to', '>=', date('Y-m-d'))
            ->distinct('code')
            ->count('code');

        $users_beach = $this->user_beach
            ->where('village_id', $villageId)
            ->whereDate('updated_at', $today)
            ->count();

        $users_pool = $this->user_pool
            ->where('village_id', $villageId)
            ->whereDate('updated_at', $today)
            ->count();

        $visits_village = $this->visit_village
            ->where('village_id', $villageId)
            ->whereDate('updated_at', $today)
            ->get();

        $visitor_visits_village_count = [
            'guest'    => $visits_village->where('type', 'visitor')->where('visitor_type', 'guest')->count(),
            'worker'   => $visits_village->where('type', 'visitor')->where('visitor_type', 'worker')->count(),
            'delivery' => $visits_village->where('type', 'visitor')->where('visitor_type', 'delivery')->count(),
        ];

        $owner_visits_village = $visits_village->where('type', 'owner')->count();
        $renter_visits_village = $visits_village->where('type', 'renter')->count();

        return response()->json([
            'units_count'                  => $units_count,
            'maintenance_request_count'    => $maintenance_request_count,
            'problem_report_count'         => $problem_report_count,
            'rents_count'                  => $rents_count,
            'users_beach'                  => $users_beach,
            'users_pool'                   => $users_pool,
            'visits_village'               => $visits_village,
            'owner_visits_village_count'   => $owner_visits_village,
            'visitor_visits_village_count' => $visitor_visits_village_count,
            'total_visits_count'           => $visits_village->count(),
        ]);
    }

    /**
     * Trait / Helper Function لتطبيق الفلاتر والبحث لتجنب تكرار الكود
     */
    private function getFilteredVisits(Request $request, string $type, ?string $visitorType = null)
    {
        $search = $request->input('search');

        return $this->visit_village
            ->where('village_id', $request->user()->village_id)
            ->where('type', $type)
            ->when($visitorType, fn($q) => $q->where('visitor_type', $visitorType))
            ->when(!$request->date_from && !$request->date_to, function ($q) {
                // تطبيق فلتر اليوم فقط في حالة عدم تحديد تاريخ تاريخ للبحث
                $q->whereDate('updated_at', now()->today());
            })
            ->when($request->date_from, fn($q) => $q->whereDate('updated_at', '>=', $request->date_from))
            ->when($request->date_to, fn($q) => $q->whereDate('updated_at', '<=', $request->date_to))
            ->with(['user', 'appartment', 'gate'])
            ->when($search, function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->whereHas('user', function ($qUser) use ($search) {
                        $qUser->where('name', 'like', "%{$search}%")
                            ->orWhere('email', 'like', "%{$search}%")
                            ->orWhere('phone', 'like', "%{$search}%");
                    })
                    ->orWhereHas('appartment', fn($qApp) => $qApp->where('unit', 'like', "%{$search}%"))
                    ->orWhereHas('gate', fn($qGate) => $qGate->where('name', 'like', "%{$search}%"));
                });
            })
            ->latest('updated_at')
            ->paginate(15)
            ->through(fn($item) => [
                "id"    => $item?->user?->id,
                "name"  => $item?->user?->name,
                "email" => $item?->user?->email,
                "phone" => $item?->user?->phone,
                "unit"  => $item?->appartment?->unit,
                "gate"  => $item?->gate?->name,
                "date"  => $item?->updated_at?->format("Y-m-d h:i A"),
            ]);
    }

    public function visitors(Request $request)
    { 
        $validator = Validator::make($request->all(), [
            'search'    => 'sometimes',
            'date_from' => 'nullable|date',
            'date_to'   => 'nullable|date',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 400);
        }

        $visits_village = $this->getFilteredVisits($request, 'visitor', 'guest');

        return response()->json([ 
            'visits_village' => $visits_village,
        ]);
    }

    public function worker(Request $request)
    { 
        $validator = Validator::make($request->all(), [
            'search'    => 'sometimes',
            'date_from' => 'nullable|date',
            'date_to'   => 'nullable|date',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 400);
        }

        $visits_village = $this->getFilteredVisits($request, 'visitor', 'worker');

        return response()->json([ 
            'visits_village' => $visits_village, 
        ]);
    }

    public function deliveries(Request $request)
    { 
        $validator = Validator::make($request->all(), [
            'search'    => 'sometimes',
            'date_from' => 'nullable|date',
            'date_to'   => 'nullable|date',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 400);
        }

        $visits_village = $this->getFilteredVisits($request, 'visitor', 'delivery');

        return response()->json([ 
            'visits_village' => $visits_village, 
        ]);
    }

    public function owners(Request $request)
    { 
        $validator = Validator::make($request->all(), [
            'search'    => 'sometimes',
            'date_from' => 'nullable|date',
            'date_to'   => 'nullable|date',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 400);
        }

        $visits_village = $this->getFilteredVisits($request, 'owner');

        return response()->json([ 
            'visits_village' => $visits_village, 
        ]);
    }

    public function renters(Request $request)
    { 
        $validator = Validator::make($request->all(), [
            'search'    => 'sometimes',
            'date_from' => 'nullable|date',
            'date_to'   => 'nullable|date',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 400);
        }

        $visits_village = $this->getFilteredVisits($request, 'renter');

        return response()->json([ 
            'visits_village' => $visits_village, 
        ]);
    }

    public function filter(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'date_from' => 'nullable|date',
            'date_to'   => 'nullable|date',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 400);
        }

        $villageId = $request->user()->village_id;

        $users_beach = $this->user_beach
            ->where('village_id', $villageId)
            ->with(['user:id,name,email,phone', 'beach:id,name'])
            ->when($request->date_from, fn($q) => $q->whereDate('updated_at', '>=', $request->date_from))
            ->when($request->date_to, fn($q) => $q->whereDate('updated_at', '<=', $request->date_to))
            ->get();

        $users_pool = $this->user_pool
            ->where('village_id', $villageId)
            ->with(['user:id,name,email,phone', 'pool:id,name'])
            ->when($request->date_from, fn($q) => $q->whereDate('updated_at', '>=', $request->date_from))
            ->when($request->date_to, fn($q) => $q->whereDate('updated_at', '<=', $request->date_to))
            ->get();

        $visits_village = $this->visit_village
            ->where('village_id', $villageId)
            ->with(['user:id,name,email,phone', 'gate:id,name', 'appartment:id,unit,location'])
            ->when($request->date_from, fn($q) => $q->whereDate('updated_at', '>=', $request->date_from))
            ->when($request->date_to, fn($q) => $q->whereDate('updated_at', '<=', $request->date_to))
            ->get();

        $visitor_visits_village = [
            'guest'    => $visits_village->where('type', 'visitor')->where('visitor_type', 'guest')->values(),
            'worker'   => $visits_village->where('type', 'visitor')->where('visitor_type', 'worker')->values(),
            'delivery' => $visits_village->where('type', 'visitor')->where('visitor_type', 'delivery')->values(),
        ];

        $visitor_visits_village_count = [
            'guest'    => $visits_village->where('type', 'visitor')->where('visitor_type', 'guest')->count(),
            'worker'   => $visits_village->where('type', 'visitor')->where('visitor_type', 'worker')->count(),
            'delivery' => $visits_village->where('type', 'visitor')->where('visitor_type', 'delivery')->count(),
        ];

        $owner_visits_village = $visits_village->where('type', 'owner')->values();
        $renter_visits_village = $visits_village->where('type', 'renter')->values();

        return response()->json([ 
            'users_beach'                  => $users_beach,
            'users_pool'                   => $users_pool,
            'owner_visits_village'         => $owner_visits_village,
            'visitor_visits_village'       => $visitor_visits_village,
            'total_visits'                 => $visits_village,
            'users_beach_count'            => $users_beach->count(),
            'users_pool_count'             => $users_pool->count(),
            'owner_visits_village_count'   => $owner_visits_village->count(),
            'visitor_visits_village_count' => $visitor_visits_village_count, 
            'total_visits_count'           => $visits_village->count(),
        ]);
    }
}
