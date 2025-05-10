<?php

namespace App\Http\Controllers\api\User\Visit;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

use App\Models\VisitorCode;

class VisitController extends Controller
{
    public function __construct(private VisitorCode $visitor_code){}
    
    public function create_qr_code(Request $request){
        $validator = Validator::make($request->all(), [
            'village_id' => 'required|exists:villages,id',
        ]);
        if ($validator->fails()) { // if Validate Make Error Return Message Error
            $firstError = $validator->errors()->first();
            return response()->json([
                'error' => $firstError,
            ],400);
        }
        $data = $request->user()->id . '-' . $request->village_id . '-' . time();
        $qrCode = QrCode::format('png')->size(300)->generate($data);
        $fileName = 'user/visit/qr/' . $data . '.png';
        Storage::disk('public')->put($fileName, $qrCode); // Save the image
        $this->visitor_code
        ->create([
            'user_id' => $request->user()->id,
            'qr_code' => $fileName
        ]);

        return response()->json([
            'success' => url('storage/' . $fileName)
        ]);
    }

    public function create_code(Request $request){
        $validator = Validator::make($request->all(), [
            'village_id' => 'required|exists:villages,id',
        ]);
        if ($validator->fails()) { // if Validate Make Error Return Message Error
            $firstError = $validator->errors()->first();
            return response()->json([
                'error' => $firstError,
            ],400);
        }   
        do {
            $code = mt_rand(1000000, 9999999); // Always 7 digits
        } while ($this->visitor_code::where('code', $code)->exists()); 
        
        $this->visitor_code
        ->create([
            'user_id' => $request->user()->id,
            'code' => $code,
            'village_id' => $request->village_id,
        ]);

        return response()->json([
            'success' => $code
        ]);
    }
}
