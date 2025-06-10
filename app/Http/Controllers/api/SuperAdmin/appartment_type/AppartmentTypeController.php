<?php

namespace App\Http\Controllers\api\SuperAdmin\appartment_type;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\trait\TraitImage;

use App\Models\AppartmentType;

class AppartmentTypeController extends Controller
{
    public function __construct(private AppartmentType $appartment_type){}
    use TraitImage;

    public function view(){
        $appartment_type = $this->appartment_type
        ->with('translations')
        ->get();

        return response()->json([
            'appartment_types' => $appartment_type,
        ]);
    }

    public function appartment_type($id){
        $appartment_type = $this->appartment_type
        ->where('id', $id)
        ->first();

        return response()->json([
            'appartment_type' => $appartment_type,
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
        
        $appartment_type = $this->appartment_type
        ->where('id', $id)
        ->update([
            'status' => $request->status
        ]);

        return response()->json([
            'success' => $request->status ? 'active' : 'banned'
        ]);
    }

    public function create(Request $request){
        // name, image, status,
        // ar_name
        $validator = Validator::make($request->all(), [
            'name' => 'required', 
            'status' => 'required|boolean',
        ]);
        if ($validator->fails()) { // if Validate Make Error Return Message Error
            return response()->json([
                'errors' => $validator->errors(),
            ],400);
        }
        $validator_2 = Validator::make($request->all(), [ 
            'image' => 'required', 
        ]);
        if ($validator_2->fails()) { // if Validate Make Error Return Message Error
            return response()->json([
                'errors' => $validator_2->errors(),
            ],400);
        }
        $appartmentRequest = $validator->validated();
        if (!is_string($request->image)) {
            $image_path = $this->upload($request, 'image', 'images/appartment_type');
            $appartmentRequest['image'] = $image_path;
        }
        $appartment = $this->appartment_type
        ->create($appartmentRequest);
        $appartment_translations = [[ 
            'locale' => 'en',
            'key' => 'name',
            'value' => $request->name,
        ]];
        if (!empty($request->ar_name)) {
            $appartment_translations[] = [ 
                'locale' => 'ar',
                'key' => 'name',
                'value' => $request->ar_name,
            ];
        }
        $appartment->translations()->createMany($appartment_translations);

        return response()->json([
            'success' => 'You add data success'
        ]);
    }

    public function modify(Request $request, $id){
        // name, image, status,
        // ar_name
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'image' => 'required',
            'status' => 'required|boolean',
        ]);
        if ($validator->fails()) { // if Validate Make Error Return Message Error
            return response()->json([
                'errors' => $validator->errors(),
            ],400);
        }
        $appartmentRequest = $validator->validated();
        $appartment = $this->appartment_type
        ->where('id', $id)
        ->first();
        if (empty($appartment)) {
            return response()->json([
                'errors' => 'appartment not found'
            ], 400);
        }
        if (!is_string($request->image)) {
            $image_path = $this->update_image($request, $appartment->image, 'image', 'images/appartment_type');
            $appartmentRequest['image'] = $image_path;
        }
        $appartment
        ->update($appartmentRequest);
        $appartment_translations = [[ 
            'locale' => 'en',
            'key' => 'name',
            'value' => $request->name,
        ]];
        if (!empty($request->ar_name)) {
            $appartment_translations[] = [ 
                'locale' => 'ar',
                'key' => 'name',
                'value' => $request->ar_name,
            ];
        }
        $appartment->translations()->delete();
        $appartment->translations()->createMany($appartment_translations);

        return response()->json([
            'success' => 'You update data success'
        ]);
    }

    public function delete($id){
        $appartment = $this->appartment_type
        ->where('id', $id)
        ->first();
        if (empty($appartment)) {
            return response()->json([
                'errors' => 'appartment not found'
            ], 400);
        }
        $appartment->translations()->delete();
        $this->deleteImage($appartment->image);
        $appartment->delete();

        return response()->json([
            'success' => 'You delete data success'
        ]);
    }
}
