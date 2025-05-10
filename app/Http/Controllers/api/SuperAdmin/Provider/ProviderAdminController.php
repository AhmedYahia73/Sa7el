<?php

namespace App\Http\Controllers\api\SuperAdmin\Provider;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\SuperAdmin\ProviderAdminRequest;
use Illuminate\Support\Facades\Validator;
use App\trait\image;

use App\Models\Provider;
use App\Models\User;
use App\Models\AdminPosition;

class ProviderAdminController extends Controller
{
    public function __construct(private Provider $provider,
    private User $admin, private AdminPosition $admin_positions){}
    use image;

    public function view($id){
        $provider = $this->provider
        ->where('id', $id)
        ->first();
        $admins = $provider?->admin ?? [];
        $provider_positions = $this->admin_positions
        ->where('type', 'provider')
        ->where('status', 1)
        ->with('roles')
        ->get();

        return response()->json([
            'provider' => $provider,
            'admins' => $admins, 
            'provider_positions' => $provider_positions,
        ]);
    }

    public function admin($id){
        $admin = $this->admin
        ->where('id', $id)
        ->where('role', 'provider')
        ->with('position.roles')
        ->first();

        return response()->json([
            'admin' => $admin,
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
        ->where('role', 'provider')
        ->update([
            'status' => $request->status
        ]);

        return response()->json([
            'success' => $request->status ? 'active' : 'banned',
        ]);
    }
    
    public function create(ProviderAdminRequest $request){
        $validator = Validator::make($request->all(), [
            'provider_id' => ['required', 'exists:providers,id'],
            'email' => ['unique:users'],
            'phone' => ['unique:users'],
        ]);
        if ($validator->fails()) { // if Validate Make Error Return Message Error
            return response()->json([
                'errors' => $validator->errors(),
            ],400);
        }
        $adminRequest = $request->validated();
        $adminRequest['role'] = 'provider';
        $adminRequest['provider_id'] = $request->provider_id;
        $this->admin
        ->create($adminRequest);

        return response()->json([
            'success' => 'You add data success',
        ]);
    }
    
    public function modify(ProviderAdminRequest $request, $id){
        $validator = Validator::make($request->all(), [
            'email' => ['email', 'unique:users,email,' . $id],
            'phone' => ['unique:users,phone,' . $id],
        ]);
        if ($validator->fails()) { // if Validate Make Error Return Message Error
            return response()->json([
                'errors' => $validator->errors(),
            ],400);
        }
        $adminRequest = $request->validated(); 
        $this->admin
        ->where('id', $id)
        ->where('role', 'provider')
        ->update($adminRequest);

        return response()->json([
            'success' => 'You update data success',
        ]);
    }
    
    public function delete($id){
        $admin = $this->admin
        ->where('id', $id)
        ->where('role', 'provider')
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
