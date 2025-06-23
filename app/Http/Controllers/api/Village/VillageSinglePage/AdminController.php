<?php

namespace App\Http\Controllers\api\Village\VillageSinglePage;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\SuperAdmin\VillageAdminRequest;
use Illuminate\Support\Facades\Validator;
use App\trait\TraitImage;
use Illuminate\Validation\Rule;

use App\Models\Village;
use App\Models\User;
use App\Models\AdminPosition;

class AdminController extends Controller
{
    public function __construct(private Village $village,
    private User $admin, private AdminPosition $admin_positions){}
    use TraitImage;

    public function view(Request $request){
        $admins = $this->admin
        ->with('position')
        ->where('village_id', $request->user()->village_id)
        ->where('id', '!=', $request->user()->id)
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

    public function my_profile(Request $request){
        $admin = $request->user();
        $admin->position;
        $package = $admin->village->package;

        return response()->json([
            'admin' => $admin,
            'village' => $admin->village,
            'package' => $package,
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
        $admin_num = $request->user()->village?->package?->admin_num ?? 0;
        $admin_count = $this->admin
        ->where('village_id', $request->user()->village_id)
        ->count();
        if ($admin_num <= $admin_count) {
            return response()->json([
                'errors' => 'You don’t have a package, so you should subscribe.'
            ], 400);
        }
        $adminRequest = $request->validated();
        $adminRequest['role'] = 'village';
        $adminRequest['village_id'] = $request->user()->village_id;
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
            'email' => ['required', 'email' ,
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
        $admin = $this->admin
        ->where('id', $id)
        ->where('role', 'village')
        ->first();
        $adminRequest = $request->validated(); 
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
