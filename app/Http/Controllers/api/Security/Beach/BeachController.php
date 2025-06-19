<?php

namespace App\Http\Controllers\api\Security\Beach;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Zxing\QrReader;
use App\trait\TraitImage;
use Illuminate\Support\Facades\Validator;

use App\Models\Appartment;
use App\Models\UserBeach;
use App\Models\EntranceBeach;
use App\Models\User;

class BeachController extends Controller
{
    public function __construct(private Appartment $appartment,
    private UserBeach $user_beach, private User $user){}
    use TraitImage;

    public function read_qr(Request $request){
        $validator = Validator::make($request->all(), [
            'qr_code' => 'required|string',
            'beach_id' => 'required|exists:beaches,id', 
        ]);
        if ($validator->fails()) { // if Validate Make Error Return Message Error
            return response()->json([
                'errors' => $validator->errors(),
            ],400);
        }

        $base64 = $request->input('qr_code');
 
        if (strpos($base64, 'base64,') !== false) {
            $base64 = explode('base64,', $base64)[1];
        }

        // $imageData = base64_decode($base64);
        // $tempImagePath = storage_path('app/temp_qr.png');
        // file_put_contents($tempImagePath, $imageData);
        // $qrcode = new QrReader($tempImagePath);
        // $text = $qrcode->text();
        $text = $request->qr_code;
        $arr_text = explode('-', $text);
        $userid = 0;
        $beach_id = 0;
        if ($arr_text[2] == 'beach_id') {
            $userid = intval($arr_text[1]);
            $beach_id = intval($arr_text[3]);
            $appartment_id = intval($arr_text[5]);
        } 
        else{
            return response()->json([
                'errors' => 'Qr code is wrong'
            ], 400);
        }
         $appartment = $this->appartment
         ->where('id', $appartment_id) 
         ->first();
         if (empty($appartment) || $beach_id != $request->beach_id) {
            return response()->json([
                'errors' => 'Qr code is wrong'
            ], 400);
         }
        $appartment->type;
        $user = $this->user
        ->where('id', $userid)
        ->first();
         $user_beach_now = $this->user_beach
         ->where('user_id', $userid)
         ->where('beach_id', $beach_id)
         ->where('village_id', $request->user()->village_id)
         ->whereDate('created_at', date('Y-m-d'))
         ->first();
        $old_time = null;
         if (!empty($user_beach_now)) {
            $user_beach_now->updated_at = now();
            $$user_beach_now->save();
            $old_time = $user_beach_now->updated_at->format('H:i:s');
         } else {
            $user_beach = $this->user_beach
            ->create([
                'user_id' => $userid,
                'beach_id' => $beach_id,
                'village_id' => $request->user()->village_id,
            ]);
            EntranceBeach::create([
                'beach_id' => $beach_id,
                'user_id' => $userid,
                'time' => date('H:i:s'),
                'village_id' => $request->user()->village_id,
            ]);
         }
         
         return response()->json([
            'success' => 'Qr code is true',
            'appartment' => $appartment,
            'user' => $user,
            'time' => $old_time,
         ]);
    }
}
