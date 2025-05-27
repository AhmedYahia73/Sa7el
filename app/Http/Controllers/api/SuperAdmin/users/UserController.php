<?php

namespace App\Http\Controllers\api\SuperAdmin\users;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\SuperAdmin\UserRequest;
use Illuminate\Support\Facades\Validator;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Illuminate\Support\Facades\Storage;
use App\trait\image;

use App\Models\User;
use App\Models\Village;
use App\Models\AppartmentCode;
use App\Models\Offer;

class UserController extends Controller
{
    public function __construct(private User $user,
    private Village $village, private AppartmentCode $appartment_code,
    private Offer $offer){}
    use image;

    public function view(){
        $users = $this->user
        ->select('id', 'name', 'email', 'phone', 'birthDate',
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
            ];
        });
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

        return response()->json([
            'user' => $user,
            'properties' => $properties,
            'offers' => $offer,
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
            'email' => ['unique:users'],
            'phone' => ['unique:users'],
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
            'email' => ['email', 'unique:users,email,' . $id],
            'phone' => ['unique:users,phone,' . $id],
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
}
