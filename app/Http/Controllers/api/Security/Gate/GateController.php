<?php

namespace App\Http\Controllers\api\Security\Gate;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Zxing\QrReader;
use App\trait\TraitImage;
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;

use App\Models\EntranceGate;
use App\Models\AppartmentCode;
use App\Models\Appartment;
use App\Models\VisitVillage;
use App\Models\User;

class GateController extends Controller
{
    public function __construct(private AppartmentCode $appartment,
    private Appartment $appartment_data, private VisitVillage $visit_village, private User $user){}
    use TraitImage;

    public function read_qr(Request $request){
        $validator = Validator::make($request->all(), [
            'qr_code' => 'required|string',
            'gate_id' => 'required|exists:gates,id', 
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
        $arr_text = explode('>', $text);
        $userid = 0;
        $visitor = 0;
        $visitor_type = null;
        $code = null;
        $appartment_id = null;
        if ($arr_text[0] == 'visitor_id') {
            $userid = intval($arr_text[1]);
            $visitor_type = $arr_text[5];
            $visitor = 1;
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
            
            $visit_village = $this->visit_village
            ->where('user_id', $userid)
            ->where('village_id', $request->user()->village_id)
            ->where('code', $qr_code_code)
            ->first();
            if (!empty($visit_village)) {
                return response()->json([
                    'errors' => 'Qr code is expired...'
                ], 400);
            }
            $appartment_id = $arr_text[11];
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
         $appartment = $this->appartment_data
         ->where('id', $appartment_id)
         ->first();
         if (empty($appartment)) {
            return response()->json([
                'errors' => 'Qr code is wrong'
            ], 400);
         }
        $user_type = $this->appartment
         ->where('appartment_id', $appartment_id)
         ->where('user_id', $userid)
         ->first()?->type;
         if ($visitor) { 
            $visit_village = $this->visit_village
            ->create([
                'user_id' => $userid,
                'village_id' => $request->user()->village_id,
                'gate_id' => $request->gate_id,
                'type' => 'visitor',
                'visitor_type' => $visitor_type,
                'code' => $code,
                'appartment_id' => $appartment_id,
                'user_type' => $user_type,
            ]);
         }
         else{ 
            $visit_village = $this->visit_village
            ->create([
                'user_id' => $userid,
                'village_id' => $request->user()->village_id,
                'gate_id' => $request->gate_id,
                'type' => $user_type,
                'appartment_id' => $appartment_id,
                'user_type' => $user_type,
            ]);
         }
         EntranceGate::create([
            'gate_id' => $request->gate_id,
            'user_id' => $userid,
            'time' => date('H:i:s'),
            'village_id' => $request->user()->village_id,
         ]);
        $appartment->type;
        $user = $this->user
        ->where('id', $userid)
        ->first();

         return response()->json([
            'success' => 'Qr code is true',
            'appartment' => $appartment,
            'user' => $user,
            'visit_village_id' => $visit_village,
            'visitor_type' => $visitor_type,
            'date' => date('Y-m-d'),
            'time' => date('h:i A'),
         ]);
    }

    public function upload_id(Request $request){
        $validator = Validator::make($request->all(), [ 
            'visit_village_id' => 'required|exists:visit_villages,id',
            'image' => 'required',
        ]);
        if ($validator->fails()) { // if Validate Make Error Return Message Error
            return response()->json([
                'errors' => $validator->errors(),
            ],400);
        }

        $image_path = $this->storeBase64Image($request->image, 'images/visitors/id'); 
        $visit_village = $this->visit_village
        ->where('id', $request->visit_village_id)
        ->update([
            'image' => $image_path, 
        ]);

        return response()->json([
            'success' => 'You upload id success'
        ]);
    }
}
