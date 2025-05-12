<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Http\Requests\Auth\SignupRequest;

use App\Models\User;
use App\Models\Village;
use App\Models\Zone;

class LoginController extends Controller
{
    public function __construct(private User $user, private Village $village,
    private Zone $zones){}

    public function sign_up_list(){
        $villages = $this->village
        ->where('status', 1)
        ->get();
        $current_village = $this->village
        ->where('status', 1)
        ->where('to', '>=', date('Y-m-d'))
        ->get();
        $zones = $this->zones
        ->where('status', 1)
        ->get();

        return response()->json([
            'villages' => $villages,
            'current_village' => $current_village,
            'zones' => $zones,
        ]);
    }

    public function admin_login(Request $request){
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required',
        ]);
        if ($validator->fails()) { // if Validate Make Error Return Message Error
            return response()->json([
                'errors' => $validator->errors(),
            ],400);
        }
        $user = $this->user
        ->where('email', $request->email)
        ->orWhere('phone', $request->email)
        ->first();
        if (empty($user)) {
            return response()->json(['errors'=>'creational not Valid'],403);
        }

        if ($user->status == 0) {
            return response()->json([
                'errors' => 'admin is banned'
            ], 400);
        }
        if (password_verify($request->input('password'), $user->password) && $user->role == 'admin') {
            $user->token = $user->createToken('admin')->plainTextToken;
            return response()->json([
                'admin' => $user,
                'token' => $user->token,
            ], 200);
        }
        else { 
            return response()->json(['errors'=>'creational not Valid'],403);
        }
    }
    

    public function village_login(Request $request){
        $validator = Validator::make($request->all(), [
            'email' => 'required',
            'password' => 'required',
        ]);
        if ($validator->fails()) { // if Validate Make Error Return Message Error
            $firstError = $validator->errors()->first();
            return response()->json([
                'errors' => $firstError,
            ],400);
        }
        $user = $this->user
        ->where('email', $request->email)
        ->orWhere('phone', $request->email)
        ->first();
        if (empty($user)) {
            return response()->json(['errors'=>'creational not Valid'],403);
        }

        if ($user->status == 0) {
            return response()->json([
                'errors' => 'user is banned'
            ], 400);
        }
        if (password_verify($request->input('password'), $user->password) && $user->role == 'village') {
            $user->token = $user->createToken('village')->plainTextToken;
            return response()->json([
                'village' => $user,
                'token' => $user->token,
            ], 200);
        }
        else { 
            return response()->json(['errors'=>'creational not Valid'],403);
        }
    }
    

    public function user_login(Request $request){
        $validator = Validator::make($request->all(), [
            'email' => 'required',
            'password' => 'required',
        ]);
        if ($validator->fails()) { // if Validate Make Error Return Message Error
            $firstError = $validator->errors()->first();
            return response()->json([
                'errors' => $firstError,
            ],400);
        }
        $user = $this->user
        ->where('email', $request->email)
        ->orWhere('phone', $request->email)
        ->first();
        if (empty($user)) {
            return response()->json(['errors'=>'creational not Valid'],403);
        }

        if ($user->status == 0) {
            return response()->json([
                'errors' => 'user is banned'
            ], 400);
        }
        if (password_verify($request->input('password'), $user->password) && $user->role == 'user') {
            $user->token = $user->createToken('user')->plainTextToken;
            return response()->json([
                'user' => $user,
                'token' => $user->token,
            ], 200);
        }
        else { 
            return response()->json(['errors'=>'creational not Valid'],403);
        }
    }

    public function sign_up(SignupRequest $request){
        $userRequest = $request->validated();
        $userRequest['user_type'] = 'visitor';
        $user = $this->user
        ->create($userRequest);
        $token = $user->createToken('user')->plainTextToken;

        return response()->json([
            'user' => $user,
            'token' => $token,
        ]);
    }
}
