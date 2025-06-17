<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Http\Requests\Auth\SignupRequest;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Illuminate\Support\Facades\Storage;

use App\Models\User;
use App\Models\PersonalAccessToken;
use App\Models\Village;
use App\Models\AppartmentType;
use App\Models\SecurityMan;
use App\Models\Package;
use App\Models\Zone;

class LoginController extends Controller
{
    public function __construct(private User $user, private Village $village,
    private Zone $zones, private AppartmentType $appartment_types,
    private SecurityMan $secuity, private Package $package){}

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
                'roles' => $user->position->sup_roles,
                'token' => $user->token,
            ], 200);
        }
        else { 
            return response()->json(['errors'=>'creational not Valid'],403);
        }
    }

    public function security_login(Request $request){
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required',
        ]);
        if ($validator->fails()) { // if Validate Make Error Return Message Error
            return response()->json([
                'errors' => $validator->errors(),
            ],400);
        }
        PersonalAccessToken::
        whereDate('created_at', '<', date('Y-m-d'))
        ->where('name', 'security')
        ->delete();
        $user = $this->secuity
        ->where('email', $request->email)
        ->first();
        if (empty($user)) {
            return response()->json(['errors'=>'creational not Valid'],403);
        }
        $personal = PersonalAccessToken::
        where('tokenable_id', $user->id)
        ->where('name', 'security')
        ->first();
        if (!empty($personal)) {
            return response()->json([
                'errors' => 'You log from another device'
            ], 400);
        }

        if ($user->status == 0) {
            return response()->json([
                'errors' => 'security is banned'
            ], 400);
        }
        if (password_verify($request->input('password'), $user->password)) {
            $user->token = $user->createToken('security')->plainTextToken;
            return response()->json([
                'security' => $user,
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
        ->first();
        
        if (empty($user)) {
            return response()->json(['errors'=>'creational not Valid'],403);
        }

        if ($user->role == 'village') {
            $user->village->zone;
            PersonalAccessToken::
            whereDate('created_at', '<', date('Y-m-d'))
            ->where('name', 'village')
            ->delete(); 
            $personal = PersonalAccessToken::
            where('tokenable_id', $user->id)
            ->where('name', 'village')
            ->first();
            if (!empty($personal)) {
                return response()->json([
                    'errors' => 'You log from another device'
                ], 400);
            }

            if ($user->status == 0) {
                return response()->json([
                    'errors' => 'user is banned'
                ], 400);
            }
            if (password_verify($request->input('password'), $user->password) && $user->role == 'village') {
                $user->token = $user->createToken('village')->plainTextToken;
                if ((!empty($user?->village?->from) && ($user->village->to < date('Y-m-d')
                || $user->village->from > date('Y-m-d'))) || empty($user?->village?->from)) {
                    $packages = $this->package
                    ->where('type', 'village')
                    ->get()
                    ->map(function($item) use($user){
                        return [
                            'id' => $item->id,
                            'name' => $item->name,
                            'description' => $item->description,
                            'price' => $item->price,
                            'feez' => $item->feez,
                            'discount' => $item->discount,
                            'beach_pool_module' => $item->beach_pool_module,
                            'maintenance_module' => $item->maintenance_module,
                            'security_num' => $item->security_num,
                            'admin_num' => $item->admin_num,
                            'admin_num' => $item->admin_num,
                            'my_package' => $user?->village?->package_id == $item->id ? 1 : 0,
                        ];
                    });
                    return response()->json([
                        'packages' => $packages,
                        'village' => $user,
                        'token' => $user->token,
                        'role' => $user->role,
                    ]);
                }
                return response()->json([
                    'village' => $user,
                    'token' => $user->token,
                    'role' => $user->role,
                ], 200);
            }
            else { 
                return response()->json(['errors'=>'creational not Valid'],403);
            }
        } 
        elseif($user->role == 'provider') {
            $user->provider->zone;
            $user->provider->village;
            PersonalAccessToken::
            whereDate('created_at', '<', date('Y-m-d'))
            ->where('name', 'provider')
            ->delete(); 
            $personal = PersonalAccessToken::
            where('tokenable_id', $user->id)
            ->where('name', 'provider')
            ->first();
            if (!empty($personal)) {
                return response()->json([
                    'errors' => 'You log from another device'
                ], 400);
            }

            if ($user->status == 0) {
                return response()->json([
                    'errors' => 'user is banned'
                ], 400);
            }
            if (password_verify($request->input('password'), $user->password) && $user->role == 'provider') {
                $user->token = $user->createToken('provider')->plainTextToken;
                if ((!empty($user?->provider?->from) && ($user->provider->to < date('Y-m-d')
                || $user->provider->from > date('Y-m-d'))) || empty($user?->provider?->from)) {
                    $packages = $this->package
                    ->where('type', 'provider')
                    ->get()
                    ->map(function($item) use($user){
                        return [
                            'id' => $item->id,
                            'name' => $item->name,
                            'description' => $item->description,
                            'price' => $item->price,
                            'feez' => $item->feez,
                            'discount' => $item->discount,
                            'beach_pool_module' => $item->beach_pool_module,
                            'maintenance_module' => $item->maintenance_module,
                            'security_num' => $item->security_num,
                            'admin_num' => $item->admin_num,
                            'admin_num' => $item->admin_num,
                            'my_package' => $user?->provider?->package_id == $item->id ? 1 : 0,
                        ];
                    });
                    return response()->json([
                        'packages' => $packages,
                        'provider' => $user,
                        'token' => $user->token,
                        'role' => $user->role,
                    ]);
                }
                return response()->json([
                    'provider' => $user,
                    'token' => $user->token,
                    'role' => $user->role,
                ], 200);
            }
            else { 
                return response()->json(['errors'=>'creational not Valid'],403);
            }
        }

        return response()->json([
            'errors' => 'You do not provider or village',
        ], 400);

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
        $userRequest['role'] = 'user';  
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

    public function delete_account(Request $request){
        // /api/delete_account
        $user = $request->user()->delete(); 
        
        return response()->json([
            'success' => 'You delete data success'
        ]);
    }
}
