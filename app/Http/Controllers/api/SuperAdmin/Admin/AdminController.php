<?php

namespace App\Http\Controllers\api\SuperAdmin\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\trait\TraitImage;
use Illuminate\Validation\Rule;

use App\Models\User;
use App\Models\AdminPosition;

class AdminController extends Controller
{
    public function __construct(private User $admin,
    private AdminPosition $position){}
    use TraitImage;

    public function my_data(Request $request){
        return response()->json([
            'name' => $request->user()->name,
            'image' => $request->user()->image_link,
        ]);
    }

    public function view(){
        $admins = $this->admin
        ->with('position.sup_roles')
        ->where('role', 'admin')
        ->get();
        $position = $this->position
        ->where('type', 'admin')
        ->where('status', 1)
        ->get();

        return response()->json([ 
            'admins' => $admins,
            'position' => $position,
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
        $this->admin
        ->where('id', $id) 
        ->update([
            'status' => $request->status
        ]);

        return response()->json([
            'success' => $request->status ? 'active' : 'banned',
        ]);
    }
    
    public function create(Request $request){
        $validator = Validator::make($request->all(), [
            'name' => ['required'],
            'email' => [ 'required', 'email',
            Rule::unique('users')->where(function ($query) {
                return $query->where('role', 'admin');
            })],
            'phone' => [
            Rule::unique('users')->where(function ($query) {
                return $query->where('role', 'admin');
            })],
            'password' => ['required'],
            'status' => ['required', 'boolean'],
            'gender' => ['in:male,female'],
            'admin_position_id' => ['required', 'exists:admin_positions,id'],
        ]);
        if ($validator->fails()) { // if Validate Make Error Return Message Error
            return response()->json([
                'errors' => $validator->errors(),
            ],400);
        }
        $adminRequest = $validator->validated();
        $adminRequest['role'] = 'admin';
        if (!is_string($request->image)) {
            $image_path = $this->upload($request, 'image', 'images/admin');
            $adminRequest['image'] = $image_path;
        }
        $admin = $this->admin
        ->create($adminRequest);
        
        return response()->json([
            'success' => 'You add data success',
        ]);
    }
    
    public function modify(Request $request, $id){
        $validator = Validator::make($request->all(), [
            'name' => ['required'],
            'email' => ['email', 'required', 
            Rule::unique('users')->where(function ($query) use($id) {
                return $query->where('role', 'admin')
                ->where('id', '!=', $id);
            })],
            'phone' => [Rule::unique('users')->where(function ($query) use($id){
                return $query->where('role', 'admin')
                ->where('id', '!=', $id);
            })],
            'status' => ['required', 'boolean'],
            'gender' => ['in:male,female'],
            'admin_position_id' => ['required', 'exists:admin_positions,id'],
        ]);
        if ($validator->fails()) { // if Validate Make Error Return Message Error
            return response()->json([
                'errors' => $validator->errors(),
            ],400);
        }
        $adminRequest = $validator->validated();
        if (!empty($request->password)) {
            $adminRequest['password'] = bcrypt($request->password);
        }
        $admin = $this->admin
        ->where('id', $id)
        ->first();
        if (!is_string($request->image)) {
            $image_path = $this->update_image($request, $payment_method->image, 'image', 'images/admin');
            $paymentMethodRequest['image'] = $image_path;
        }
        $admin->update($adminRequest);

        return response()->json([
            'success' => 'You update data success',
        ]);
    }
    
    public function delete($id){
        $admin = $this->admin
        ->where('id', $id) 
        ->first();
        if (empty($admin)) {
            return response()->json([
                'errors' => 'Admin not found'
            ], 400);
        }
        $this->deleteImage($admin->image);
        $admin->delete();

        return response()->json([
            'success' => 'You delete data success',
        ]);
    }
}
