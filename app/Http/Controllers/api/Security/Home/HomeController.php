<?php

namespace App\Http\Controllers\api\Security\Home;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

use App\Models\Beach;
use App\Models\Pools;
use App\Models\Gate;
use App\Models\VisitVillage;
use App\Models\UserBeach;
use App\Models\UserPool;

class HomeController extends Controller
{
    public function __construct(private Beach $beaches,
    private Pools $pools, private Gate $gates, 
    private VisitVillage $visit_village, private UserBeach $user_beach,
    private UserPool $user_pool){}

    public function view(Request $request){
        $beaches = $this->beaches
        ->where('village_id', $request->user()->village_id)
        ->whereHas('security', function($query) use($request){
            $query->where('security_men.id', $request->user()->id);
        })
        ->get();
        $pools = $this->pools
        ->where('village_id', $request->user()->village_id)
        ->whereHas('security', function($query) use($request){
            $query->where('security_men.id', $request->user()->id);
        })
        ->get();
        $gates = $this->gates
        ->where('village_id', $request->user()->village_id)
        ->whereHas('security', function($query) use($request){
            $query->where('security_men.id', $request->user()->id);
        })
        ->get();

        return response()->json([
            'beaches' => $beaches,
            'pools' => $pools,
            'gates' => $gates,
        ]);
    }

    public function entrance_gate(Request $request){
        $validator = Validator::make($request->all(), [
            'gate_id' => 'required|exists:gates,id',
        ]);
        if ($validator->fails()) { // if Validate Make Error Return Message Error
            return response()->json([
                'errors' => $validator->errors(),
            ],400);
        }
        $visit_village = $this->visit_village
        ->where('gate_id', $request->gate_id)
        ->whereDate('created_at', date('Y-m-d'))
        ->get();
        $entrance = $visit_village->count();
        $entrance_owner = $visit_village->where('type', 'owner')->count();
        $entrance_visitor = $visit_village->where('type', 'visitor');

        return response()->json([
            'entrance' => $entrance,
            'entrance_owner' => $entrance_owner,
            'entrance_visitor' => $entrance_visitor->count(),
            'entrance_visitor_worker' => $entrance_visitor->where('visitor_type', 'worker')->count(),
            'entrance_visitor_guest' => $entrance_visitor->where('visitor_type', 'guest')->count(),
            'entrance_visitor_delivery' => $entrance_visitor->where('visitor_type', 'delivery')->count(),
        ]);
    }

    public function entrance_pool(Request $request){
        $validator = Validator::make($request->all(), [
            'pool_id' => 'required|exists:pools,id',
        ]);
        if ($validator->fails()) { // if Validate Make Error Return Message Error
            return response()->json([
                'errors' => $validator->errors(),
            ],400);
        }
        $user_pool = $this->user_pool
        ->where('pool_id', $request->pool_id)
        ->whereDate('created_at', date('Y-m-d'))
        ->count();

        return response()->json([
            'user_pool' => $user_pool,
        ]);
    }

    public function entrance_beach(Request $request){
        $validator = Validator::make($request->all(), [
            'beach_id' => 'required|exists:beaches,id',
        ]);
        if ($validator->fails()) { // if Validate Make Error Return Message Error
            return response()->json([
                'errors' => $validator->errors(),
            ],400);
        }
        $user_beach = $this->user_beach
        ->where('beach_id', $request->beach_id)
        ->whereDate('created_at', date('Y-m-d'))
        ->count();

        return response()->json([
            'user_beach' => $user_beach,
        ]);
    }
}
