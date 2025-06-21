<?php

namespace App\Http\Controllers\api\SuperAdmin\village;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\SuperAdmin\VillageAdminRequest;
use Illuminate\Support\Facades\Validator;
use App\trait\TraitImage;
use Illuminate\Validation\Rule;

use App\Models\Village;
use App\Models\User;
use App\Models\AdminPosition;

class VillageAdminController extends Controller
{
    public function __construct(private Village $village,
    private User $admin, private AdminPosition $admin_positions){}
    use TraitImage;

    public function view($id){
        $village = $this->village
        ->where('id', $id)
        ->with('admin.position')
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
            'email' => [ 'email',
            Rule::unique('users')->where(function ($query) {
                return $query->whereIn('role', ['village', 'maintenance_provider', 'provider']);
            })],
            'phone' => [
            Rule::unique('users')->where(function ($query) {
                return $query->whereIn('role', ['village', 'maintenance_provider', 'provider']);
            })],
            'password' => ['required'],
        ]);
        if ($validator->fails()) { // if Validate Make Error Return Message Error
            return response()->json([
                'errors' => $validator->errors(),
            ],400);
        }
        $village = $this->village
        ->where('id', $request->village_id)
        ->first();
        $admin_num = $village?->package?->admin_num ?? 1;
        $admin_count = $this->admin
        ->where('village_id', $request->village_id)
        ->count();
        if ($admin_num <= $admin_count) {
            return response()->json([
                'errors' => 'You have exceeded the limit of add admin'
            ], 400);
        }
        $adminRequest = $request->validated();
        $adminRequest['role'] = 'village';
        $adminRequest['village_id'] = $request->village_id;
        $adminRequest['password'] = $request->password;
        if (!empty($request->image) && !is_string($request->image)) {
            $image_path = $this->upload($request, 'image', 'images/village_admin_image');
            $adminRequest['image'] = $image_path;
        } 
        $this->admin
        ->create($adminRequest);

        return response()->json([
            'success' => 'You add data success',
        ]);
    }
    
    public function modify(VillageAdminRequest $request, $id){
        $validator = Validator::make($request->all(), [
            'email' => ['email' ,
                Rule::unique('users')
                ->ignore($id)
                ->where(function ($query) {
                    return $query->whereIn('role', ['provider', 'village', 'maintenance_provider']);
                }),
            ],
            'phone' => [
                Rule::unique('users')
                ->ignore($id)
                ->where(function ($query) {
                    return $query->whereIn('role', ['provider', 'village', 'maintenance_provider']);
                }),
            ],
        ]);
        if ($validator->fails()) { // if Validate Make Error Return Message Error
            return response()->json([
                'errors' => $validator->errors(),
            ],400);
        }
        $adminRequest = $request->validated();
        $admin = $this->admin
        ->where('id', $id)
        ->where('role', 'village')
        ->first();
        if (!empty($request->password)) {
            $adminRequest['password'] = bcrypt($request->password);
        } 
        if (!empty($request->image) && !is_string($request->image)) {
            $image_path = $this->update_image($request, $admin->image, 'image', 'images/village_admin_image');
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
