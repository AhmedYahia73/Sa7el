<?php

namespace App\Http\Controllers\api\ServiceProvider\WorkHours;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

use App\Models\Provider;

class WorkHoursController extends Controller
{
    public function __construct(private Provider $provider){}

    public function view(Request $request){
        $provider = $this->provider
        ->where('id', $request->user()->provider_id)
        ->first();

        return response()->json([
            'open_from' => $provider?->open_from,
            'open_to' => $provider?->open_to,
        ]);
    }

    public function update(Request $request){
        $validator = Validator::make($request->all(), [
            'open_from' => ['regex:/^([01]\d|2[0-3]):[0-5]\d:[0-5]\d$/', 'required'], 
            'open_to' => ['regex:/^([01]\d|2[0-3]):[0-5]\d:[0-5]\d$/', 'required'],
        ]);
        if ($validator->fails()) { // if Validate Make Error Return Message Error
            return response()->json([
                'errors' => $validator->errors(),
            ],400);
        }

        $provider = $this->provider
        ->where('id', $request->user()->provider_id)
        ->update([
            'open_from' => $request->open_from,
            'open_to' => $request->open_to,
        ]);

        return response()->json([
            'success' => 'You update data success'
        ]);
    }
}
