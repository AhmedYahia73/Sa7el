<?php

namespace App\Http\Controllers\api\SuperAdmin\village;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\SuperAdmin\VillageAdminRequest;
use Illuminate\Support\Facades\Validator;
use App\trait\image;

use App\Models\Village;
use App\Models\User;
use App\Models\AdminPosition;

class VillageAdminController extends Controller
{
    public function __construct(private Village $village,
    private User $admin, private AdminPosition $admin_positions){}
    use image;

    public function view($id){
        $village = $this->village
        ->where('id', $id)
        ->first();
        $admins = $village?->admin ?? [];
        $village_positions = $this->admin_positions
        ->where('type', 'village')
        ->where('status', 1)
        ->with('roles')
        ->get();

        return response()->json([
            'village' => $village,
            'admins' => $admins, 
            'village_positions' => $village_positions,
        ]);
    }

    public function admin($id){
        $admin = $this->admin
        ->where('id', $id)
        ->where('role', 'village')
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
        ->where('role', 'village')
        ->update([
            'status' => $request->status
        ]);

        return response()->json([
            'success' => $request->status ? 'active' : 'banned',
        ]);
    }
    
    public function create(VillageAdminRequest $request){
        $validator = Validator::make($request->all(), [
            'village_id' => ['required', 'exists:villages,id'],
            'email' => ['unique:users'],
            'phone' => ['unique:users'],
        ]);
        if ($validator->fails()) { // if Validate Make Error Return Message Error
            return response()->json([
                'errors' => $validator->errors(),
            ],400);
        }
        $adminRequest = $request->validated();
        $adminRequest['role'] = 'village';
        $adminRequest['village_id'] = $request->village_id;
        $this->admin
        ->create($adminRequest);

        return response()->json([
            'success' => 'You add data success',
        ]);
    }
    
    public function modify(VillageAdminRequest $request, $id){
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
        if (!empty($request->password)) {
            $adminRequest['password'] = bcrypt($request->password);
        }
        $this->admin
        ->where('id', $id)
        ->where('role', 'village')
        ->update($adminRequest);

        return response()->json([
            'success' => 'You update data success',
        ]);
    }
    
    public function delete($id){
        $admin = $this->admin
        ->where('id', $id)
        ->where('role', 'village')
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
