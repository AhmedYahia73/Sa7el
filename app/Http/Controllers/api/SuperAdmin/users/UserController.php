<?php

namespace App\Http\Controllers\api\SuperAdmin\users;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\SuperAdmin\UserRequest;
use Illuminate\Support\Facades\Validator;
use App\trait\image;

use App\Models\User;
use App\Models\Village;

class UserController extends Controller
{
    public function __construct(private User $user,
    private Village $village){}
    use image;

    public function view(){
        $users = $this->user
        ->select('id', 'name', 'email', 'phone', 'password', 'rent_from', 'birthDate',
        'rent_to', 'user_type', 'village_id', 'image', 'parent_user_id', 'status', 'gender')
        ->with('villages_user', 'parent')
        ->where('role', 'user')
        ->get();
        $village = $this->village
        ->get();

        return response()->json([
            'users' => $users,
            'village' => $village,
        ]);
    }

    public function user($id){
        $user = $this->user
        ->select('id', 'name', 'email', 'phone', 'password', 'rent_from', 'birthDate',
        'rent_to', 'user_type', 'village_id', 'image', 'parent_user_id', 'status', 'gender')
        ->where('id', $id)
        ->where('role', 'user')
        ->with('villages_user', 'parent')
        ->first();

        return response()->json([
            'user' => $user,
        ]);
    }

    public function status(Request $request, $id){
        $validator = Validator::make($request->all(), [
            'status' => 'required|boolean',
        ]);
        if ($validator->fails()) { // if Validate Make Error Return Message Error
            return response()->json([
                'error' => $validator->errors(),
            ],400);
        }
        
        $user = $this->user
        ->where('id', $id)
        ->where('role', 'user')
        ->update([
            'status' => $request->status
        ]);

        return response()->json([
            'success' => $request->status ? 'active' : 'banned'
        ]);
    }

    public function create(UserRequest $request){
        // name, user_type, email, phone
        // password, status, parent_user_id, gender, birthDate
        $validator = Validator::make($request->all(), [ 
            'email' => ['unique:users'],
            'phone' => ['unique:users'],
            'password' => ['required'],
        ]);
        if ($validator->fails()) { // if Validate Make Error Return Message Error
            return response()->json([
                'error' => $validator->errors(),
            ],400);
        }
        $userRequest = $request->validated();
        $userRequest['role'] = 'user';
        $userRequest['password'] = $request->password;
        // if (!is_string($request->image)) {
        //     $image_path = $this->upload($request, 'image', 'images/users');
        //     $userRequest['image'] = $image_path;
        // }
        $user = $this->user
        ->create($userRequest);

        return response()->json([
            'success' => 'You add data success'
        ]);
    }

    public function modify(UserRequest $request, $id){
        // name, user_type, email, phone
        // password, status, parent_user_id, gender, birthDate
        $validator = Validator::make($request->all(), [
            'email' => ['email', 'unique:users,email,' . $id],
            'phone' => ['unique:users,phone,' . $id],
        ]);
        if ($validator->fails()) { // if Validate Make Error Return Message Error
            return response()->json([
                'error' => $validator->errors(),
            ],400);
        }
        $userRequest = $request->validated();
        if ($request->password) {
            $userRequest['password'] = $request->password;
        }
        if ($request->user_type != 'rent') {
            $userRequest['rent_from'] = null;
            $userRequest['rent_to'] = null;
        }
        $user = $this->user
        ->where('id', $id)
        ->where('role', 'user')
        ->first();
        if (empty($user)) {
            return response()->json([
                'errors' => 'user not found'
            ], 400);
        }
        // if (!is_string($request->image)) {
        //     $image_path = $this->update_image($request, $user->image, 'image', 'images/users');
        //     $userRequest['image'] = $image_path;
        // }
        $user->update($userRequest);

        return response()->json([
            'success' => 'You update data success'
        ]);
    }

    public function delete($id){
        $user = $this->user
        ->where('id', $id)
        ->first();
        if (empty($user)) {
            return response()->json([
                'errors' => 'user not found'
            ], 400);
        }
        $this->deleteImage($user->image);
        $user->delete();

        return response()->json([
            'success' => 'You delete data success'
        ]);
    }
}
