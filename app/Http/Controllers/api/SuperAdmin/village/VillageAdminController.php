<?php

namespace App\Http\Controllers\api\SuperAdmin\village;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\SuperAdmin\VillageRequest;
use Illuminate\Support\Facades\Validator;

use App\Models\Village;
use App\Models\User;

class VillageAdminController extends Controller
{
    public function __construct(private Village $village,
    private User $admin){}

    public function view($id){
        $village = $this->village
        ->where('id', $id)
        ->first();
        $admins = $village?->admin ?? [];

        return response()->json([
            'village' => $village,
            'admins' => $admins,
        ]);
    }

    public function admin($id){
        $admin = $this->admin
        ->where('id', $id)
        ->first();

        return response()->json([
            'admin' => $admin,
        ]);
    }


    public function status(Request $request, $id){
        $this->admin
        ->where('id', $id)
        ->update([
            'status' => $request->status
        ]);

        return response()->json([
            'success' => $request->status ? 'active' : 'banned',
        ]);
    }
    
    public function create(){
        $this->admin
        ->create();

        return response()->json([
            'success' => 'You add data success',
        ]);
    }
    
    public function modify($id){
        $villages = $this->village
        ->get();

        return response()->json([
            'villages' => $villages,
        ]);
    }
    
    public function delete($id){
        $villages = $this->village
        ->get();

        return response()->json([
            'villages' => $villages,
        ]);
    }
}
