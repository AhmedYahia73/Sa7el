<?php

namespace App\Http\Controllers\api\Security\Gate;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Zxing\QrReader;
use App\trait\image;
use Illuminate\Support\Facades\Validator;

use App\Models\AppartmentCode;
use App\Models\VisitVillage;
use App\Models\User;

class GateController extends Controller
{
    public function __construct(private AppartmentCode $appartment,
    private VisitVillage $visit_village, private User $user){}
    use image;

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

        $imageData = base64_decode($base64);
        $tempImagePath = storage_path('app/temp_qr.png');
        file_put_contents($tempImagePath, $imageData);

        $qrcode = new QrReader($tempImagePath);
        $text = $qrcode->text();
        $arr_text = explode('-', $text);
        $userid = 0;
        $visitor = 0;
        $visitor_type = null;
        if ($arr_text[0] == 'visitor_id') {
            $userid = intval($arr_text[1]);
            $visitor_type = intval($arr_text[5]);
            $visitor = 1;
        } 
        elseif(intval($arr_text[0])) {
            $userid = intval($arr_text[0]); 
        }
        else{
            return response()->json([
                'errors' => 'Qr code is wrong'
            ], 400);
        }
         $appartment = $this->appartment
         ->where('user_id', $userid)
         ->where('type', 'owner')
         ->where('village_id', $request->user()->village_id)
         ->orWhere('user_id', $userid)
         ->where('village_id', $request->user()->village_id)
         ->where('type', 'renter')
         ->where('from', '<=', date('Y-m-d'))
         ->where('to', '>=', date('Y-m-d'))
         ->first();
         if (empty($appartment)) {
            return response()->json([
                'errors' => 'Qr code is wrong'
            ], 400);
         }
         if ($visitor) {
            // $image_path = null;
            // if ($request->has('image')) {
            //     $image_path = $this->upload($request, 'image', 'images/visitors/id');
            // }
            $visit_village = $this->visit_village
            ->create([
                'user_id' => $userid,
                'village_id' => $request->user()->village_id,
                'gate_id' => $request->gate_id,
                'type' => 'visitor',
            ]);
         }
         else{ 
            $visit_village = $this->visit_village
            ->create([
                'user_id' => $userid,
                'village_id' => $request->user()->village_id,
                'gate_id' => $request->gate_id,
                'type' => 'owner'
            ]);
         }
         $appartment = $appartment->appartment;
        $appartment->type;
        $user = $this->user
        ->where('id', $userid)
        ->first();

         return response()->json([
            'success' => 'Qr code is true',
            'appartment' => $appartment,
            'user' => $user,
            'visit_village_id' => $visit_village,
            'visitor_type' => $visitor_type
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
        
        // $image_path = null;
        // if ($request->has('image')) {
        //     $image_path = $this->upload($request, 'image', 'images/visitors/id');
        // }
        $visit_village = $this->visit_village
        ->create([
            'user_id' => $userid,
            'village_id' => $request->user()->village_id,
            'gate_id' => $request->gate_id,
            'type' => 'visitor',
        ]);
    }
}
