<?php

namespace App\Http\Controllers\api\SuperAdmin\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\trait\image;

use App\Models\User;
use App\Models\SuperRole;

class AdminController extends Controller
{
    public function __construct(private User $admin,
    private SuperRole $super_role){}
    use image;

    public function view(){
        $admins = $this->admin
        ->with('super_roles')
        ->where('role', 'admin')
        ->get();
        $actions = [
            'all',
            'view',
            'add',
            'edit',
            'status',
            'delete',
        ];

        return response()->json([ 
            'admins' => $admins,
            'actions' => $actions,
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
            'provider_only' => ['required', 'boolean'],
            'action' => ['required'],
            'action.*' => ['required', 'in:all,view,status,add,edit,delete'],
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
        foreach ($request->action as $action) {
            $this->super_role
            ->create([
                'action' => $action,
                'user_id' => $admin->id,
            ]);
        }
        

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
            'provider_only' => ['required', 'boolean'],
            'action' => ['required'],
            'action.*' => ['required', 'in:all,view,status,add,edit,delete'],
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
        $this->super_role
        ->where('user_id', $admin->id)
        ->delete();
        foreach ($request->action as $action) {
            $this->super_role
            ->create([
                'action' => $action,
                'user_id' => $admin->id,
            ]);
        }

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
