<?php

namespace App\Http\Controllers\api\SuperAdmin\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

use App\Models\AdminPosition;
use App\Models\SuperRole;

class AdminRoleController extends Controller
{
    public function __construct(private AdminPosition $admin_position, 
    private SuperRole $admin_roles){}
    protected $roleRequest = [
        'name',
        'status'
    ];

    public function view(){
        $admin_position = $this->admin_position
        ->where('type', 'admin')
        ->with('sup_roles')
        ->get();
        $roles = [
            'Zone' => ['all', 'view', 'status', 'add', 'edit', 'delete'],
            'Village' => ['all', 'view', 'status', 'add', 'edit', 'delete', 'update_profile', 'view_units', 'delete_unit'],
            'Village Gallery' => ['all', 'view', 'status', 'add', 'delete'],
            'Village Admin' => ['all', 'view', 'status', 'add', 'edit', 'delete'],
            'Village Admin Role' => ['all', 'view', 'status', 'add', 'edit', 'delete'],
            'Village Cover' => ['all', 'view', 'status', 'add', 'delete'],
            'Appartment Type' => ['all', 'view', 'status', 'add', 'edit', 'delete'],
            'User' => ['all', 'view', 'status', 'add', 'edit', 'delete'],
            'Service Type' => ['all', 'view', 'status', 'add', 'delete'],
            'Provider' => ['all', 'view', 'status', 'add', 'edit', 'delete', 'update_profile'],
            'Provider Gallery' => ['all', 'view', 'status', 'add', 'delete'],
            'Provider Admin' => ['all', 'view', 'status', 'add', 'edit', 'delete'],
            'Provider Admin Role' => ['all', 'view', 'status', 'add', 'edit', 'delete'],
            'Provider Profile' => ['all', 'edit', 'delete'],
            'Payment Method' => ['all', 'view', 'status', 'add', 'edit', 'delete'],
            'Subscription' => ['all', 'view', 'status', 'add', 'edit', 'delete'],
            'subcriber' => ['all', 'view', 'add', 'edit', 'delete'],
            'Payment' => ['all', 'view', 'status'],
            'Invoice' => ['all', 'view'],
            'Admin' => ['all', 'view', 'status', 'add', 'edit', 'delete'],
            'Admin Role' => ['all', 'view', 'status', 'add', 'edit', 'delete'],
            'Maintenance Type' => ['all', 'view', 'status', 'add', 'edit', 'delete'],
            'Provider Maintenance' => ['all', 'view', 'status', 'add', 'edit', 'delete', 'update_profile'],
            'Provider Maintenance Gallery' => ['all', 'view', 'status', 'add', 'delete'],
            'Provider Maintenance Admin' => ['all', 'view', 'status', 'add', 'edit', 'delete'],
            'Provider Maintenance Admin Role' => ['all', 'view', 'status', 'add', 'edit', 'delete'],
            'Provider Maintenance Cover' => ['all', 'view', 'status', 'add', 'delete'],
            'Mall' => ['all', 'view', 'status', 'add', 'edit', 'delete', 'update_profile'],
            'Mall Gallery' => ['all', 'view', 'status', 'add', 'delete'],
            'Mall Admin' => ['all', 'view', 'status', 'add', 'edit', 'delete'],
            'Mall Admin Role' => ['all', 'view', 'status', 'add', 'edit', 'delete'],
            'Mall Cover' => ['all', 'view', 'status', 'add', 'delete', 'update_profile'],
            'Home' => ['all', 'view'],
        ];

        return response()->json([
            'admin_position' => $admin_position,
            'roles' => $roles,
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
        ->where('type', 'admin')
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
            'roles.*.module' => 'required',
            'roles.*.action' => 'required',
        ]);
        if ($validator->fails()) { // if Validate Make Error Return Message Error
            return response()->json([
                'errors' => $validator->errors(),
            ],400);
        }
        $positionRequest = $request->only($this->roleRequest);
        $positionRequest['type'] = 'admin';
        $admin_position = $this->admin_position
        ->create($positionRequest);
        if ($request->roles) {
            foreach ($request->roles as $item) {
                $this->admin_roles
                ->create([
                    'module' => $item['module'],
                    'action' => $item['action'],
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
            'roles.*.module' => 'required',
            'roles.*.action' => 'required',
        ]);
        if ($validator->fails()) { // if Validate Make Error Return Message Error
            return response()->json([
                'errors' => $validator->errors(),
            ],400);
        }
        $positionRequest = $request->only($this->roleRequest);
        $positionRequest['type'] = 'admin';
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
                    'module' => $item['module'],
                    'action' => $item['action'],
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
        ->where('type', 'admin')
        ->delete();

        return response()->json([
            'success' => 'You delete data success'
        ]);
    }
}
