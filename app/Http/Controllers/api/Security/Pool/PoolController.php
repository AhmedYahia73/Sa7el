<?php

namespace App\Http\Controllers\api\Security\Pool;

use App\Http\Controllers\Controller;
use App\Models\Appartment;
use App\Models\AppartmentCode;
use App\Models\AppartmentTypeUmbrella;
use App\Models\EntrancePool;
use App\Models\User;
use App\Models\UserPool;
use App\Models\VisitBeach;
use App\trait\TraitImage;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Zxing\QrReader;

class PoolController extends Controller
{
    public function __construct(private Appartment $appartment,
    private UserPool $user_pool, private User $user,
    private AppartmentCode $appartment_code){}
    use TraitImage;

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
        ->select("id", "unit", "appartment_type_id")
        ->with("type:id,name")
         ->where('id', $appartment_id) 
         ->first();
         if (empty($appartment) || $pool_id != $request->pool_id) {
            return response()->json([
                'errors' => 'Qr code is wrong'
            ], 400);
         }
        $type = [
            "id" => $appartment->id,
            "name" => $appartment->name,
        ];
        unset($appartment->type);
        $umberllas = AppartmentTypeUmbrella::
        where("appartment_type_id", $appartment->appartment_type_id)
        ->where("village_id", $request->user()->village_id)
        ->first()?->umbrellas ?? 1;
         $user_umbrellas = $this->user_pool
         ->where('user_id', $userid) 
         ->where('village_id', $request->user()->village_id)
         ->whereDate('created_at', date('Y-m-d'))
         ->sum("umbrella") ?? 0;
        $my_umbrellas = $umberllas - $user_umbrellas;
        
        $user = $this->user
        ->select("id", "name", "image")
        ->where('id', $userid)
        ->first();
        $old_date_user_pool = $this->user_pool
        ->where("appartment_id", $appartment_id)
        ->where('village_id', $request->user()->village_id)
        ->with("user:id,name,image")
        ->orderByDesc('id') 
        ->first();
         
        $user_type = $this->appartment_code
         ->where('appartment_id', $appartment_id)
         ->where('user_id', $old_date_user_pool?->user?->id)
         ->whereNotNull('user_id')
         ->orderByDesc('id')
         ->first()?->type;
        if (!empty($old_date_user_pool)) {
            $old_time = $old_date_user_pool->updated_at->format('Y-d-m h:i A');
        } else {
            $old_time = date('Y-m-d h:i A');
        }
        if($my_umbrellas < 1){  
            return response()->json([
                'success' => 'User has no umbrellas available',
                'appartment' => $appartment,
                'appartment_type' => $type,
                'user' => $user,
                'last_user' => $old_date_user_pool?->user,
                'time' => $old_time,
                'user_type' => $user_type,
                "umbrellas" => 0,
                "open_status" => false,
            ]);
        }
         if (empty($user_type)) {
            return response()->json([
                'errors' => 'Appartment is wrong'
            ], 400);
         }    
        $user_pool = $this->user_pool
        ->create([
            'user_id' => $userid,
            'pool_id' => $pool_id,
            'village_id' => $request->user()->village_id,
            'user_type' => $user_type,
            'umbrella' => 1,
            "appartment_id" => $appartment_id,
        ]);
        EntrancePool::create([
            'pool_id' => $pool_id,
            'user_id' => $userid,
            'time' => date('H:i:s'),
            'village_id' => $request->user()->village_id,
        ]); 

         return response()->json([
            'success' => 'Qr code is true',
            'appartment' => $appartment,
            'appartment_type' => $type,
            'user_type' => $user_type,
            'user' => $user,
            'last_user' => $old_date_user_pool?->user,
            'time' => $old_time,
            "umbrellas" => $my_umbrellas - 1,
            "open_status" => true,
         ]);
    } 

    public function entrance_user(Request $request){
        $validator = Validator::make($request->all(), [
            'pool_id' => 'required|exists:pools,id',
            'user_id' => 'required|exists:users,id',
            'appartment_id' => 'required|exists:appartments,id', 
        ]);
        if ($validator->fails()) { // if Validate Make Error Return Message Error
            return response()->json([
                'errors' => $validator->errors(),
            ],400);
        }
    
        $pool_id = $request->pool_id; 
        $userid = $request->user_id;
        $appartment_id = $request->appartment_id;
       
         $appartment = $this->appartment
        ->select("id", "unit", "appartment_type_id")
        ->with("type:id,name")
         ->where('id', $appartment_id) 
         ->first();
         if (empty($appartment) || $pool_id != $request->pool_id) {
            return response()->json([
                'errors' => 'Qr code is wrong'
            ], 400);
         }
        $type = [
            "id" => $appartment->id,
            "name" => $appartment->name,
        ];
        unset($appartment->type);
        $umberllas = AppartmentTypeUmbrella::
        where("appartment_type_id", $appartment->appartment_type_id)
        ->where("village_id", $request->user()->village_id)
        ->first()?->umbrellas ?? 1;
         $user_umbrellas = $this->user_pool
         ->where('user_id', $userid) 
         ->where('village_id', $request->user()->village_id)
         ->whereDate('created_at', date('Y-m-d'))
         ->sum("umbrella") ?? 0;
        $my_umbrellas = $umberllas - $user_umbrellas;
        
        $user = $this->user
        ->select("id", "name", "image")
        ->where('id', $userid)
        ->first();
        $old_date_user_pool = $this->user_pool
        ->where("appartment_id", $appartment_id)
        ->where('village_id', $request->user()->village_id)
        ->with("user:id,name,image")
        ->orderByDesc('id') 
        ->first();
         
        $user_type = $this->appartment_code
         ->where('appartment_id', $appartment_id)
         ->where('user_id', $old_date_user_pool?->user?->id)
         ->whereNotNull('user_id')
         ->orderByDesc('id')
         ->first()?->type;
        if (!empty($old_date_user_pool)) {
            $old_time = $old_date_user_pool->updated_at->format('Y-d-m h:i A');
        } else {
            $old_time = date('Y-m-d h:i A');
        }
        if($my_umbrellas < 1){
            return response()->json([
                'success' => 'User has no umbrellas available',
                'appartment' => $appartment,
                'appartment_type' => $type,
                'user' => $user,
                'last_user' => $old_date_user_pool?->user,
                'time' => $old_time,
                'user_type' => $user_type,
                "umbrellas" => 0,
                "open_status" => false,
            ]);
        }
         if (empty($user_type)) {
            return response()->json([
                'errors' => 'Appartment is wrong'
            ], 400);
         }
         if (empty($user_type)) {
            return response()->json([
                'errors' => 'Appartment is wrong'
            ], 400);
         } 
        $user_pool = $this->user_pool
        ->create([
            'user_id' => $userid,
            'pool_id' => $pool_id,
            'village_id' => $request->user()->village_id,
            'user_type' => $user_type,
            'umbrella' => $request->umbrella ?? 1,
        ]);
        EntrancePool::create([
            'pool_id' => $pool_id,
            'user_id' => $userid,
            'time' => date('H:i:s'),
            'village_id' => $request->user()->village_id,
        ]); 

         return response()->json([
            'success' => 'Qr code is true',
            'appartment' => $appartment,
            'appartment_type' => $type,
            'user_type' => $user_type,
            'user' => $user,
            'last_user' => $old_date_user_pool?->user,
            'time' => $old_time,
            "umbrellas" => $my_umbrellas - 1,
            "open_status" => true,
         ]);
    } 
}
