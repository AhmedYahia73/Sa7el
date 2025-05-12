<?php

namespace App\Http\Controllers\api\Village\Gate;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\trait\image;

use App\Models\Gate;

class GateController extends Controller
{
    public function __construct(private Gate $gates){}
    use image;

    public function view(Request $request){
        $gates = $this->gates
        ->where('village_id', $request->user()->village_id)
        ->get();

        return response()->json([
            'gatess' => $gates,
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
        
        $gates = $this->gates
        ->where('id', $id)
        ->where('village_id', $request->user()->village_id)
        ->update([
            'status' => $request->status
        ]);

        return response()->json([
            'success' => $request->status ? 'active' : 'banned'
        ]);
    }

    public function create(Request $request){
        // name, location, status, 
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'location' => 'required',
            'status' => 'required|boolean',
            'image' => 'required',
        ]);
        if ($validator->fails()) { // if Validate Make Error Return Message Error
            return response()->json([
                'errors' => $validator->errors(),
            ],400);
        }
        $gateRequest = $validator->validated();
        $gateRequest['village_id'] = $request->user()->village_id;
        $image_path = $this->upload($request, 'image', '/village/gate');
        $gateRequest['image'] = $image_path;
  
        $gates = $this->gates
        ->create($gateRequest);
      
        return response()->json([
            'success' => 'You add data success'
        ]);
    }

    public function modify(Request $request, $id){
        // name, location, status, 
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'location' => 'required',
            'status' => 'required|boolean',
        ]);
        if ($validator->fails()) { // if Validate Make Error Return Message Error
            return response()->json([
                'errors' => $validator->errors(),
            ],400);
        }
        $gateRequest = $validator->validated();
        $gates = $this->gates
        ->where('id', $id)
        ->where('village_id', $request->user()->village_id)
        ->first();
        if (empty($gates)) {
            return response()->json([
                'errors' => 'gates not found'
            ], 400);
        }
        if ($request->image && !is_string($request->image)) {
            $image_path = $this->upload($request, 'image', '/village/gate');
            $gateRequest['image'] = $image_path;
        }
        $gates->update($gateRequest);
   
        return response()->json([
            'success' => 'You update data success'
        ]);
    }

    public function delete($id){
        $gates = $this->gates
        ->where('id', $id)
        ->first();
        if (empty($gates)) {
            return response()->json([
                'errors' => 'gates not found'
            ], 400);
        }
        $this->deleteImage($gates->image);
        $gates->delete();

        return response()->json([
            'success' => 'You delete data success'
        ]);
    }
}
