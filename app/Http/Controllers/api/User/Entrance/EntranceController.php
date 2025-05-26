<?php

namespace App\Http\Controllers\api\User\Entrance;

use App\Http\Controllers\Controller;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

use App\Models\User;

class EntranceController extends Controller
{
    public function __construct(private User $user){}

    public function view(Request $request){
        $validator = Validator::make($request->all(), [
            'appartment_id' => 'required|exists:appartments,id', 
        ]);
        if ($validator->fails()) { // if Validate Make Error Return Message Error
            return response()->json([
                'errors' => $validator->errors(),
            ],400);
        }
        $user = $request->user();
        $data = $user->id . '>appartment>' . $item->appartment_id;
        $qrCode = base64_encode(
            QrCode::format('png')->size(300)->generate($data)
        );
        $qrCodeImage = 'data:image/png;base64,' . $qrCode;

        return response()->json([
            'id' => $user->id,
            'name' => $user->name,
            'image_link' => $user->image_link,
            'qr_code_link' => $qrCodeImage,
        ]);
    }
}
