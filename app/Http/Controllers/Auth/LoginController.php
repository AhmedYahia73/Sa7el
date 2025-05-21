<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Http\Requests\Auth\SignupRequest;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

use App\Models\User;
use App\Models\Village;
use App\Models\AppartmentType;
use App\Models\Zone;

class LoginController extends Controller
{
    public function __construct(private User $user, private Village $village,
    private Zone $zones, private AppartmentType $appartment_types){}

    public function sign_up_list(Request $request){
        $local = $request->local == 'ar' ? 1 : 0;
        $villages = $this->village
        ->where('status', 1)
        ->get();
        $current_village = $this->village
        ->where('status', 1)
        ->where('to', '>=', date('Y-m-d'))
        ->get();
        $zones = $this->zones
        ->where('status', 1)
        ->get()
        ->map(function($item) use($local){
            return [
                'id' => $item->id,
                'name' => !$local ? $item->name : $item->ar_name ?? $item->name,
            ];
        });
        $appartment_types = $this->appartment_types
        ->where('status', 1)
        ->get()
        ->map(function($item) use($local){
            return [
                'id' => $item->id,
                'name' => !$local ? $item->name : $item->ar_name ?? $item->name,
            ];
        });

        return response()->json([
            'villages' => $villages,
            'current_village' => $current_village,
            'zones' => $zones,
            'appartment_types' => $appartment_types,
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
        ->with('village.zone')
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
        $data = $user->id;
        $qrCode = QrCode::format('png')->size(300)->generate($data);
        $fileName = 'user/qr/' . $data . '.png';
        Storage::disk('public')->put($fileName, $qrCode); // Save the image
        $user->qr_code = $fileName;
        $user->save();
        $token = $user->createToken('user')->plainTextToken;

        return response()->json([
            'user' => $user,
            'token' => $token,
        ]);
    }

    public function logout(Request $request){ 
        $user =auth()->user();
        $deletToken = $user->tokens()->delete();
        if ($deletToken) {
            return response()->json([
                'success' => 'You logout success'
            ]);
        } else {
            return response()->json([
                'faild' => 'You faild to logout'
            ], 400);
        }
    }
}
