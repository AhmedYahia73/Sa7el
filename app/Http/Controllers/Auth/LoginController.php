<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Http\Requests\Auth\SignupRequest;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use App\Events\NotificationEvent;
use Illuminate\Support\Facades\Mail;
use App\Mail\ForgetPasswordMail;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Str;

use App\Models\User;
use App\Models\PersonalAccessToken;
use App\Models\LoginRequest;
use App\Models\Village;
use App\Models\AppartmentType;
use App\Models\SecurityMan;
use App\Models\Package;
use App\Models\Notification;
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
        ->with("pool", "beach", "gate", "inside_gates")
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
            $gate = $user->gate->count() > 0;
            $beach = $user->beach->count() > 0;
            $pool = $user->pool->count() > 0;
            $inside_gate_pool = $user->inside_gates
            ->where("type", "pool")->count() > 0;
            $inside_gate_beach = $user->inside_gates
            ->where("type", "beach")->count() > 0;
            return response()->json([
                'security' => $user,
                'token' => $user->token,
                "gate" => $gate ,
                "beach" => $beach ,
                "pool" => $pool ,
                "inside_gate_pool" => $inside_gate_pool ,
                "inside_gate_beach" => $inside_gate_beach ,
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
		->where('role', '!=', 'user')
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
        elseif($user->role == 'maintenance_provider') {
            $user->maintenance_provider->village;
            PersonalAccessToken::
            whereDate('created_at', '<', date('Y-m-d'))
            ->where('name', 'maintenance_provider')
            ->delete(); 
            $personal = PersonalAccessToken::
            where('tokenable_id', $user->id)
            ->where('name', 'maintenance_provider')
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
            if (password_verify($request->input('password'), $user->password) && $user->role == 'maintenance_provider') {
                $user->token = $user->createToken('maintenance_provider')->plainTextToken;
                if ((!empty($user?->maintenance_provider?->from) && ($user->maintenance_provider->to < date('Y-m-d')
                || $user->maintenance_provider->from > date('Y-m-d'))) || empty($user?->maintenance_provider?->from)) {
                    $packages = $this->package
                    ->where('type', 'maintenance_provider')
                    ->get()
                    ->map(function($item) use($user){
                        return [
                            'id' => $item->id,
                            'name' => $item->name,
                            'description' => $item->description,
                            'price' => $item->price,
                            'feez' => $item->feez,
                            'discount' => $item->discount,
                            'admin_num' => $item->admin_num,
                            'my_package' => $user?->maintenance_provider?->package_id == $item->id ? 1 : 0,
                        ];
                    });
                    return response()->json([
                        'packages' => $packages,
                        'maintenance_provider' => $user,
                        'token' => $user->token,
                        'role' => $user->role,
                    ]);
                }
                return response()->json([
                    'maintenance_provider' => $user,
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
            "fcm_token" => "sometimes",
        ]);
        if ($validator->fails()) { // if Validate Make Error Return Message Error
            $firstError = $validator->errors()->first();
            return response()->json([
                'errors' => $firstError,
            ],400);
        }
        if(empty($request->password)){
            return response()->json([
                "errors" => "Password is required"
            ], 400);
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
            // if ($user->tokens()->exists()) {
            //     return response()->json([
            //         'errors' => 'already logged in from another device'
            //     ], 403);
            // }
            $user->fcm_token = $request->fcm_token ?? null;
            $user->save();
            $user->token = $user->createToken('user')->plainTextToken;
         
            return response()->json([
                'user' => $user,
                'token' => $user->token,
                "image_status" => $user->image ? true : false
            ], 200);
        }
        else {
            return response()->json(['errors'=>'creational not Valid'],403);
        }
    }

    // public function check_user_login_request(Request $request){
    //     $validator = Validator::make($request->all(), [
    //         'village_id' => 'required|exists:villages,id', 
    //         'appartment_id' => 'required|exists:appartments,id',
    //         'ip_address' => "sometimes"
    //     ]);
    //     if ($validator->fails()) { // if Validate Make Error Return Message Error
    //         $firstError = $validator->errors()->first();
    //         return response()->json([
    //             'errors' => $firstError,
    //         ],400);
    //     }
    //     $login_request_status = LoginRequest:: 
    //     where("user_id", auth()->user()->id)
    //     ->where("village_id", $request->village_id)
    //     ->where("appartment_id", $request->appartment_id)
    //     ->orderByDesc("id")
    //     ->first()?->status;
    //     $ip_address = $request->ip_address ?? $request->ip();
    //     if($request->user()->ip_address != $ip_address){
    //         if(empty($login_request_status)){
    //             $login_request = LoginRequest::create([
    //                 "user_id" => $request->user()->id,
    //                 "ip_address" => $ip_address,
    //                 "status" => "approve",
    //                 "village_id" => $request->village_id,
    //                 "appartment_id" => $request->appartment_id,
    //             ]);
    //             auth()->user()->update(['ip_address' => $ip_address]);
    //         }
    //         else{ 
    //             $login_request = LoginRequest::create([
    //                 "user_id" => $request->user()->id,
    //                 "ip_address" => $ip_address,
    //                 "status" => "pending",
    //                 "village_id" => $request->village_id,
    //                 "appartment_id" => $request->appartment_id,
    //             ]);
    //             auth()->user()->update(['ip_address' => $ip_address]);
    //             $notification = "قام " . auth()->user()->name . " بمحاولة الدخول من الابليكشن";
    //             $data = [
    //                 'village_id' => $request->village_id,
    //                 'code_request_id' => null,
    //                 'login_request_id' => $login_request->id,
    //                 "type" => "admin", // user, admin
    //                 'notification' => $notification,
    //             ];
    //             Notification::create($data);
    //             NotificationEvent::dispatch($data);
    //         }
    //     }
    //     $login_request = LoginRequest::
    //     where("ip_address", $ip_address)
    //     ->where("user_id", auth()->user()->id)
    //     ->where("village_id", $request->village_id)
    //     ->orderByDesc("id")
    //     ->first()?->status ==  "approve" || empty($login_request_status) 
    //     ? true : false;

    //     return response()->json([
    //         "login" => $login_request
    //     ]);
    // }
    public function check_user_login_request(Request $request){
        $validator = Validator::make($request->all(), [
            'village_id' => 'required|exists:villages,id', 
            'appartment_id' => 'required|exists:appartments,id',
            'ip_address' => "required" // خليتها required لأنك بتعتمد عليها كـ Unique ID
        ]);

        if ($validator->fails()) { 
            return response()->json([
                'errors' => $validator->errors()->first(),
            ], 400);
        }
        return response()->json(["login" => true]);

        $userId = auth()->user()->id;
        $ip_address = $request->ip_address;

        // هنجيب آخر طلب متسجل للمستخدم في القرية والشقة دي بالظبط (طلب واحد بس)
        $last_request = LoginRequest::where("user_id", $userId)
            ->where("village_id", $request->village_id)
            ->where("appartment_id", $request->appartment_id)
            ->orderByDesc("id")
            ->first();

        // 1. لو مفيش أي طلبات خالص (أول مرة يدخل الشقة دي)
        if (!$last_request) {
            LoginRequest::create([
                "user_id" => $userId,
                "ip_address" => $ip_address,
                "status" => "approve",
                "village_id" => $request->village_id,
                "appartment_id" => $request->appartment_id,
            ]);
            
            $request->user()->update(['ip_address' => $ip_address]);

            return response()->json(["login" => true]);
        }

        // 2. لو فيه طلب سابق، هنشوف هل الجهاز الحالي هو نفسه "آخر جهاز" ولا اتغير؟
        if ($last_request->ip_address == $ip_address) {
            // طالما هو نفس الجهاز الأخير، هنشوف حالته
            if ($last_request->status == "approve") {
                return response()->json(["login" => true]); // يدخل علطول
            } else {
                // لو كان لسه Pending، هيرجع False ومش هيبعت إشعار تاني (عشان ميغرقش الأدمن إشعارات لو فضل يدوس)
                return response()->json(["login" => false]);
            }
        } 
        // 3. لو الجهاز اتغير (جهاز جديد، أو جهاز قديم ورجعله تاني)
        else {
            // نكريت طلب جديد خالص وحالته Pending
            $login_request = LoginRequest::create([
                "user_id" => $userId,
                "ip_address" => $ip_address,
                "status" => "pending",
                "village_id" => $request->village_id,
                "appartment_id" => $request->appartment_id,
            ]);

            $request->user()->update(['ip_address' => $ip_address]);

            $notification = "قام " . auth()->user()->name . " بمحاولة الدخول من جهاز مختلف";
            $data = [
                'village_id' => $request->village_id,
                'code_request_id' => null,
                'login_request_id' => $login_request->id,
                "type" => "admin", 
                'notification' => $notification,
            ];
            
            Notification::create($data);
            NotificationEvent::dispatch($data);

            // نقفل عليه لحد ما الأدمن يوافق
            return response()->json(["login" => false]);
        }
    }
    
    public function sign_up(SignupRequest $request){
        $users = User::
        where(function($query) use($request){
            $query->where("email", $request->email)
            ->orWhere("phone", $request->phone);
        })
        ->where("role", "user")
        ->first();
        if($users){
            return response()->json([
                "errors" => "email or phone is enrolled"
            ], 400);
        }
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
    
    public function logout(Request $request) { 
        $user = auth()->user();
        
        // بيمسح التوكن الحالي المستخدم في الطلب ده فقط
        $deleteToken = $user->currentAccessToken()->delete();

        if ($deleteToken) {
            return response()->json([
                'success' => 'You logged out successfully'
            ]);
        } else {
            return response()->json([
                'failed' => 'Failed to logout'
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

    public function forget_password(Request $request){
        $validator = Validator::make($request->all(), [
            'email' => 'required|exists:users,email',
        ]);
        if ($validator->fails()) { // if Validate Make Error Return Message Error
            $firstError = $validator->errors()->first();
            return response()->json([
                'errors' => $firstError,
            ],400);
        }
        $code = rand(1000000, 9999999);
        Mail::to($request->email)->send(new ForgetPasswordMail($code));
        User::
        where("email", $request->email)
        ->update([
            "code" => $code
        ]);

        return response()->json([
            "success" => "You update code success"
        ]);
    }

    public function check_forget_password(Request $request){
        $validator = Validator::make($request->all(), [
            'email' => 'required|exists:users,email',
            "code"  => "required", 
        ]);
        if ($validator->fails()) { // if Validate Make Error Return Message Error
            $firstError = $validator->errors()->first();
            return response()->json([
                'errors' => $firstError,
            ],400);
        }  
        $user = User::
        where("email", $request->email)
        ->where("code", $request->code)
        ->first();

        return response()->json([
            "check_code" => $user ? true : false
        ]);
    }

    public function update_password(Request $request){
        $validator = Validator::make($request->all(), [
            'email' => 'required|exists:users,email',
            "code"  => "required", 
            "password"  => "required", 
        ]);
        if ($validator->fails()) { // if Validate Make Error Return Message Error
            $firstError = $validator->errors()->first();
            return response()->json([
                'errors' => $firstError,
            ],400);
        }  
        $user = User::
        where("email", $request->email)
        ->where("code", $request->code)
        ->update([
            "password" => bcrypt($request->password),
            "code" => null,
        ]);

        return response()->json([
            "success" => "You update data success"
        ]);
    }

    public function google_login(Request $request) {
        // 1. التحقق من وصول التوكن من الفرونت إند
        $validator = Validator::make($request->all(), [
            'access_token' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors()->first(),
            ], 400);
        }

        try {
            // 2. التحقق من ال
            $googleUser = Socialite::driver('google')->stateless()->userFromToken($request->access_token);
        } catch (\Exception $e) {
            return response()->json([
                'errors' => 'Invalid Google token or expired',
            ], 401);
        }

        // 3. البحث عن المستخدم بالإيميل
        $user = $this->user->where('email', $googleUser->getEmail())->first();

        if (empty($user)) {
            $validator = Validator::make($request->all(), [
                'phone' => 'required|unique:users,phone', // التوكن المرسل من الفرونت إند
            ]); 
            if ($validator->fails()) {
                return response()->json([
                    'errors' => $validator->errors()->first(),
                ], 400);
            }
            // إذا كان مستخدم جديد تماماً، نقوم بإنشائه
            $user = $this->user->create([
                'name' => $googleUser->getName(),
                'email' => $googleUser->getEmail(),
                'google_id' => $googleUser->getId(),
                "phone" => $request->phone,
                'role' => 'user', // تحديد الرول الافتراضي كما في كودك
                'status' => 1,    // مستخدم نشط
                'password' => bcrypt(Str::random(16)), // باسورد عشوائي معمي لحماية الحساب
            ]);
        } else {
            // إذا كان الحساب موجود مسبقاً بالإيميل ولكن لم يربط بجوجل بعد
            if (empty($user->google_id)) {
                $user->update(['google_id' => $googleUser->getId()]);
            }
        }

        // 4. تطبيق شروطك الخاصة (نفس شروط الدالة العادية)
        
        // أ. التأكد من أن الحساب مش Banned
        if ($user->status == 0) {
            return response()->json([
                'errors' => 'user is banned'
            ], 400);
        }

        // ب. التأكد من الرول (Role)
        if ($user->role != 'user') {
            return response()->json([
                'errors' => 'credentials not Valid'
            ], 403);
        }

        // ج. التأكد من عدم تسجيل الدخول من جهاز آخر (Single Device Check)
        if ($user->tokens()->exists()) {
            return response()->json([
                'errors' => 'already logged in from another device'
            ], 403);
        }

        // 5. إنشاء توكن جديد وإرجاع البيانات
        $user->token = $user->createToken('user')->plainTextToken;

        return response()->json([
            'user' => $user,
            'token' => $user->token,
        ], 200);
    }

    public function apple_login(Request $request) {
        $validator = Validator::make($request->all(), [
            'access_token' => 'required', // التوكن المرسل من الفرونت إند
            'name' => 'nullable|string',   // الاسم المرسل من الفرونت (مهم لأول مرة تسجيل)
        ]);

        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors()->first(),
            ], 400);
        }

        try {
            // 2. التحقق من التوكن وجلب البيانات من سيرفرات آبل
            $appleUser = Socialite::driver('apple')->userFromToken($request->access_token);
        } catch (\Exception $e) {
            return response()->json([
                'errors' => 'Invalid Apple token or expired',
            ], 401);
        }

        // 3. البحث عن المستخدم (نبحث بالـ apple_id أولاً ثم الإيميل كخيار بديل)
        $user = $this->user->where('apple_id', $appleUser->getId())
            ->orWhere('email', $appleUser->getEmail())
            ->first();

        if (empty($user)) {
            $validator = Validator::make($request->all(), [
                'phone' => 'required|unique:users,phone', // التوكن المرسل من الفرونت إند
            ]); 
            if ($validator->fails()) {
                return response()->json([
                    'errors' => $validator->errors()->first(),
                ], 400);
            }
            // إذا كان مستخدم جديد، ننشئ الحساب
            // لو آبل مش بعتت الإيميل (بسبب خيارخفاء الإيميل)، آبل بتعمل إيميل وهمي ينتهي بـ @privaterelay.apple.com وهو شغال عادي.
            $user = $this->user->create([
                'name' => $request->name ?? $appleUser->getName() ?? 'Apple User',
                'email' => $appleUser->getEmail(),
                'apple_id' => $appleUser->getId(),
                'role' => 'user',
                'status' => 1,
                "phone" => $request->phone,
                'password' => bcrypt(Str::random(16)),
            ]);
        } else {
            // لو الحساب موجود مسبقاً بس مش مربوط بآبل
            if (empty($user->apple_id)) {
                $user->update(['apple_id' => $appleUser->getId()]);
            }
        }

        // 4. تطبيق نفس قيود الحماية بتاعتك
        
        // التأكد من الحظر
        if ($user->status == 0) {
            return response()->json([
                'errors' => 'user is banned'
            ], 400);
        }

        // التأكد من الرول
        if ($user->role != 'user') {
            return response()->json([
                'errors' => 'credentials not Valid'
            ], 403);
        }

        // التأكد من عدم تسجيل الدخول من جهاز آخر
        if ($user->tokens()->exists()) {
            return response()->json([
                'errors' => 'already logged in from another device'
            ], 403);
        }

        // 5. إصدار التوكن وإرجاع الاستجابة
        $user->token = $user->createToken('user')->plainTextToken;

        return response()->json([
            'user' => $user,
            'token' => $user->token,
        ], 200);
    }
}
