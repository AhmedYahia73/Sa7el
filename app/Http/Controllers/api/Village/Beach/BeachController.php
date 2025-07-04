<?php

namespace App\Http\Controllers\api\Village\Beach;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\Village\PoolRequest as BeachRequest;
use Illuminate\Support\Facades\Validator;
use App\trait\TraitImage;

use App\Models\BeachGallary;
use App\Models\Beach;

class BeachController extends Controller
{
    public function __construct(private Beach $beach,
    private BeachGallary $gallary){}
    use TraitImage;

    public function view(Request $request){
        $beach = $this->beach
        ->with(['translations', 'gallery'])
        ->where('village_id', $request->user()->village_id)
        ->get();

        return response()->json([
            'beachs' => $beach,
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
        
        $beach = $this->beach
        ->where('id', $id)
        ->where('village_id', $request->user()->village_id)
        ->update([
            'status' => $request->status
        ]);

        return response()->json([
            'success' => $request->status ? 'active' : 'banned'
        ]);
    }

    public function create(BeachRequest $request){
        // name, from, to, status,
        // ar_name 
        $validator = Validator::make($request->all(), [
            'images' => ['required', 'array']
        ]);
        if ($validator->fails()) { // if Validate Make Error Return Message Error
            return response()->json([
                'errors' => $validator->errors(),
            ],400);
        }
        $beachRequest = $request->validated();
        $beachRequest['village_id'] = $request->user()->village_id;

        $beach = $this->beach
        ->create($beachRequest);
        if ($request->has('images')) {
            foreach ($request->images as $item) {
                $image_path = $this->uploadFile($item, '/village/beach');
                $this->gallary
                ->create([
                    'beach_id' => $beach->id,
                    'image' => $image_path,
                ]);
            }
        }
        $beach_translations = [[ 
            'locale' => 'en',
            'key' => 'name',
            'value' => $request->name,
        ]];
        if (!empty($request->ar_name)) {
            $beach_translations[] = [ 
                'locale' => 'ar',
                'key' => 'name',
                'value' => $request->ar_name,
            ];
        }
        $beach->translations()->createMany($beach_translations);

        return response()->json([
            'success' => 'You add data success'
        ]);
    }

    public function modify(BeachRequest $request, $id){
        // name, image, status,
        // ar_name
        $validator = Validator::make($request->all(), [
            'image_id' => 'exists:beach_gallaries,id',
        ]);
        if ($validator->fails()) { // if Validate Make Error Return Message Error
            return response()->json([
                'errors' => $validator->errors(),
            ],400);
        }
        $beachRequest = $request->validated();
        $beach = $this->beach
        ->where('id', $id)
        ->where('village_id', $request->user()->village_id)
        ->first();
        if (empty($beach)) {
            return response()->json([
                'errors' => 'beach not found'
            ], 400);
        }
        $beach
        ->update($beachRequest);
        $beach_translations = [[ 
            'locale' => 'en',
            'key' => 'name',
            'value' => $request->name,
        ]];
        if (!empty($request->ar_name)) {
            $beach_translations[] = [ 
                'locale' => 'ar',
                'key' => 'name',
                'value' => $request->ar_name,
            ];
        }
        $beach->translations()->delete();
        $beach->translations()->createMany($beach_translations);

        if (!empty($request->image) && !is_string($request->image)) {
            if (!empty($request->image_id)) {
                $beach_gallary = $this->gallary
                ->where('id', $request->image_id)
                ->first();
                $image_path = $this->update_image($request, $beach_gallary->image, 'image', '/village/beach');
                $beach_gallary
                ->update([
                    'image' => $image_path,
                ]);
            } 
            else {
                $image_path = $this->uploadFile($request->image, '/village/beach');
                $this->gallary
                ->create([
                    'beach_id' => $id,
                    'image' => $image_path,
                ]);
            }
        }

        return response()->json([
            'success' => 'You update data success'
        ]);
    }

    public function delete(Request $request, $id){
        $beach = $this->beach
        ->where('id', $id)
        ->where('village_id', $request->user()->village_id)
        ->first();
        if (empty($beach)) {
            return response()->json([
                'errors' => 'beach not found'
            ], 400);
        }
        $beach->translations()->delete();
        $beach->delete();

        return response()->json([
            'success' => 'You delete data success'
        ]);
    }

    public function view_gallery($id){
        $gallary = $this->gallary
        ->where('beach_id', $id)
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
            $image_path = $this->uploadFile($item, '/village/beach');
            $this->gallary
            ->create([
                'beach_id' => $id,
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
