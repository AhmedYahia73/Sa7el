<?php

namespace App\Http\Controllers\api\Security\Home;

use App\Http\Controllers\Controller;
use App\Models\AppartmentCode;
use App\Models\Appartment;
use App\Models\Beach;
use App\Models\EntranceGate;
use App\Models\Gate;
use App\Models\Pools;
use App\Models\User;
use App\Models\UserBeach;
use App\Models\UserPool;
use App\Models\VisitorCode;
use App\Models\VisitVillage;
use App\Models\InsideGate;
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
 
    public function inside_gates(Request $request){
        $validator = Validator::make($request->all(), [
            'locale' => 'required|in:ar,en',
        ]);

        if ($validator->fails()) { 
            return response()->json([
                'errors' => $validator->errors(),
            ], 400);
        }

        $locale = $request->locale;
        $inside_gate = InsideGate::
        where("village_id", auth()->user()->village_id) 
        ->get()
        ->map(function($item) use($locale){
            return [
                "id" => $item->id,
                "name" => $locale == "en" ?
                $item->name : $item->ar_name ?? $item->name,
            ];
        });

        return response()->json([
            "inside_gates" => $inside_gate, 
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
        if (!auth()->user()->gate_visitors) {
            return response()->json([
                'errors' => 'You are not allowed to view visitors'
            ], 401);
        }

        $validator = Validator::make($request->all(), [
            'search' => 'sometimes|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors(),
            ], 400);
        }

        $search = $request->input('search');

        $visitors = VisitorCode::with(['unit', 'user']) // يفضل وضع العلاقات في مصفوفة
            ->where('village_id', $request->user()->village_id)
            ->whereDate('created_at', now()->toDateString())
            ->whereDoesntHave('is_visit')
            // --- تعديل كود البحث لحماية الشروط الأساسية ---
            ->when($search, function ($query, $search) {
                $query->where(function ($innerQuery) use ($search) {
                    $innerQuery->whereHas('unit', function ($unitQuery) use ($search) {
                        $unitQuery->where('unit', 'like', '%' . $search . '%');
                    })
                    ->orWhereHas('user', function ($userQuery) use ($search) {
                        $userQuery->where('name', 'like', '%' . $search . '%');
                    });
                });
            })
            // ------------------------------------------
            ->paginate(15)
            ->through(function ($visitor) {
                return [
                    'id' => $visitor->id,
                    'code' => $visitor->code,
                    'visitor_type' => $visitor->visitor_type,
                    'appartment_id' => $visitor->appartment_id,
                    'appartment_name' => $visitor->unit?->unit,
                    'image_link' => $visitor->qr_code_link,
                    'owner' => $visitor->user?->name,
                    'created_at' => $visitor->created_at->format('Y-m-d h:i A'),
                ];
            });

        return response()->json([
            'visitors' => $visitors,
        ]);
    }

    public function search_village_users(Request $request){
        if (!auth()->user()->gate_entrance) {
            return response()->json([
                'errors' => 'You are not allowed to view visitors'
            ], 401);
        }

        $validator = Validator::make($request->all(), [
            'phone' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors(),
            ], 400);
        }

        $userModel = User::where('phone', $request->phone)
            ->whereHas('appartment_code', function($query) use ($request) {
                $query->where('village_id', $request->user()->village_id);
            })
            ->with(['appartment_code' => function($query) {
                $today = date("Y-m-d");
                $query->where("type", "owner")
                ->where("village_id", auth()->user()->village_id)
                    ->orWhere(function($q) use ($today) {
                        $q->where("type", "renter")
                            ->where("from", "<=", $today)
                            ->where("to", ">=", $today); // تم تصحيح الاتجاه هنا ليغطي العقود السارية
                    })
                    ->with("appartment");
            }])
            ->first();

        if (!$userModel) {
            return response()->json([
                'errors' => 'User not found or does not belong to this village'
            ], 404);
        }

        $responseData = [
            "id"    => $userModel->id,
            "name"  => $userModel->name,
            "email" => $userModel->email,
            "image" => $userModel->image_link,
            "units" => $userModel->appartment_code->map(function($item) {
                return [
                    "id"         => $item?->appartment?->id,
                    "appartment" => $item?->appartment?->unit, // سيصبح null إذا لم يطابق شروط الـ Owner أو Renter الساري
                ];
            }), 
        ];

        return response()->json([
            'user' => $responseData,
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
            'user_id' => $visitor_code->user_id,
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
            "user_id" => "required|exists:users,id",
            "appartment_id" => "required|exists:appartments,id",
        ]);
        if ($validator->fails()) { // if Validate Make Error Return Message Error
            return response()->json([
                'errors' => $validator->errors(),
            ],400);
        }
         
        $user = User::
        where("id", $request->user_id)
        ->first();
        $appartment = Appartment::
        where("id", $request->appartment_id)
        ->first();
        $appartment_code = AppartmentCode::
         where('appartment_id', $request->appartment_id)
         ->where('user_id', $request->user_id)
         ->orderByDesc('id')
         ->with("appartment.type")
         ->first();
        $user_type = $appartment_code?->type;
        $unit_type = $appartment_code?->appartment?->type?->name;
        $visit_village = VisitVillage::
        create([
            'user_id' => $request->user_id,
            'village_id' => $request->user()->village_id,
            'gate_id' => $request->gate_id,
            'type' => $user_type,
            'appartment_id' => $request->appartment_id,
            'user_type' => $user_type,
        ]);
        EntranceGate::create([
            'gate_id' => $request->gate_id,
            'user_id' => $request->user_id,
            'time' => date('H:i:s'),
            'village_id' => $request->user()->village_id,
        ]);

        return response()->json([
            'success' => 'User entrance success',
            'visit_village_id' => $visit_village->id,
            'user_type' => $user_type,
            'date' => date('Y-m-d'),
            'time' => date('h:i A'),
            "user_name" => $user->name,
            "appartment" => $appartment->unit,
            "unit_type" => $unit_type,
        ]);
    }
}
