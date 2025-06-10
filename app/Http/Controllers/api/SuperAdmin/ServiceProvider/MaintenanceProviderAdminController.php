<?php

namespace App\Http\Controllers\api\SuperAdmin\ServiceProvider;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\SuperAdmin\ProviderAdminRequest;
use Illuminate\Support\Facades\Validator;
use App\trait\TraitImage;

use App\Models\ServiceProvider;
use App\Models\User;
use App\Models\AdminPosition;

class MaintenanceProviderAdminController extends Controller
{
    public function __construct(private ServiceProvider $provider,
    private User $admin, private AdminPosition $admin_positions){}
    use TraitImage;

    public function view($id){
        $provider = $this->provider
        ->where('id', $id)
        ->with('admin.position.roles')
        ->first();
        $admins = $provider?->admin ?? [];
        $provider_positions = $this->admin_positions
        ->where('type', 'maintenance_provider')
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
        ->where('role', 'maintenance_provider')
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
        ->where('role', 'maintenance_provider')
        ->update([
            'status' => $request->status
        ]);

        return response()->json([
            'success' => $request->status ? 'active' : 'banned',
        ]);
    }
    
    public function create(ProviderAdminRequest $request){
        $validator = Validator::make($request->all(), [
            'maintenance_provider_id' => ['required', 'exists:service_providers,id'],
            'email' => ['unique:users'],
            'phone' => ['unique:users'],
            'password' => ['required'],
        ]);
        if ($validator->fails()) { // if Validate Make Error Return Message Error
            return response()->json([
                'errors' => $validator->errors(),
            ],400);
        }
        $adminRequest = $request->validated();
        $adminRequest['role'] = 'maintenance_provider';
        $adminRequest['maintenance_provider_id'] = $request->maintenance_provider_id;
        $adminRequest['password'] = $request->password;
        if (!empty($request->image) && !is_string($request->image)) {
            $image_path = $this->upload($request, 'image', 'images/maintenance_provider_image');
            $adminRequest['image'] = $image_path;
        } 
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
        $admin = $this->admin
        ->where('id', $id)
        ->where('role', 'maintenance_provider')
        ->first();
        $adminRequest = $request->validated();
        if (!empty($request->password)) {
            $adminRequest['password'] = bcrypt($request->password);
        } 
        if (!empty($request->image) && !is_string($request->image)) {
            $image_path = $this->update_image($request, $admin->image, 'image', 'images/maintenance_provider_image');
            $adminRequest['image'] = $image_path;
        }
        $admin->update($adminRequest);

        return response()->json([
            'success' => 'You update data success',
        ]);
    }
    
    public function delete($id){
        $admin = $this->admin
        ->where('id', $id)
        ->where('role', 'maintenance_provider')
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
