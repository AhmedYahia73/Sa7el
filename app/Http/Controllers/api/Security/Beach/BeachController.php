<?php

namespace App\Http\Controllers\api\Security\Beach;

use App\Http\Controllers\Controller;
use App\Models\Appartment;
use App\Models\AppartmentCode;
use App\Models\EntranceBeach;
use App\Models\User;
use App\Models\UserBeach;
use App\Models\VisitBeach;
use App\Models\VisitorCode;
use App\Models\InsideGate;
use App\Models\AppartmentTypeUmbrella;
use App\trait\TraitImage;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Zxing\QrReader;

class BeachController extends Controller
{
    public function __construct(private Appartment $appartment,
    private UserBeach $user_beach, private User $user,
    private AppartmentCode $appartment_code){}
    use TraitImage;

    public function read_qr(Request $request){
        $validator = Validator::make($request->all(), [
            'qr_code' => 'required|string',
            'beach_id' => 'required|exists:beaches,id',
            "umbrella" => "sometimes|integer",
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
        $umberllas = AppartmentTypeUmbrella::
        where("appartment_type_id", $appartment->appartment_type_id)
        ->where("village_id", $request->user()->village_id)
        ->first()?->umbrellas ?? 1;
         $user_umbrellas = $this->user_beach
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
         $old_user_beach = $this->user_beach
         ->where('user_id', $userid)
         ->where('beach_id', $beach_id)
         ->where('village_id', $request->user()->village_id) 
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
         if (!empty($old_user_beach)) {
            $old_time = $old_user_beach->updated_at->format('Y-d-m h:i A');
            $old_user_beach->updated_at = now();
            $old_user_beach->save();
         } else {
            $user_beach = $this->user_beach
            ->create([
                'user_id' => $userid,
                'beach_id' => $beach_id,
                'user_type' => $user_type,
                'village_id' => $request->user()->village_id,
                'umbrella' => $request->umbrella ?? 1,
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
            "umbrellas" => $my_umbrellas - ($request->umbrella ?? 0),
         ]);
    }

    public function entrance_beach_user(Request $request){
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|exists:users,id',
            'beach_id' => 'required|exists:beaches,id',
            'appartment_id' => 'required|exists:appartments,id',
            "umbrella" => "sometimes|integer",
        ]);
        if ($validator->fails()) { // if Validate Make Error Return Message Error
            return response()->json([
                'errors' => $validator->errors(),
            ],400);
        }
 
        $userid = $request->user_id;
        $beach_id = $request->beach_id;
        $appartment_id = $request->appartment_id;
    
         $appartment = $this->appartment
         ->where('id', $appartment_id) 
         ->first();
         if (empty($appartment) || $beach_id != $request->beach_id) {
            return response()->json([
                'errors' => 'Qr code is wrong'
            ], 400);
         }
        $umberllas = AppartmentTypeUmbrella::
        where("appartment_type_id", $appartment->appartment_type_id)
        ->where("village_id", $request->user()->village_id)
        ->first()?->umbrellas ?? 1;
         $user_umbrellas = $this->user_beach
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
         $old_user_beach = $this->user_beach
         ->where('user_id', $userid)
         ->where('beach_id', $beach_id)
         ->where('village_id', $request->user()->village_id) 
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
         if (!empty($old_user_beach)) {
            $old_time = $old_user_beach->updated_at->format('Y-d-m h:i A');
            $old_user_beach->updated_at = now();
            $old_user_beach->save();
         } else {
            $user_beach = $this->user_beach
            ->create([
                'user_id' => $userid,
                'beach_id' => $beach_id,
                'user_type' => $user_type,
                'village_id' => $request->user()->village_id,
                'umbrella' => $request->umbrella ?? 1,
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
            "umbrellas" => $my_umbrellas - ($request->umbrella ?? 0),
         ]);
    }

    public function entrance_inside_gate_user(Request $request){
        $validator = Validator::make($request->all(), [
            'inside_gate_id' => 'required|exists:inside_gates,id',
            'user_id' => 'required|exists:users,id',
            'appartment_id' => 'required|exists:appartments,id',
        ]);
        if ($validator->fails()) { // if Validate Make Error Return Message Error
            return response()->json([
                'errors' => $validator->errors(),
            ],400);
        }
  
        $is_visitor = AppartmentCode::
        where("user_id", $request->user_id)
        ->where("appartment_id", $request->appartment_id)
        ->orderByDesc("id")
        ->first() ? false : true;
        $last_visit_date = date("Y-m-d");
        $last_visit_time = date("h:i A");
        $userid = $request->user_id;
        if ($is_visitor) {
            $visitor_code = VisitorCode::
            where("user_id", $userid)
            ->where("appartment_id", $request->appartment_id)
            ->orderByDesc("id")
            ->first();
            if (!$visitor_code) {
                return response()->json([
                    'errors' => 'Qr code is expired'
                ], 400);
            }
            $tomorrow = Carbon::now()->addDay();
            $qrcode_time = $visitor_code->created_at;
            $qrcode_time = Carbon::parse($qrcode_time);
            if ($tomorrow < $qrcode_time) {
                return response()->json([
                    'errors' => 'Qr code is expired'
                ], 400);
            }
            $qr_code_code = $visitor_code->code;
            $code = $qr_code_code;
            $visitor_type = $visitor_code->visitor_type;
            if($visitor_type != 'guest'){
                return response()->json([
                    'errors' => 'Not Allowed'
                ], 400);
            } 
            $inside_gate = InsideGate::
            where('village_id', $request->user()->village_id)
            ->where("id", $request->inside_gate_id)
            ->first();
            if (!$visit_village->visitor) {
                return response()->json([
                    'errors' => 'Visitor has not perimission'
                ], 401);
            }
            $visit_village = VisitBeach::
            where('user_id', $userid)
            ->where('village_id', $request->user()->village_id)
            ->where("inside_gate_id", $request->inside_gate_id)
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
        else { 
            $appartment_id = $request->appartment_id;
            $last_visit = VisitBeach::
            where("user_id", $userid)
            ->where("village_id", $request->user()->village_id)
            ->first();
            $last_visit_date = $last_visit ? $last_visit->created_at->format("Y-m-d") ?? null : null;
            $last_visit_time = $last_visit ? $last_visit->created_at->format("h:i A") ?? null : null;
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
        
        $visit_inside_gates = VisitBeach::
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
            'date' => $last_visit_date,
            'time' => $last_visit_time,
            "visit_inside_gate_id" => $visit_inside_gates,
         ]); 
    }

    public function entrance_inside_gate_qr(Request $request){
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
        $last_visit_date = date("Y-m-d");
        $last_visit_time = date("h:i A");
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
            $inside_gate = InsideGate::
            where('village_id', $request->user()->village_id)
            ->where("id", $request->inside_gate_id)
            ->first();
            if (!$visit_village->visitor) {
                return response()->json([
                    'errors' => 'Visitor has not perimission'
                ], 401);
            }
            $visit_village = VisitBeach::
            where('user_id', $userid)
            ->where('village_id', $request->user()->village_id)
            ->where("inside_gate_id", $request->inside_gate_id)
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
            $last_visit = VisitBeach::
            where("user_id", $userid)
            ->where("village_id", $request->user()->village_id)
            ->first();
            $last_visit_date = $last_visit ? $last_visit->created_at->format("Y-m-d") ?? null : null;
            $last_visit_time = $last_visit ? $last_visit->created_at->format("h:i A") ?? null : null;
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
        
        $visit_inside_gates = VisitBeach::
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
            'date' => $last_visit_date,
            'time' => $last_visit_time,
            "visit_inside_gate_id" => $visit_inside_gates,
         ]); 
    }

    public function inside_gate_upload_id(Request $request){
        $validator = Validator::make($request->all(), [ 
            'visit_inside_gate_id' => 'required|exists:visit_villages,id',
            'image' => 'required',
        ]);
        if ($validator->fails()) { // if Validate Make Error Return Message Error
            return response()->json([
                'errors' => $validator->errors(),
            ],400);
        }

        $image_path = $this->storeBase64Image($request->image, 'images/visitors/id'); 
        $visit_village = VisitBeach::
        where('id', $request->visit_inside_gate_id)
        ->update([
            'image' => $image_path, 
        ]);

        return response()->json([
            'success' => 'You upload id success'
        ]);
    }
}
