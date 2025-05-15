<?php

namespace App\Http\Controllers\api\SuperAdmin\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\SuperAdmin\ProviderAdminRequest;
use Illuminate\Support\Facades\Validator;
use App\trait\image;

use App\Models\User;

class AdminController extends Controller
{
    public function __construct(private User $admin){}
    use image;

    public function view($id){
        $admins = $this->admin
        ->where('role', 'admin')
        ->get();

        return response()->json([ 
            'admins' => $admins, 
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
    
    public function create(ProviderAdminRequest $request){
        $validator = Validator::make($request->all(), [
            'name' => ['required'],
            'email' => ['required', 'email', 'unique:users,email'],
            'phone' => ['required', 'unique:users,phone'],
            'password' => ['required'],
            'status' => ['required', 'boolean'],
            'gender' => ['required', 'in:male,female']
        ]);
        if ($validator->fails()) { // if Validate Make Error Return Message Error
            return response()->json([
                'errors' => $validator->errors(),
            ],400);
        }
        $adminRequest = $validator->validated();
        $adminRequest['role'] = 'admin';
        $this->admin
        ->create($adminRequest);

        return response()->json([
            'success' => 'You add data success',
        ]);
    }
    
    public function modify(ProviderAdminRequest $request, $id){
        $validator = Validator::make($request->all(), [
            'name' => ['required'],
            'email' => ['required', 'email', 'unique:users,email,' . $id],
            'phone' => ['required', 'unique:users,phone,' . $id],
            'password' => ['required'],
            'status' => ['required', 'boolean'],
            'gender' => ['required', 'in:male,female']
        ]);
        if ($validator->fails()) { // if Validate Make Error Return Message Error
            return response()->json([
                'errors' => $validator->errors(),
            ],400);
        }
        $adminRequest = $validator->validated(); 
        $this->admin
        ->where('id', $id) 
        ->update($adminRequest);

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
