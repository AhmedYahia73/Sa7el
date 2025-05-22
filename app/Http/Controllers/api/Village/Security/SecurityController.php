<?php

namespace App\Http\Controllers\api\Village\Security;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Http\Requests\Village\SecuirtyRequest;
use App\trait\image;

use App\Models\SecurityMan;

class SecurityController extends Controller
{
    public function __construct(private SecurityMan $security){}
    use image;

    public function view(Request $request){
        $security = $this->security
        ->where('village_id', $request->user()->village_id)
        ->get();

        return response()->json([
            'security' => $security,
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
        
        $security = $this->security
        ->where('id', $id)
        ->where('village_id', $request->user()->village_id)
        ->update([
            'status' => $request->status
        ]);

        return response()->json([
            'success' => $request->status ? 'active' : 'banned'
        ]);
    }

    public function create(SecuirtyRequest $request){
        // name, location, shift_from, shift_to, password, image
        // email, phone, type, status
        $validator = Validator::make($request->all(), [
            'image' => 'required',
            'password' => 'required',
            'email' => 'unique:security_men,email',
            'phone' => 'unique:security_men,phone',
        ]);
        if ($validator->fails()) { // if Validate Make Error Return Message Error
            return response()->json([
                'errors' => $validator->errors(),
            ],400);
        }
        $security_num = $request->user()->village?->package?->security_num ?? 0;
        $security_count = $this->security
        ->where('village_id', $request->user()->village_id)
        ->count();
        if ($security_num <= $security_count) {
            return response()->json([
                'errors' => 'You have exceeded the limit of add admin'
            ], 400);
        }
        $securityRequest = $request->validated();
        $securityRequest['village_id'] = $request->user()->village_id;
        $securityRequest['password'] = $request->password;
        $image_path = $this->upload($request, 'image', '/village/security');
        $securityRequest['image'] = $image_path;
  
        $security = $this->security
        ->create($securityRequest);
      
        return response()->json([
            'success' => 'You add data success'
        ]);
    }

    public function modify(SecuirtyRequest $request, $id){
        // name, location, shift_from, shift_to, password, image
        // email, phone, type, status
        $validator = Validator::make($request->all(), [
            'email' => 'unique:security_men,email,' . $id,
            'phone' => 'unique:security_men,phone,' . $id,
        ]);
        if ($validator->fails()) { // if Validate Make Error Return Message Error
            return response()->json([
                'errors' => $validator->errors(),
            ],400);
        }
        $securityRequest = $request->validated();
        $security = $this->security
        ->where('id', $id)
        ->where('village_id', $request->user()->village_id)
        ->first();
        if (empty($security)) {
            return response()->json([
                'errors' => 'security not found'
            ], 400);
        }
        if ($request->image && !is_string($request->image)) {
            $image_path = $this->update_image($request, $security->image, 'image', '/village/security');
            $securityRequest['image'] = $image_path;
        }
        if (!empty($request->password)) {
            $securityRequest['password'] = bcrypt($request->password);
        }
        $security->update($securityRequest);
   
        return response()->json([
            'success' => 'You update data success'
        ]);
    }

    public function delete($id){
        $security = $this->security
        ->where('id', $id)
        ->first();
        if (empty($security)) {
            return response()->json([
                'errors' => 'security not found'
            ], 400);
        }
        $this->deleteImage($security->image);
        $security->delete();

        return response()->json([
            'success' => 'You delete data success'
        ]);
    }
}
