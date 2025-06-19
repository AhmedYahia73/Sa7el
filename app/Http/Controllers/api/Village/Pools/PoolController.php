<?php

namespace App\Http\Controllers\api\Village\Pools;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\Village\PoolRequest;
use Illuminate\Support\Facades\Validator;
use App\trait\TraitImage;

use App\Models\PoolGallary;
use App\Models\Pools;

class PoolController extends Controller
{
    public function __construct(private Pools $pool,
    private PoolGallary $gallary){}
    use TraitImage;

    public function view(Request $request){
        $pool = $this->pool
        ->with(['translations', 'gallery'])
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
        // ar_name, images
        $validator = Validator::make($request->all(), [
            'images' => ['required', 'array']
        ]);
        if ($validator->fails()) { // if Validate Make Error Return Message Error
            return response()->json([
                'errors' => $validator->errors(),
            ],400);
        }
        $poolRequest = $request->validated();
        $poolRequest['village_id'] = $request->user()->village_id;

        $pool = $this->pool
        ->create($poolRequest); 
        if ($request->has('images')) {
            foreach ($request->images as $item) {
                $image_path = $this->uploadFile($item, '/village/pool');
                $this->gallary
                ->create([
                    'pool_id' => $pool->id,
                    'image' => $image_path,
                ]);
            }
        }
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
        $validator = Validator::make($request->all(), [
            'image_id' => 'exists:pool_gallaries,id',
        ]);
        if ($validator->fails()) { // if Validate Make Error Return Message Error
            return response()->json([
                'errors' => $validator->errors(),
            ],400);
        }
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
        if (!empty($request->image) && !is_string($request->image)) {
            if (!empty($request->image_id)) {
                $pool_gallary = $this->gallary
                ->where('id', $request->image_id)
                ->first();
                $image_path = $this->update_image($request, $pool_gallary->image, 'image', '/village/pool');
                $pool_gallary->update([
                    'image' => $image_path,
                ]);
            } 
            else {
                $image_path = $this->uploadFile($item, '/village/pool');
                $this->gallary
                ->create([
                    'pool_id' => $id,
                    'image' => $image_path,
                ]);
            }
        }

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

    public function view_gallery($id){
        $gallary = $this->gallary
        ->where('pool_id', $id)
        ->get();

        return response()->json([
            'gallary' => $gallary
        ]);
    }

    public function add_gallery(Request $request, $id){
        $validator = Validator::make($request->all(), [
            'images' => ['required', 'array']
        ]);
        if ($validator->fails()) { // if Validate Make Error Return Message Error
            return response()->json([
                'errors' => $validator->errors(),
            ],400);
        }
        foreach ($request->images as $item) {
            $image_path = $this->uploadFile($item, '/village/pool');
            $this->gallary
            ->create([
                'pool_id' => $id,
                'image' => $image_path,
            ]);
        }

        return response()->json([
            'success' => 'You add data success'
        ]);
    }

    public function delete_gallery($id){ 
        $gallary = $this->gallary
        ->where('id', $id)
        ->first();
        $this->deleteImage($gallary->image);
        $gallary->delete();

        return response()->json([
            'success' => 'You delete data success'
        ]);
    }
}
