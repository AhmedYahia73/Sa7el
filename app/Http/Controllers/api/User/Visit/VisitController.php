<?php

namespace App\Http\Controllers\api\User\Visit;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

use App\Models\VisitorCode;

class VisitController extends Controller
{
    public function __construct(private VisitorCode $visitor_code){}
    
    public function create_qr_code(Request $request){
        $data = $request->user()->id . time();
        $qrCode = QrCode::format('png')->size(300)->generate($data);
        $fileName = 'user/visit/qr/' . $data . 'png';
        Storage::put($fileName, $qrCode);
        $this->visitor_code
        ->create([
            'user_id' => $request->user()->id,
            'qr_code' => $fileName
        ]);

        return reponse()->json([
            'success' => url('storage/' . $fileName)
        ]);
    }

    public function create_code(Request $request){    
        do {
            $code = mt_rand(1000000, 9999999); // Always 7 digits
        } while ($this->visitor_code::where('code', $code)->exists()); 
        
        $this->visitor_code
        ->create([
            'user_id' => $request->user()->id,
            'code' => $code
        ]);

        return reponse()->json([
            'success' => $code
        ]);
    }
}
