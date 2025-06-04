<?php

namespace App\Http\Controllers\api\SuperAdmin\ServiceProvider;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

use App\Models\AdminPosition;
use App\Models\AdminRole;

class MaintenanceProviderRolesController extends Controller
{
    public function __construct(private AdminPosition $admin_position, 
    private AdminRole $admin_roles){}
    protected $roleRequest = [
        'name',
        'status'
    ];

    public function view(){
        $admin_position = $this->admin_position
        ->where('type', 'maintenance_provider')
        ->with('roles')
        ->get();
        $roles = ['Test'];

        return response()->json([
            'admin_position' => $admin_position,
            'roles' => $roles,
        ]);
    }

    public function position($id){
        $admin_position = $this->admin_position
        ->where('type', 'maintenance_provider')
        ->where('id', $id)
        ->with('roles')
        ->first();

        return response()->json([
            'admin_position' => $admin_position,
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
        $admin_position = $this->admin_position
        ->where('type', 'maintenance_provider')
        ->where('id', $id) 
        ->update([
            'status' => $request->status
        ]);

        return response()->json([
            'success' => $request->status ? 'active' : 'banned'
        ]);
    }

    public function create(Request $request){
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'status' => 'required|boolean',
            'roles' => 'array',
            'roles.*' => 'required',
        ]);
        if ($validator->fails()) { // if Validate Make Error Return Message Error
            return response()->json([
                'errors' => $validator->errors(),
            ],400);
        }
        $positionRequest = $request->only($this->roleRequest);
        $positionRequest['type'] = 'maintenance_provider';
        $admin_position = $this->admin_position
        ->create($positionRequest);
        if ($request->roles) {
            foreach ($request->roles as $item) {
                $this->admin_roles
                ->create([
                    'module' => $item,
                    'position_id' => $admin_position->id,
                ]);
            }
        } 

        return response()->json([
            'success' => 'You add data success'
        ]);
    }

    public function modify(Request $request, $id){
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'status' => 'required|boolean',
            'roles' => 'array',
            'roles.*' => 'required',
        ]);
        if ($validator->fails()) { // if Validate Make Error Return Message Error
            return response()->json([
                'errors' => $validator->errors(),
            ],400);
        }
        $positionRequest = $request->only($this->roleRequest);
        $positionRequest['type'] = 'maintenance_provider';
        $admin_position = $this->admin_position
        ->where('id', $id)
        ->update($positionRequest);
        $this->admin_roles
        ->where('position_id', $id)
        ->delete();
        if ($request->roles) {
            foreach ($request->roles as $item) {
                $this->admin_roles
                ->create([
                    'module' => $item,
                    'position_id' => $id,
                ]);
            }
        } 

        return response()->json([
            'success' => 'You update data success'
        ]);
    }

    public function delete($id){
        $admin_position = $this->admin_position
        ->where('id', $id)
        ->where('type', 'maintenance_provider')
        ->delete();

        return response()->json([
            'success' => 'You delete data success'
        ]);
    }
}
