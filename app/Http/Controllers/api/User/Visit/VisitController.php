<?php

namespace App\Http\Controllers\api\User\Visit;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

use App\Models\VisitorCode;
use App\Models\VisitVillage;

class VisitController extends Controller
{
    public function __construct(private VisitorCode $visitor_code,
    private VisitVillage $visit_village){}
    
    public function create_qr_code(Request $request){
        $validator = Validator::make($request->all(), [
            'village_id' => 'required|exists:villages,id',
            'visitor_type' => 'required|in:guest,worker,delivery',
            'appartment_id' => 'required|exists:appartments,id',
        ]);
        if ($validator->fails()) { // if Validate Make Error Return Message Error
            $firstError = $validator->errors()->first();
            return response()->json([
                'errors' => $firstError,
            ],400);
        }
        $data = 'visitor_id>' . $request->user()->id . '>village_id>' . $request->village_id . 
        '>visitor_type>' . $request->visitor_type . '>time>' . now() . '>rand>' . rand(1, 100000)
        . '>appartment_id>' . $request->appartment_id;
        $qrCode = QrCode::format('png')->size(300)->generate($data);
        $fileName = 'user/visit/qr/' . $data . '.png';
        Storage::disk('public')->put($fileName, $qrCode); // Save the image
        $this->visitor_code
        ->create([
            'user_id' => $request->user()->id,
            'qr_code' => $fileName,
            'village_id' => $request->village_id,
            'appartment_id' => $request->appartment_id,
            'visitor_type' => $request->visitor_type
        ]);
        // $request->visitor_type

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
                'errors' => $firstError,
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

    public function visitor_qr(Request $request){
        $validator = Validator::make($request->all(), [
            'village_id' => 'required|exists:villages,id',
            'appartment_id' => 'required|exists:appartments,id',
        ]);
        if ($validator->fails()) { // if Validate Make Error Return Message Error
            $firstError = $validator->errors()->first();
            return response()->json([
                'errors' => $firstError,
            ],400);
        }

        $visitor_code = $this->visitor_code
        ->select('qr_code', 'visitor_type')
        ->where('village_id', $request->village_id)
        ->where('appartment_id', $request->appartment_id)
        ->whereDate('created_at', date('Y-m-d'))
        ->get();

        return response()->json([
            'visitors_count' => $visitors_count
        ]);
    }
}
