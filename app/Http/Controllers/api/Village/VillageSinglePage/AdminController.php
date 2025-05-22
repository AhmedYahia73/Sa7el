<?php

namespace App\Http\Controllers\api\Village\VillageSinglePage;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\SuperAdmin\VillageAdminRequest;
use Illuminate\Support\Facades\Validator;
use App\trait\image;

use App\Models\Village;
use App\Models\User;
use App\Models\AdminPosition;

class AdminController extends Controller
{
    public function __construct(private Village $village,
    private User $admin, private AdminPosition $admin_positions){}
    use image;

    public function view(Request $request){
        $admins = $this->admin
        ->where('village_id', $request->user()->village_id)
        ->where('role', 'village')
        ->get();
        $village_positions = $this->admin_positions
        ->where('type', 'village')
        ->where('status', 1)
        ->with('roles')
        ->get();

        return response()->json([ 
            'admins' => $admins, 
            'village_positions' => $village_positions,
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
            'email' => ['unique:users'],
            'phone' => ['unique:users'],
            'password' => ['required'],
        ]);
        if ($validator->fails()) { // if Validate Make Error Return Message Error
            return response()->json([
                'errors' => $validator->errors(),
            ],400);
        }
        $admin_num = $request->user()->village?->package?->admin_num ?? 0;
        $admin_count = $this->admin
        ->where('village_id', $request->user()->village_id)
        ->count();
        if ($admin_num <= $admin_count) {
            return response()->json([
                'errors' => 'You have exceeded the limit of add admin'
            ], 400);
        }
        $adminRequest = $request->validated();
        $adminRequest['role'] = 'village';
        $adminRequest['village_id'] = $request->user()->village_id;
        $adminRequest['password'] = $request->password;
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
