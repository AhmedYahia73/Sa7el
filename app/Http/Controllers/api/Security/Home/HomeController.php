<?php

namespace App\Http\Controllers\api\Security\Home;

use App\Http\Controllers\Controller;
use App\Models\AppartmentCode;
use App\Models\Beach;
use App\Models\EntranceGate;
use App\Models\Gate;
use App\Models\Pools;
use App\Models\User;
use App\Models\UserBeach;
use App\Models\UserPool;
use App\Models\VisitorCode;
use App\Models\VisitVillage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

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

    public function visitors(Request $request){
        if(!auth()->user()->gate_visitors){
            return response()->json([
                'errors' => 'You are not allowed to view visitors'
            ], 401);
        }

        $visitors = VisitorCode::
        where('village_id', $request->user()->village_id)
        ->whereDate("created_at", date('Y-m-d'))
        ->whereDoesntHave('is_visit')
        ->with("unit")
        ->get()
        ->map(function($visitor){
            return [
                'id' => $visitor->id,
                'code' => $visitor->code,
                'appartment_id' => $visitor->appartment_id,
                'appartment_name' => $visitor->unit?->name,
                'image_link' => $visitor->qr_code_link,
                'created_at' => $visitor->created_at->format('Y-m-d h:i A'),
            ];
        });
    }

    public function search_village_users(Request $request){
        if(!auth()->user()->gate_entrance){
            return response()->json([
                'errors' => 'You are not allowed to view visitors'
            ], 401);
        }
        $validator = Validator::make($request->all(), [
            'phone' => 'required',
        ]);
        if ($validator->fails()) { // if Validate Make Error Return Message Error
            return response()->json([
                'errors' => $validator->errors(),
            ],400);
        }

        $user = User::
        where('phone', $request->phone)
        ->whereHas('appartment_code', function($query) use($request){
            $query->where('village_id', $request->user()->village_id);
        })
        ->first();

        return response()->json([
            'user' => $user,
        ]);
    }

    public function entrance_visitor(Request $request){
        if(!auth()->user()->gate_visitors){
            return response()->json([
                'errors' => 'You are not allowed to view visitors'
            ], 401);
        }
        $validator = Validator::make($request->all(), [
            'gate_id' => 'required|exists:gates,id',
            "visitor_id" => "required|exists:visitor_codes,id",
        ]);
        if ($validator->fails()) { // if Validate Make Error Return Message Error
            return response()->json([
                'errors' => $validator->errors(),
            ],400);
        }
        
        $visitor_code = VisitorCode::
        where('id', $request->visitor_id)
        ->first();
        $user_type = AppartmentCode::
         where('appartment_id', $visitor_code->appartment_id)
         ->where('user_id', $visitor_code->user_id)
         ->orderByDesc('id')
         ->first()?->type;
        $visit_village = VisitVillage::
        create([
            'user_id' => $$visitor_code->user_id,
            'village_id' => $request->user()->village_id,
            'gate_id' => $request->gate_id,
            'type' => 'visitor',
            'visitor_type' => $visitor_code->visitor_type,
            'code' => $visitor_code->code,
            'appartment_id' => $visitor_code->appartment_id,
            'user_type' => $user_type,
        ]);
        EntranceGate::create([
            'gate_id' => $request->gate_id,
            'user_id' => $visitor_code->user_id,
            'time' => date('H:i:s'),
            'village_id' => $request->user()->village_id,
        ]);

        return response()->json([
            'success' => 'Visitor entrance success',
            'visit_village_id' => $visit_village->id,
            'visitor_type' => $visitor_code->visitor_type,
            'date' => date('Y-m-d'),
            'time' => date('h:i A'),
        ]);
    }

    public function entrance_user(Request $request){
        if(!auth()->user()->gate_entrance){
            return response()->json([
                'errors' => 'You are not allowed to view visitors'
            ], 401);
        }
        $validator = Validator::make($request->all(), [
            'gate_id' => 'required|exists:gates,id',
            "visitor_id" => "required|exists:visitor_codes,id",
        ]);
        if ($validator->fails()) { // if Validate Make Error Return Message Error
            return response()->json([
                'errors' => $validator->errors(),
            ],400);
        }
        
        $visitor_code = VisitorCode::
        where('id', $request->visitor_id)
        ->first();
        $user_type = AppartmentCode::
         where('appartment_id', $visitor_code->appartment_id)
         ->where('user_id', $visitor_code->user_id)
         ->orderByDesc('id')
         ->first()?->type;
        $visit_village = VisitVillage::
        create([
            'user_id' => $$visitor_code->user_id,
            'village_id' => $request->user()->village_id,
            'gate_id' => $request->gate_id,
            'type' => $user_type,
            'appartment_id' => $visitor_code->appartment_id,
            'user_type' => $user_type,
        ]);
        EntranceGate::create([
            'gate_id' => $request->gate_id,
            'user_id' => $visitor_code->user_id,
            'time' => date('H:i:s'),
            'village_id' => $request->user()->village_id,
        ]);

        return response()->json([
            'success' => 'User entrance success',
            'visit_village_id' => $visit_village->id,
            'user_type' => $user_type,
            'date' => date('Y-m-d'),
            'time' => date('h:i A'),
        ]);
    }
}
