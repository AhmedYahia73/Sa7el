<?php

namespace App\Http\Controllers\api\Village\Pools;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\Village\PoolRequest;
use Illuminate\Support\Facades\Validator;

use App\Models\Pools;

class PoolController extends Controller
{
    public function __construct(private Pools $pool){}

    public function view(Request $request){
        $pool = $this->pool
        ->with('translations')
        ->where('village_id', $request->user()->village_id)
        ->get();

        return response()->json([
            'pools' => $pool,
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
        
        $pool = $this->pool
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
    
        $poolRequest = $request->validated();
        $poolRequest['village_id'] = $request->user()->village_id;

        $pool = $this->pool
        ->create($poolRequest);
        $pool_translations = [[ 
            'locale' => 'en',
            'key' => 'name',
            'value' => $request->name,
        ]];
        if (!empty($request->ar_name)) {
            $pool_translations[] = [ 
                'locale' => 'ar',
                'key' => 'name',
                'value' => $request->ar_name,
            ];
        }
        $pool->translations()->createMany($pool_translations);

        return response()->json([
            'success' => 'You add data success'
        ]);
    }

    public function modify(PoolRequest $request, $id){
        // name, image, status,
        // ar_name
        $poolRequest = $request->validated();
        $pool = $this->pool
        ->where('id', $id)
        ->where('village_id', $request->user()->village_id)
        ->first();
        if (empty($pool)) {
            return response()->json([
                'errors' => 'pool not found'
            ], 400);
        }
        $pool
        ->update($poolRequest);
        $pool_translations = [[ 
            'locale' => 'en',
            'key' => 'name',
            'value' => $request->name,
        ]];
        if (!empty($request->ar_name)) {
            $pool_translations[] = [ 
                'locale' => 'ar',
                'key' => 'name',
                'value' => $request->ar_name,
            ];
        }
        $pool->translations()->delete();
        $pool->translations()->createMany($pool_translations);

        return response()->json([
            'success' => 'You update data success'
        ]);
    }

    public function delete(Request $request, $id){
        $pool = $this->pool
        ->where('id', $id)
        ->where('village_id', $request->user()->village_id)
        ->first();
        if (empty($pool)) {
            return response()->json([
                'errors' => 'pool not found'
            ], 400);
        }
        $pool->translations()->delete();
        $pool->delete();

        return response()->json([
            'success' => 'You delete data success'
        ]);
    }
}
