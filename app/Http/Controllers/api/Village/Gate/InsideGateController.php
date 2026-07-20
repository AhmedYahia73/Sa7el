<?php

namespace App\Http\Controllers\api\Village\Gate;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\Village\PoolRequest;
use Illuminate\Support\Facades\Validator;
use App\trait\TraitImage;
 
use App\Models\InsideGate;
use App\Models\VisitBeach;
use App\Models\VisitPool;

class InsideGateController extends Controller
{
    public function __construct(private InsideGate $inside_gate){}
    use TraitImage;

    public function view(Request $request){
        $inside_gate = $this->inside_gate 
        ->where('village_id', $request->user()->village_id)
        ->get()
        ->map(function($item){
            return [
                "id" => $item->id,
                "name" => $item->name,
                "from" => $item->from,
                "to" => $item->to,
                "status" => $item->status,
                "visitor" => $item->visitor,
            ];
        });

        return response()->json([
            'inside_gates' => $inside_gate,
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
        
        $inside_gate = $this->inside_gate
        ->where('id', $id)
        ->where('village_id', $request->user()->village_id)
        ->update([
            'status' => $request->status
        ]);

        return response()->json([
            'success' => $request->status ? 'active' : 'banned'
        ]);
    }

    public function create(PoolRequest $request){
        // name, from, to, status,
        // ar_name , type
        $validator = Validator::make($request->all(), [
            'visitor' => 'required|boolean',
        ]);
        if ($validator->fails()) { // if Validate Make Error Return Message Error
            return response()->json([
                'errors' => $validator->errors(),
            ],400);
        }
        $inside_gateRequest = $request->validated();
        $inside_gateRequest['visitor'] = $request->visitor;
        $inside_gateRequest['village_id'] = $request->user()->village_id;

        $inside_gate = $this->inside_gate
        ->create($inside_gateRequest);  
        $inside_gate_translations = [[ 
            'locale' => 'en',
            'key' => 'name',
            'value' => $request->name,
        ]];
        if (!empty($request->ar_name)) {
            $inside_gate_translations[] = [ 
                'locale' => 'ar',
                'key' => 'name',
                'value' => $request->ar_name,
            ];
        }
        $inside_gate->translations()->createMany($inside_gate_translations);

        return response()->json([
            'success' => 'You add data success'
        ]);
    }

    public function modify(PoolRequest $request, $id){
        // name, image, status,
        // ar_name 
        $validator = Validator::make($request->all(), [
            'visitor' => 'required|boolean',
        ]);
        if ($validator->fails()) { // if Validate Make Error Return Message Error
            return response()->json([
                'errors' => $validator->errors(),
            ],400);
        }
        $inside_gateRequest = $request->validated();
        $inside_gateRequest['visitor'] = $request->visitor;
        $inside_gate = $this->inside_gate
        ->where('id', $id)
        ->where('village_id', $request->user()->village_id)
        ->first();
        if (empty($inside_gate)) {
            return response()->json([
                'errors' => 'inside_gate not found'
            ], 400);
        }
        $inside_gate
        ->update($inside_gateRequest);
        $inside_gate_translations = [[ 
            'locale' => 'en',
            'key' => 'name',
            'value' => $request->name,
        ]];
        if (!empty($request->ar_name)) {
            $inside_gate_translations[] = [ 
                'locale' => 'ar',
                'key' => 'name',
                'value' => $request->ar_name,
            ];
        }
        $inside_gate->translations()->delete();
        $inside_gate->translations()->createMany($inside_gate_translations);
       
        return response()->json([
            'success' => 'You update data success'
        ]);
    }

    public function delete(Request $request, $id){
        $inside_gate = $this->inside_gate
        ->where('id', $id)
        ->where('village_id', $request->user()->village_id)
        ->first();
        if (empty($inside_gate)) {
            return response()->json([
                'errors' => 'inside_gate not found'
            ], 400);
        }
        $inside_gate->translations()->delete();
        $inside_gate->delete();

        return response()->json([
            'success' => 'You delete data success'
        ]);
    }

    public function entrance_list(Request $request, $id){
        $validator = Validator::make($request->all(), [
            'from'   => 'nullable|date',
            'to'     => 'nullable|date',
            'search' => 'nullable|string|max:255'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors(),
            ], 400);
        }

        $insideGate = InsideGate::findOrFail($id);

        $modelClass =  VisitBeach::class;

        $query = $modelClass::where("inside_gate_id", $id)
            ->where('village_id', $request->user()->village_id)
            ->with(["user", "appartment"]);

        if ($request->filled('from')) {
            $query->whereDate("created_at", ">=", $request->from);
        }

        if ($request->filled('to')) {
            $query->whereDate("created_at", "<=", $request->to);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            
            $query->where(function ($q) use ($search) {
                $q->whereHas('user', function ($userQuery) use ($search) {
                    $userQuery->where('name', 'like', "%{$search}%")
                            ->orWhere('phone', 'like', "%{$search}%")
                            ->orWhere('email', 'like', "%{$search}%");
                })
                ->orWhereHas('appartment', function ($appartmentQuery) use ($search) {
                    $appartmentQuery->where('unit', 'like', "%{$search}%");
                });
            });
        }

        $paginatedData = $query->latest()->paginate($request->get('per_page', 15));

        $formattedItems = collect($paginatedData->items())->map(function ($item) use ($gateType) {
            return [
                "id"            => $item->id,
                "type"          => $item->type,
                "visitor_type"  => $item->visitor_type,
                "user_type"     => $item->user_type,
                "appartment"    => $item->appartment?->unit,
                "user_name"     => $item->user?->name,
                "user_phone"    => $item->user?->phone,
                "user_email"    => $item->user?->email,
                "gate_type"     => ($gateType === "beach") ? "beach" : "pool",
                "date"          => $item->created_at->format("Y-m-d"),
                "time"          => $item->created_at->format("H:i A"),
            ];
        });

        return response()->json([
            "data" => $formattedItems,
            "pagination" => [
                "current_page" => $paginatedData->currentPage(),
                "last_page"    => $paginatedData->lastPage(),
                "per_page"     => $paginatedData->perPage(),
                "total"        => $paginatedData->total(),
            ]
        ]);
    }
}
