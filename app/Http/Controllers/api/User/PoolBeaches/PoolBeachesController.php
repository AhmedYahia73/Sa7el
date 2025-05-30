<?php

namespace App\Http\Controllers\api\User\PoolBeaches;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

use App\Models\Pools;
use App\Models\Beach;

class PoolBeachesController extends Controller
{
    public function __construct(private Pools $pools,
    private Beach $beaches){}

    public function beaches(Request $request){
        $validator = Validator::make($request->all(), [
            'local' => 'required|in:en,ar',
            'village_id' => 'required|exists:villages,id',
            'appartment_id' => 'required|exists:appartments,id',
        ]);
        if ($validator->fails()) { // if Validate Make Error Return Message Error
            $firstError = $validator->errors()->first();
            return response()->json([
                'errors' => $firstError,
            ],400);
        } 
        $beaches = $this->beaches
        ->where('village_id', $request->village_id)
        ->where('status', 1)
        ->get()
        ->map(function($item) use($request){
            $data = 'user_id-' . $request->user()->id . '-beach_id-' . $item->id
            . '-appartment_id-' . $request->appartment_id;
            $qrCode = base64_encode(
                QrCode::format('png')->size(300)->generate($data)
            );
            $qrCodeImage = 'data:image/png;base64,' . $qrCode;
            return [
                'name' => $request->local == 'en' ? $item->name : $item->ar_name ?? $item->name,
                'from' => $item->from,
                'to' => $item->to,
                'qr_code' =>$qrCodeImage,
            ];
        }); 

        return response()->json([
            'beaches' => $beaches
        ]);
    }

    public function pools(Request $request){
        $validator = Validator::make($request->all(), [
            'local' => 'required|in:en,ar',
            'village_id' => 'required|exists:villages,id',
            'appartment_id' => 'required|exists:appartments,id', 
        ]);
        if ($validator->fails()) { // if Validate Make Error Return Message Error
            $firstError = $validator->errors()->first();
            return response()->json([
                'errors' => $firstError,
            ],400);
        }
        $pools = $this->pools
        ->where('village_id', $request->village_id)
        ->where('status', 1)
        ->get()
        ->map(function($item) use($request){
            $data = 'user_id-' . $request->user()->id . '-pool_id-' . $item->id
            . '-appartment_id-' . $request->appartment_id;
            $qrCode = base64_encode(
                QrCode::format('png')->size(300)->generate($data)
            );
            $qrCodeImage = 'data:image/png;base64,' . $qrCode;
            return [
                'name' => $request->local == 'en' ? $item->name : $item->ar_name ?? $item->name,
                'from' => $item->from,
                'to' => $item->to,
                'qr_code' => $qrCodeImage,
            ];
        });

        return response()->json([
            'pools' => $pools
        ]);
    }
}
