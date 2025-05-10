<?php

namespace App\Http\Controllers\api\Village\Owner;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\trait\image;
use App\Http\Requests\Village\OwnerRequest;

use App\Models\User;
use App\Models\EntranceBeach;
use App\Models\EntranceGate;
use App\Models\EntrancePool;
use App\Models\Rent;
use App\Models\ProblemReport;
use App\Models\Maintenance;

class OwnerController extends Controller
{
    public function __construct(private User $owners){}
    use image;

    public function view(Request $request){
        $owners = $this->owners
        ->where('user_type', 'owner')
        ->where('user_type', 'owner')
        ->get();
        $parent = $this->owners
        ->where('user_type', 'owner')
        ->where('user_type', 'owner')
        ->whereNull('parent_user_id')
        ->get();

        return response()->json([
            'owners' => $owners,
            'parents' => $parent,
        ]);
    }

    public function owner(Request $request, $id){
        $owner = $this->owners
        ->where('user_type', 'owner')
        ->where('id', $id)
        ->first();
        $entrance =  [
            'gates' => EntranceGate::with('gate')
            ->where('user_id', $id)->get(),
            'beaches' => EntranceBeach::with('beach')
            ->where('user_id', $id)->get(),
            'pools' => EntrancePool::with('pool')
            ->where('user_id', $id)->get(),
        ];
        $rent = Rent::
        with('renter', 'unit', 'unit_type')
        ->where('owner_id', $id)
        ->get();
        $problem_request = ProblemReport::where('user_id', $id)->get();
        $maintenance_request = Maintenance::
        with('maintenance_type')
        ->where('user_id', $id)->get();

        return response()->json([
            'owner' => $owner,
            'entrance' => $entrance,
            'rent' => $rent,
            'problem_request' => $problem_request,
            'maintenance_request' => $maintenance_request,

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

        $owner = $this->owners
        ->where('village_id', $request->user()->village_id)
        ->where('user_type', 'owner')
        ->where('id', $id)
        ->update([
            'status' => $request->status
        ]);

        return response()->json([
            'owner' => $owner,
        ]);
    }

    public function create(OwnerRequest $request){
        $validator = Validator::make($request->all(), [
            'email' => 'unique:users,email',
            'phone' => 'unique:users,phone',
            'password' => 'required',
        ]);
        if ($validator->fails()) { // if Validate Make Error Return Message Error
            return response()->json([
                'errors' => $validator->errors(),
            ],400);
        }

        $ownerRequest = $request->validated();
        $ownerRequest['user_type'] = 'owner';
        $ownerRequest['role'] = 'user';
        $ownerRequest['password'] = $request->password;
        $ownerRequest['village_id'] = $request->user()->village_id;
        if ($request->has('image')) {
            $image_path = $this->upload($request, 'image', '/village/owner');
            $ownerRequest['image'] = $image_path;
        }
        $this->owners
        ->create($ownerRequest);

        return response()->json([
            'success' => 'You add data success'
        ]);
    }

    public function modify(OwnerRequest $request, $id){
        $validator = Validator::make($request->all(), [
            'email' => 'unique:users,email,' . $id . ',id',
            'phone' => 'unique:users,phone,' . $id . ',id',
        ]);
        if ($validator->fails()) { // if Validate Make Error Return Message Error
            return response()->json([
                'errors' => $validator->errors(),
            ],400);
        }

        $owner = $this->owners
        ->where('id', $id)
        ->first();
        if (empty($owner)) {
            return response()->json([
                'errors' => 'owner not found'
            ], 400);
        }
        $ownerRequest = $request->validated();
        if ($request->has('image')) {
            $image_path = $this->update_image($request, $owner->image, 'image', '/village/owner');
            $ownerRequest['image'] = $image_path;
        }
        if (!empty($request->password)) {
            $ownerRequest['password'] = $request->password;
        }
        $owner->update($ownerRequest);

        return response()->json([
            'success' => 'You update data success'
        ]);
    }

    public function delete(Request $request, $id){
        $owner = $this->owners
        ->where('id', $id)
        ->first();
        if (empty($owner)) {
            return response()->json([
                'errors' => 'owner not found'
            ], 400);
        }

        $this->deleteImage($owner->image);
        $owner->delete();

        return response()->json([
            'success' => 'You delete data success'
        ]);
    }
}
