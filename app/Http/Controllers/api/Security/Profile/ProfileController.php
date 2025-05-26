<?php

namespace App\Http\Controllers\api\Security\Profile;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\trait\image;

use App\Models\SecurityMan;

class ProfileController extends Controller
{
    public function __construct(private SecurityMan $security_man){}
    use image;

    public function profile(Request $request){
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
        $security = $this->security_man
        ->where('id', $request->user()->id)
        ->first();
        $security->name = $request->name ?? $security->name;
        $security->email = $request->email ?? $security->email;
        $security->phone = $request->phone ?? $security->phone;
        $security->password = bcrypt($request->password) ?? $security->password;
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
