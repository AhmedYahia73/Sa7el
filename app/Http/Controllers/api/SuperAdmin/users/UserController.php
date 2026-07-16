<?php

namespace App\Http\Controllers\api\SuperAdmin\users;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\SuperAdmin\UserRequest;
use Illuminate\Support\Facades\Validator;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Illuminate\Support\Facades\Storage;
use App\trait\TraitImage;
use Illuminate\Validation\Rule;
use Carbon\Carbon;

use App\Models\User;
use App\Models\Village;
use App\Models\AppartmentCode;
use App\Models\Offer;

class UserController extends Controller
{
    public function __construct(private User $user,
    private Village $village, private AppartmentCode $appartment_code,
    private Offer $offer){}
    use TraitImage;

    public function view(){
        $users = $this->user
        ->select('id', 'name', 'email', 'phone', 'birthDate', 'verification',
        'user_type', 'village_id', 'image', 'parent_user_id', 'status', 'gender')
        ->with('villages_user', 'parent')
        ->where('role', 'user')
        ->get()
        ->map(function($item){
            $user_type_owner = $item->appartment_code->where('type', 'owner')->values();
            $user_type_renter = $item->appartment_code->where('type', 'renter')
            ->where('from', '<=', date('Y-m-d'))
            ->where('to', '>=', date('Y-m-d'))->values();
            $type = 'Visitor';
            if (count($item->appartment_code) > 0) {
                if (count($user_type_owner) > 0) {
                    $type = 'Owner';
                }
                elseif(count($user_type_renter) > 0){
                    $type = 'Renter';
                }
                else{
                    $type = 'Visitor';
                }
            }
            return [
                'id' => $item->id,
                'name' => $item->name,
                'email' => $item->email,
                'phone' => $item->phone,
                'user_type' => $type,
                'village_id' => $item->village_id,
                'image' => $item->image_link,
                'parent_user_id' => $item->parent_user_id,
                'status' => $item->status,
                'gender' => $item->gender,
                'villages_user' => $item->villages_user,
                'parent' => $item->parent,
                "verification" => $item->verification,
            ];
        });
        $village = $this->village
        ->get();

        return response()->json([
            'users' => $users,
            'village' => $village,
        ]);
    }

    public function users(Request $request){
        $perPage = 15;
        $search = $request->search; // أو request('search') حسب مكان الكود

        $users = $this->user
            ->select('id', 'name', 'email', 'phone', 'birthDate', 'user_type', 
            'village_id', 'image', 'parent_user_id', 'status', 'gender', 'verification')
            ->with(['villages_user', 'parent', 'appartment_code'])
            ->where('role', 'user')
            
            // --- بداية كود البحث ---
            ->when($search, function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%");
                });
            })
            // --- نهاية كود البحث ---
            
            ->paginate($perPage)
            ->through(function($item) {
                $user_type_owner = $item->appartment_code->where('type', 'owner')->values();
                $user_type_renter = $item->appartment_code->where('type', 'renter')
                    ->where('from', '<=', date('Y-m-d'))
                    ->where('to', '>=', date('Y-m-d'))->values();
                    
                $type = 'Visitor';
                if (count($item->appartment_code) > 0) {
                    if (count($user_type_owner) > 0) {
                        $type = 'Owner';
                    }
                    elseif(count($user_type_renter) > 0){
                        $type = 'Renter';
                    }
                    else{
                        $type = 'Visitor';
                    }
                }
                
                return [
                    'id' => $item->id,
                    'name' => $item->name,
                    'email' => $item->email,
                    'phone' => $item->phone,
                    'user_type' => $type,
                    'village_id' => $item->village_id,
                    'image' => $item->image_link,
                    'parent_user_id' => $item->parent_user_id,
                    'status' => $item->status,
                    'gender' => $item->gender,
                    'villages_user' => $item->villages_user,
                    'parent' => $item->parent,
                    'favourite' => $item->favourite,
                    "verification" => $item->verification,
                ];
            });
        $village = $this->village
        ->get();

        return response()->json([
            'users' => $users,
            'village' => $village,
        ]);
    }

    public function favourite_users(Request $request){
        $perPage = 15;
        $search = $request->search; // أو request('search') حسب مكان الكود

        $users = $this->user
            ->select('id', 'name', 'email', 'phone', 'birthDate', 'user_type', 'village_id', 'image', 'parent_user_id', 'status', 'gender')
            ->with(['villages_user', 'parent', 'appartment_code'])
            ->where('role', 'user')
            ->where("favourite", true)
            
            // --- بداية كود البحث ---
            ->when($search, function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%");
                });
            })
            // --- نهاية كود البحث ---
            
            ->paginate($perPage)
            ->through(function($item) {
                $user_type_owner = $item->appartment_code->where('type', 'owner')->values();
                $user_type_renter = $item->appartment_code->where('type', 'renter')
                    ->where('from', '<=', date('Y-m-d'))
                    ->where('to', '>=', date('Y-m-d'))->values();
                    
                $type = 'Visitor';
                if (count($item->appartment_code) > 0) {
                    if (count($user_type_owner) > 0) {
                        $type = 'Owner';
                    }
                    elseif(count($user_type_renter) > 0){
                        $type = 'Renter';
                    }
                    else{
                        $type = 'Visitor';
                    }
                }
                
                return [
                    'id' => $item->id,
                    'name' => $item->name,
                    'email' => $item->email,
                    'phone' => $item->phone,
                    'user_type' => $type,
                    'village_id' => $item->village_id,
                    'image' => $item->image_link,
                    'parent_user_id' => $item->parent_user_id,
                    'status' => $item->status,
                    'gender' => $item->gender,
                    'villages_user' => $item->villages_user,
                    'parent' => $item->parent,
                    'favourite' => $item->favourite,
                ];
            }); 

        return response()->json([
            'users' => $users, 
        ]);
    }

    public function make_user_favourite(Request $request, $id){
        $user = User::
        where("id", $id)
        ->first();
        $user->favourite = !$user->favourite;
        $user->save();
        
        return response()->json([
            'success' => "You update data success",
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
        $properties = $this->appartment_code
        ->where('user_id', $id)
        ->where('type', 'owner')
        ->get()
        ->map(function($item){
            return [
                'id' => $item->id,
                'village' => $item?->village?->name,
                'icon' => $item?->village?->image_link,
                'cover_image' => $item?->village?->cover_image_link,
                'unit' => $item?->appartment?->unit,
            ];
        });
        $offer = $this->offer
        ->where('owner_id', $id)
        ->whereHas('offer_status', function($query){
            $query->where('rent_status', 1)
            ->orWhere('sale_status', 1);
        })
        ->get()
        ->map(function($item){
            $type_offer = null;
            if ($item->offer_status->sale_status && $item->offer_status->rent_status) {
                $type_offer = 'Sale & Rent';
            }
            elseif ($item->offer_status->sale_status) {
                $type_offer = 'Sale';
            }
            elseif ($item->offer_status->rent_status) {
                $type_offer = 'Rent';
            }
            return [
                'id' => $item->id,
                'village' => $item?->village?->name,
                'image' => $item?->village?->image_link,
                'cover_image' => $item?->village?->cover_image_link,
                'owner' => $item?->owner?->name,
                'unit' => $item?->appartment?->unit,
                'unit' => $item?->appartment?->unit,
                'description' => $item->description,
                'type_offer' => $type_offer,
                'price_day' => $item->price_day,
                'price_month' => $item->price_month,
                'price' => $item->price,
            ];
        });
        $appartment_code = $appartment_code->where('type', 'renter')
        ->where('from', '<=', date('Y-m-d'))
        ->where('to', '>=', date('Y-m-d'))->values();
        $type = 'Visitor';
        if (count($appartment_code) > 0) {
            if ($appartment_code[0]->type == "owner") {
                $type = 'Owner';
            }
            elseif($appartment_code[0]->type == "renter"){
                $type = 'Renter';
            }
            else{
                $type = 'Visitor';
            }
        }
        unset($user->user_type);
        $user->user_type = $type;
        
        return response()->json([
            'user' => $user,
            'properties' => $properties,
            'offers' => $offer,
        ]);
    }
 
    public function online_user_units($id){
        $units = AppartmentCode::
        where("user_id", $id)
        ->with("appartment")
        ->get()
        ->map(function($item){
            return [
                "id" => $item->id,
                "type" => $item->type,
                "from" => $item->from,
                "to" => $item->to,
                "people" => $item->people,
                "unit" => $item?->appartment?->unit,
            ];
        });

        return response()->json([
            "units" => $units
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
            'email' => [
            Rule::unique('users')->where(function ($query) {
                return $query->where('role', 'user');
            })],
            'phone' => [
            Rule::unique('users')->where(function ($query) {
                return $query->where('role', 'user');
            })],
            'password' => ['required'],
        ]);
        if ($validator->fails()) { // if Validate Make Error Return Message Error
            return response()->json([
                'errors' => $validator->errors(),
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
        $data = $user->id;
        $qrCode = QrCode::format('png')->size(300)->generate($data);
        $fileName = 'user/qr/' . $data . '.png';
        Storage::disk('public')->put($fileName, $qrCode); // Save the image
        $user->qr_code = $fileName;
        $user->save();

        return response()->json([
            'success' => 'You add data success'
        ]);
    }

    public function modify(UserRequest $request, $id){
        // name, user_type, email, phone
        // password, status, parent_user_id, gender, birthDate
        $validator = Validator::make($request->all(), [
            'email' => ['email', Rule::unique('users')->where(function ($query) use($id) {
                return $query->where('role', 'user')
                ->where('id', '!=', $id);
            })],
            'phone' => [Rule::unique('users')->where(function ($query) use($id) {
                return $query->where('role', 'user')
                ->where('id', '!=', $id);
            })],
        ]);
        if ($validator->fails()) { // if Validate Make Error Return Message Error
            return response()->json([
                'errors' => $validator->errors(),
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

    public function user_active(Request $request){
        $validator = Validator::make($request->all(), [
            'village_id' => ['required', 'exists:villages,id'], 
        ]);
        if ($validator->fails()) { // if Validate Make Error Return Message Error
            return response()->json([
                'errors' => $validator->errors(),
            ],400);
        }
        $admins = User::
        whereHas("appartment_code", function($query) use($request){
            $query->where("village_id", $request->village_id)
            ->where("from", "<=", now())
            ->where("to", ">=", now())
            ->orWhere("type", "owner")
            ->where("village_id", $request->village_id);
        })
        ->whereHas('tokens') 
        ->get()
        ->map(function($item){
            return [
                "id" => $item->id,
                "name" => $item->name,
                "email" => $item->email,
                "phone" => $item->phone,
            ];
        });

        return response()->json([
            "admins" => $admins
        ]);
    }

    public function logout_user($id){
        $user = User::
        where("id", $id)
        ->first();
        if ($user) {
            // حذف جميع التوكنز الخاصة بهذا المستخدم
            $user->tokens()->delete();
            
            return response()->json(['message' => 'تم تسجيل الخروج بنجاح من جميع الأجهزة']);
        }

        return response()->json(['message' => 'المستخدم غير موجود'], 404);
    }

    public function units(Request $request, $id){
        $properties = AppartmentCode::where("user_id", $id)
            ->where("type", "owner")
            ->with(["appartment", "village"])
            ->get();

        $formattedProperties = $properties->map(function ($item) {
            return [
                "id"            => $item->id,
                "people"        => $item->people,
                "image_id_link" => $item->image_id_link,
                "village"       => $item->village?->name,
                "unit"          => $item->appartment?->unit,
            ];
        });

        $apartmentIds = $properties->pluck('appartment_id')->filter()->unique();

        $rents = AppartmentCode::whereIn("appartment_id", $apartmentIds)
            ->where("type", "renter")
            ->with(["appartment", "village"])
            ->get()
            ->unique("code")
            ->map(function ($item) {
                $now = now();
                $from = Carbon::parse($item->from);
                $to = Carbon::parse($item->to);

                if ($from->lessThanOrEqualTo($now) && $to->greaterThanOrEqualTo($now)) {
                    $status = "Current";
                } elseif ($to->greaterThanOrEqualTo($now)) {
                    $status = "Upcoming";
                } else {
                    $status = "Past";
                }

                return [
                    "id"            => $item->id,
                    "people"        => $item->people,
                    "image_id_link" => $item->image_id_link,
                    "from"          => $item->from,
                    "to"            => $item->to,
                    "village"       => $item->village?->name,
                    "unit"          => $item->appartment?->unit,
                    "status"        => $status,
                ];
            })->values();

        return response()->json([
            "property" => $formattedProperties,
            "rents"    => $rents,
        ]);
    }
     
    public function delete_user(Request $request){
        $validator = Validator::make($request->all(), [
            'id' => 'required|exists:appartment_codes,id',
        ]);
        if ($validator->fails()) { // if Validate Make Error Return Message Error
            return response()->json([
                'errors' => $validator->errors(),
            ],400);
        }

        $code = AppartmentCode::
        where("id", $request->id)
        ->update([
            "user_id" => null
        ]);

        return response()->json([
            "success" => "You delete data success"
        ]);
    }

    public function delete_code(Request $request){
        $validator = Validator::make($request->all(), [
            'code' => 'required', 
        ]);
        if ($validator->fails()) { // if Validate Make Error Return Message Error
            return response()->json([
                'errors' => $validator->errors(),
            ],400);
        }

        $code = AppartmentCode::
        where("code", $request->code) 
        ->delete();

        return response()->json([
            "success" => "You delete data success"
        ]);
    }
}
