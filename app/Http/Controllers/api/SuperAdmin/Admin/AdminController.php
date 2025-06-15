<?php

namespace App\Http\Controllers\api\SuperAdmin\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\trait\TraitImage;

use App\Models\User;
use App\Models\AdminPosition;

class AdminController extends Controller
{
    public function __construct(private User $admin,
    private AdminPosition $position){}
    use TraitImage;

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
            'email' => ['required', 'email', 'unique:users,email'],
            'phone' => ['unique:users,phone'],
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
        $admin = $this->admin
        ->create($adminRequest);
        
        return response()->json([
            'success' => 'You add data success',
        ]);
    }
    
    public function modify(Request $request, $id){
        $validator = Validator::make($request->all(), [
            'name' => ['required'],
            'email' => ['required', 'email', 'unique:users,email,' . $id],
            'phone' => ['unique:users,phone,' . $id],
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
