<?php

namespace App\Http\Controllers\api\User\Entrance;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\User;

class EntranceController extends Controller
{
    public function __construct(private User $user){}

    public function view(Request $request){
        $user = $request->user();

        return response()->json([
            'id' => $user->id,
            'name' => $user->name,
            'image_link' => $user->image_link,
            'qr_code_link' => $user->qr_code_link,
        ]);
    }
}
