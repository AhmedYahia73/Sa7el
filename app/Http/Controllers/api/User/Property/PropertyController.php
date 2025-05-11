<?php

namespace App\Http\Controllers\api\User\Property;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

use App\Models\Appartment;
use App\Models\AppartmentCode;
use App\Models\Zone;

class PropertyController extends Controller
{
    public function __construct(private Appartment $appartment,
    private AppartmentCode $appartment_code, private Zone $zones){}

    public function my_property(Request $request){
        $validator = Validator::make($request->all(), [
            'local' => 'required|in:en,ar',
            'village_id' => 'required|exists:villages,id',
        ]);
        if ($validator->fails()) { // if Validate Make Error Return Message Error
            $firstError = $validator->errors()->first();
            return response()->json([
                'errors' => $firstError,
            ],400);
        }
        $zones = $this->zones
        ->where('status', 1)
        ->get();
        $appartment = $this->appartment_code
        ->with(['appartment' => function($query){
            $query->with('type', 'village');
        }])
        ->where('type', 'owner')
        ->orWhere('type', 'renter')
        ->where('from', '<=', date('Y-m-d'))
        ->where('to', '>=', date('Y-m-d'))
        ->get()
        ->pluck('appartment')
        ->map(function($item) use($request){
            return [
                'id' => $item->id,
                'unit' => $item->unit,
                'image' => $item->image_link,
                'village' => $item->village->name,
                'number_floors' => $item->number_floors,
                'type' => $request->local == 'en' ? $item?->type?->name : 
                $item?->type?->ar_name ?? $item?->type?->name,
                'zone' => $request->local == 'en' ? $item?->zone?->name
                : $item?->zone?->ar_name ?? $item?->zone?->name,
                'zone_id' => $item->zone_id,
            ];
        });

        return response()->json([
            'appartment' => $appartment,
            'zones' => $zones
        ]);
    }

    public function add_property(Request $request){
        $validator = Validator::make($request->all(), [
            'village_id' => 'required|exists:villages,id',
            'code' => 'required',
        ]);
        if ($validator->fails()) { // if Validate Make Error Return Message Error
            $firstError = $validator->errors()->first();
            return response()->json([
                'errors' => $firstError,
            ],400);
        }

        $appartment_code = $this->appartment_code
        ->where('type', 'owner')
        ->where('village_id', $request->village_id)
        ->where('code', $request->code)
        ->orWhere('type', 'renter')
        ->where('from', '<=', date('Y-m-d'))
        ->where('to', '>=', date('Y-m-d'))
        ->where('village_id', $request->village_id)
        ->where('code', $request->code)
        ->first();
        
        if (empty($appartment_code)) {
            return response()->json([
                'errors' => 'appartment is not found'
            ]);
        }

        $appartment_code->user_id = $request->user()->id;
        $appartment_code->save();
        if ($appartment_code->type == 'owner') {
            $this->appartment
            ->where('id', $appartment_code->appartment_id)
            ->update([
                'user_id' => $request->user()->id
            ]);
        }

        return response()->json([
            'success' => 'You add data success'
        ]);
    }
}
