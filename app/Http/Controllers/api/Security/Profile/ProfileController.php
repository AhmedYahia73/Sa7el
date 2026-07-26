<?php

namespace App\Http\Controllers\api\Security\Profile;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\trait\TraitImage;

use App\Models\SecurityMan;

class ProfileController extends Controller
{
    public function __construct(private SecurityMan $security_man){}
    use TraitImage;

    public function profile(Request $request){
        $validator = Validator::make($request->all(), [
            'locale' => 'in:ar,en',
        ]);
        if ($validator->fails()) { // if Validate Make Error Return Message Error
            return response()->json([
                'errors' => $validator->errors(),
            ],400);
        }
        $security = $request->user();

        return response()->json([
            'security' => [
                'name' => $security->name,
                'email' => $security->email,
                'phone' => $security->phone,
                'image' => $security->image_link,
            ]
        ]);
    }

    public function update_profile(Request $request){
        $validator = Validator::make($request->all(), [
            'locale' => 'in:ar,en',
        ]);
        if ($validator->fails()) { // if Validate Make Error Return Message Error
            return response()->json([
                'errors' => $validator->errors(),
            ],400);
        }
        $security = $this->security_man
        ->where('id', $request->user()->id)
        ->first();
        $security->name = $request->name ?? $security->name;
        $security->email = $request->email ?? $security->email;
        $security->phone = $request->phone ?? $security->phone;
        if (!empty($security->password)) {
            $security->password = bcrypt($request->password) ?? $security->password;
        }
        if ($request->has('image')) {
            $image_path =$this->storeBase64Image($request->image, '/images/user_profile');
            $security->image = $image_path;
        }
        $security->save();

        return response()->json([
            'security' => $security
        ]);
    }
}
