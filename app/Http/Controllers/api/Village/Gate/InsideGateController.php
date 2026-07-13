<?php

namespace App\Http\Controllers\api\Village\Gate;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\Village\PoolRequest;
use Illuminate\Support\Facades\Validator;
use App\trait\TraitImage;

use App\Models\PoolGallary;
use App\Models\Pools;

class InsideGateController extends Controller
{
    public function __construct(private InsideGate $pool){}
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
        // ar_name 
        $inside_gateRequest = $request->validated();
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
        $inside_gateRequest = $request->validated();
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
}
