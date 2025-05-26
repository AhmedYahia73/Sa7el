<?php

namespace App\Http\Controllers\api\Security\Pool;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Zxing\QrReader;
use App\trait\image;
use Illuminate\Support\Facades\Validator;

use App\Models\AppartmentCode;
use App\Models\UserPool;
use App\Models\User;

class PoolController extends Controller
{
    public function __construct(private AppartmentCode $appartment,
    private UserPool $user_pool, private User $user){}
    use image;

    public function read_qr(Request $request){
        $validator = Validator::make($request->all(), [
            'qr_code' => 'required|string',
            'pool_id' => 'required|exists:pools,id', 
        ]);
        if ($validator->fails()) { // if Validate Make Error Return Message Error
            return response()->json([
                'errors' => $validator->errors(),
            ],400);
        }

        // $base64 = $request->input('qr_code');
 
        // if (strpos($base64, 'base64,') !== false) {
        //     $base64 = explode('base64,', $base64)[1];
        // }

        // $imageData = base64_decode($base64);
        // $tempImagePath = storage_path('app/temp_qr.png');
        // file_put_contents($tempImagePath, $imageData);
        // $qrcode = new QrReader($tempImagePath);
        // $text = $qrcode->text();
        $text = $request->qr_code;
        $arr_text = explode('-', $text);
        $userid = 0;
        $pool_id = 0;
        $appartment_id = 0;
        if ($arr_text[2] == 'pool_id') {
            $userid = intval($arr_text[1]);
            $pool_id = intval($arr_text[3]);
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
         if (empty($appartment) || $pool_id != $request->pool_id) {
            return response()->json([
                'errors' => 'Qr code is wrong'
            ], 400);
         }
         $user_pool = $this->user_pool
         ->create([
            'user_id' => $userid,
            'pool_id' => $pool_id,
            'village_id' => $request->user()->village_id,
         ]);
        $appartment = $appartment->appartment;
        $appartment->type;
        $user = $this->user
        ->where('id', $userid)
        ->first();

         return response()->json([
            'success' => 'Qr code is true',
            'appartment' => $appartment,
            'user' => $user,
         ]);
    }
}
