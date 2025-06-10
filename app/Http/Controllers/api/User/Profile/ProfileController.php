<?php

namespace App\Http\Controllers\api\User\Profile;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\trait\TraitImage;

use App\Models\User;

class ProfileController extends Controller
{
    public function __construct(private User $user_man){}
    use TraitImage;

    public function profile(Request $request){
        $user = $request->user();

        return response()->json([
            'user' => [
                'name' => $user->name,
                'email' => $user->email,
                'phone' => $user->phone,
                'image' => $user->image_link,
            ]
        ]);
    }

    public function update_profile(Request $request){
        $user = $this->user_man
        ->where('id', $request->user()->id)
        ->first();
        $user->name = $request->name ?? $user->name;
        $user->email = $request->email ?? $user->email;
        $user->phone = $request->phone ?? $user->phone;
        if (!empty($request->password)) {
            $user->password = bcrypt($request->password) ?? $user->password;
        }
        if ($request->has('image')) {
            $image_path =$this->storeBase64Image($request->image, '/images/user_profile');
            $user->image = $image_path;
        }
        $user->save();

        return response()->json([
            'user' => $user
        ]);
    }
}
