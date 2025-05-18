<?php

namespace App\Http\Controllers\api\Village\Appartments;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\trait\image;

use App\Models\Appartment;
use App\Models\AppartmentCode;
use App\Models\AppartmentType;
use App\Models\User;
use App\Models\Zone;

class AppartmentController extends Controller
{
    public function __construct(private Appartment $appartment,
    private AppartmentCode $appartment_code, private Zone $zones,
    private AppartmentType $appartment_type, private User $users){}
    use image;

    public function view(Request $request){
        $appartments = $this->appartment
        ->where('village_id', $request->user()->village_id)
        ->with('type:id,name,image', 'zone:id,name,image,description')
        ->get();
        $zones = $this->zones
        ->where('status', 1)
        ->get();
        $appartment_type = $this->appartment_type
        ->where('status', 1)
        ->get();
        $users = $this->users
        ->where('role', 'user')
        ->get();

        return response()->json([ 
            'appartments' => $appartments, 
            'zones' => $zones, 
            'appartment_type' => $appartment_type, 
            'users' => $users, 
        ]);
    }

    public function create_code(Request $request){
        if ($request->type == 'owner') {
            $validator = Validator::make($request->all(), [
                'appartment_id' => ['required', 'exists:appartments,id'],
                'type' => ['required', 'in:owner,renter'],
                'user_id' => ['required', 'exists:users,id'],
            ]);
        } 
        else {
            $validator = Validator::make($request->all(), [
                'appartment_id' => ['required', 'exists:appartments,id'],
                'type' => ['required', 'in:owner,renter'],
                'from' => ['required', 'date'],
                'to' => ['required', 'date'],
                'people' => ['required', 'numeric'],
                'image' => ['required'],
                'user_id' => ['required', 'exists:users,id'],
            ]);
        }
        if ($validator->fails()) { // if Validate Make Error Return Message Error
            return response()->json([
                'errors' => $validator->errors(),
            ],400);
        }

        $appartment_code = $this->appartment_code
        ->where('appartment_id', $request->appartment_id)
        ->where('type', 'owner')
        ->whereNotNull('user_id')
        ->first();
        if (!empty($appartment_code)) {
            return response()->json([
                'errors' => "This appartment has owner you can't buy it"
            ], 400);
        }
        $codeRequest = $validator->validated();
        do {
            $code = mt_rand(1000000, 9999999); // Always 7 digits
        } while ($this->appartment_code::where('code', $code)->exists());
        $codeRequest['code'] = $code;
        $codeRequest['village_id'] = $request->user()->village_id;
        if ($request->has('image')) {
            $image_path = $this->upload($request, 'image', '/village/appartment_code/id');
            $codeRequest['image'] = $image_path;
        }
        $this->appartment_code
        ->create($codeRequest);
        if ($request->type == 'owner') {
            $appartments = $this->appartment
            ->where('id', $request->appartment_id) 
            ->update([
                'user_id' => $request->user_id 
            ]);
        }

        return response()->json([
            'success' => $code
        ]);
    }

    public function create(Request $request){
        $validator = Validator::make($request->all(), [
            'unit' => ['required'],
            'number_floors' => ['required', 'numeric'],
            'appartment_type_id' => ['required', 'exists:appartment_types,id'],
            'zone_id' => ['required', 'exists:zones,id'],
        ]);
        if ($validator->fails()) { // if Validate Make Error Return Message Error
            return response()->json([
                'errors' => $validator->errors(),
            ],400);
        }
            // 'image' => ['required'],
            // 'village_id' => ['required'],
        $appartmentRequest = $validator->validated();
        $appartmentRequest['village_id'] = $request->user()->village_id;
        if ($request->has('image')) {
            $image_path = $this->upload($request, 'image', '/village/appartment');
            $appartmentRequest['image'] = $image_path;
        }
        $this->appartment
        ->create($appartmentRequest);

        return response()->json([
            'success' => 'You add data success',
        ]);
    }
    
    public function modify(Request $request, $id){
        $validator = Validator::make($request->all(), [
            'unit' => ['required'],
            'number_floors' => ['required', 'numeric'],
            'appartment_type_id' => ['required', 'exists:appartment_types,id'],
            'zone_id' => ['required', 'exists:zones,id'],
        ]);
        if ($validator->fails()) { // if Validate Make Error Return Message Error
            return response()->json([
                'errors' => $validator->errors(),
            ],400);
        }
        $appartment = $this->appartment
        ->where('id', $id) 
        ->first();
        $appartmentRequest = $validator->validated();
        if ($request->has('image')) {
            $image_path = $this->update_image($request, $appartment->image, 'image', '/village/appartment');
            $appartmentRequest['image'] = $image_path;
        }
        $appartment->update($appartmentRequest);

        return response()->json([
            'success' => 'You update data success',
        ]);
    }
    
    public function delete($id){
        $appartment = $this->appartment
        ->where('id', $id) 
        ->first();
        if (empty($appartment)) {
            return response()->json([
                'errors' => 'Admin not found'
            ], 400);
        }
        $this->deleteImage($appartment->image);
        $appartment->delete();

        return response()->json([
            'success' => 'You delete data success',
        ]);
    }
}
