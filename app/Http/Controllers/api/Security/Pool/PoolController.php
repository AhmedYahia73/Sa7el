<?php

namespace App\Http\Controllers\api\Security\Pool;

use App\Http\Controllers\Controller;
use App\Models\Appartment;
use App\Models\AppartmentCode;
use App\Models\AppartmentTypeUmbrella;
use App\Models\EntrancePool;
use App\Models\User;
use App\Models\UserPool;
use App\Models\VisitPool;
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
            "umbrella" => "sometimes|integer", 
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
        if($my_umbrellas < $request->umbrella){
            return response()->json([
                'errors' => 'عدد الشمسيات المتاحة ' . $my_umbrellas
            ], 400);
        }
         $user_pool_now = $this->user_pool
         ->where('user_id', $userid)
         ->where('pool_id', $pool_id)
         ->where('village_id', $request->user()->village_id)
         ->whereDate('created_at', date('Y-m-d'))
         ->first();
        $appartment->type;
        $user = $this->user
        ->where('id', $userid)
        ->first();
        $old_time = null;
        $user_type = $this->appartment_code
         ->where('appartment_id', $appartment_id)
         ->where('user_id', $userid)
         ->orderByDesc('id')
         ->first()?->type;
         if (empty($user_type)) {
            return response()->json([
                'errors' => 'Appartment is wrong'
            ], 400);
         }
         if (!empty($user_pool_now)) {
            $old_time = $user_pool_now->updated_at->format('Y-m-d h:i A');
            $user_pool_now->updated_at = now();
            $user_pool_now->save();
         }
         else{
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
         }

         return response()->json([
            'success' => 'Qr code is true',
            'appartment' => $appartment,
            'user' => $user,
            'time' => $old_time,
         ]);
    }
    
    public function entrance_pool_qr(Request $request){
        $validator = Validator::make($request->all(), [
            'qr_code' => 'required|string',
            'inside_gate_id' => 'required|exists:inside_gates,id',
        ]);
        if ($validator->fails()) { // if Validate Make Error Return Message Error
            return response()->json([
                'errors' => $validator->errors(),
            ],400);
        }
  
        $text = $request->qr_code;
        $arr_text = explode('>', $text);
        $userid = 0; 
        $appartment_id = 0;
        $type = null;
        $code = null;
        $user_type = null;
        $visitor_type = null;
        if ($arr_text[0] == 'visitor_id') {
            $userid = intval($arr_text[1]); 
            $tomorrow = Carbon::now()->addDay();
            $qrcode_time = $arr_text[7];
            $qrcode_time = Carbon::parse($qrcode_time);
            if ($tomorrow < $qrcode_time) {
                return response()->json([
                    'errors' => 'Qr code is expired'
                ], 400);
            }
            $qr_code_code = $arr_text[9];
            $code = $qr_code_code;
            $visitor_type = $arr_text[5];
            if($visitor_type != 'guest'){
                return response()->json([
                    'errors' => 'Not Allowed'
                ], 400);
            } 
            
            $visit_village = VisitPool::
            where('user_id', $userid)
            ->where('village_id', $request->user()->village_id)
            ->where('code', $qr_code_code)
            ->first();
            if (!empty($visit_village)) {
                return response()->json([
                    'errors' => 'Qr code is expired...'
                ], 400);
            }
            $appartment_id = $arr_text[11];
            $appartment = Appartment::
            where('id', $appartment_id)
            ->first();
            if (empty($appartment)) {
                return response()->json([
                    'errors' => 'Appartment is wrong'
                ], 400);
            }
            $type = 'visitor';
        } 
        elseif(intval($arr_text[0])) {
            $userid = intval($arr_text[0]); 
            $appartment_id = $arr_text[2];
        }
        else{
            return response()->json([
                'errors' => 'Qr code is wrong'
            ], 400);
        }
        
         $appartment = Appartment::
         where('id', $appartment_id)
         ->first();
         if (empty($appartment)) {
            return response()->json([
                'errors' => 'Qr code is wrong'
            ], 400);
         }
        $user_type = AppartmentCode::
         where('appartment_id', $appartment_id)
         ->where('user_id', $userid)
         ->orderByDesc('id')
         ->first()?->type;
         if (empty($user_type)) {
            return response()->json([
                'errors' => 'Appartment is wrong'
            ], 400);
         }
        
        $last_visit = VisitPool::
        where("user_id", $userid)
        ->where("village_id", $request->user()->village_id)
        ->first();
        VisitPool::
        create([
            'user_id' => $userid,
            'village_id' => $request->user()->village_id,
            'inside_gate_id' => $request->inside_gate_id,
            'appartment_id' => $appartment_id,
            'type' => $type,
            'visitor_type' => $visitor_type,
            'code' => $code,
            'user_type' => $user_type,
        ]);
        $user = User::
        where('id', $userid)
        ->first();

         return response()->json([
            'success' => 'Qr code is true',
            'appartment' => $appartment,
            'user' => $user, 
            'visitor_type' => $visitor_type,
            'user_type' => $user_type,
            "is_visitor" => $type == 'visitor' ? true : false,
            'date' => $last_visit ? $last_visit->created_at->format("Y-m-d") ?? null : null,
            'time' => $last_visit ? $last_visit->created_at->format("h:i A") ?? null : null,
         ]);
        
    }
}
