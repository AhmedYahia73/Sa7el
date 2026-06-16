<?php

namespace App\Http\Controllers\api\User\Property;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

use App\Models\Appartment;
use App\Models\AppartmentCode;
use App\Models\CodeRequest;
use App\Models\Zone;

class PropertyController extends Controller
{
    public function __construct(private Appartment $appartment,
    private AppartmentCode $appartment_code, private Zone $zones){}

    public function my_property(Request $request){
        $validator = Validator::make($request->all(), [
            'local' => 'required|in:en,ar',
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
        ->where('user_id', $request->user()->id)
        ->orWhere('type', 'renter')
        ->where('user_id', $request->user()->id)
        ->where('from', '<=', date('Y-m-d'))
        ->where('to', '>=', date('Y-m-d'))
        ->get()
        ->map(function($item) use($request){
            $appartment = $item->appartment;
            if (empty($appartment)) {
                return [
                    'id' => null,
                    'unit' => null,
                    'image' => $item->village->image_link,
                    'cover_image' => $item->village->cover_image_link,
                    'village_id' => $item->village_id,
                    'village' => $item->village->name, 
                    'type' => null,
                    'zone' => $request->local == 'en' ? $item?->village?->zone?->name
                    : $item?->village?->zone?->ar_name ?? $item?->village?->zone?->name,
                    'zone_id' => $item?->village?->zone_id,
                    'rent_flag' => $item->type == 'renter' ? 1 : 0,
                    'flag' => $item?->village?->from >= date('Y-m-d') && 
                    $item?->village?->to <= date('Y-m-d') && 
                    !empty($item->code) ? true : false,
                    'pool_beach_flag' => $item?->village?->package?->beach_pool_module ? 1 : 0,
                    'maintenance_flag' => $item?->village?->package?->maintenance_module ? 1 : 0, 
                    'entrance_status' => false,
                    'pool_status' => false,
                    'beach_status' => false,
                    'rent_code_status' => false,
                    'selling_status' => false,
                    'rent_status' => false,
                    'visits_status' => false,
                    'options_status' => false,
                    'all_status' => false,
                ];
            } else {
                return [
                    'id' => $appartment->id,
                    'unit' => $appartment->unit,
                    'image' => $appartment->village->image_link,
                    'cover_image' => $appartment->village->cover_image_link,
                    'village_id' => $appartment->village_id,
                    'village' => $appartment->village->name, 
                    'type' => $request->local == 'en' ? $appartment?->type?->name : 
                    $appartment?->type?->ar_name ?? $appartment?->type?->name, 
                    'zone' => $request->local == 'en' ? $item?->village?->zone?->name
                    : $item?->village?->zone?->ar_name ?? $item?->village?->zone?->name,
                    'zone_id' => $item?->village?->zone_id,
                    'rent_flag' => $item->type == 'renter' ? 1 : 0,
                    'flag' => $appartment?->village?->from <= date('Y-m-d') && 
                    $appartment?->village?->to >= date('Y-m-d') && 
                    !empty($item->code) ? true : false,
                    'pool_beach_flag' => $item?->village?->package?->beach_pool_module ? 1 : 0,
                    'maintenance_flag' => $item?->village?->package?->maintenance_module ? 1 : 0,
                    'entrance_status' => $appartment->entrance_status,
                    'pool_status' => $appartment->pool_status,
                    'beach_status' => $appartment->beach_status,
                    'rent_code_status' => $appartment->rent_code_status,
                    'selling_status' => $appartment->selling_status,
                    'rent_status' => $appartment->rent_status,
                    'visits_status' => $appartment->visits_status,
                    'options_status' => $appartment->options_status,
                    'all_status' => $appartment->all_status,    
                ];
            }
        });

        return response()->json([
            'appartment' => $appartment,
            'zones' => $zones
        ]);
    }

    public function add_property(Request $request){
        $validator = Validator::make($request->all(), [
            'village_id' => 'required|exists:villages,id',
            'code' => 'sometimes',
        ]);
        if ($validator->fails()) { // if Validate Make Error Return Message Error
            $firstError = $validator->errors()->first();
            return response()->json([
                'errors' => $firstError,
            ],400);
        }

        if (!$request->code) {
            $validator_errors = Validator::make($request->all(), [
                'unit' => 'required',
                'appartment_type_id' => 'required|exists:appartment_types,id',
            ]);
            if ($validator_errors->fails()) { // if Validate Make Error Return Message Error
                $firstError = $validator_errors->errors()->first();
                return response()->json([
                    'errors' => $firstError,
                ],400);
            }
            $appartment = $this->appartment
            ->create([
                'unit' => $request->unit,
                'appartment_type_id' => $request->appartment_type_id,
                'village_id' => $request->village_id,
                'user_id' => $request->user()->id,
            ]);
            $this->appartment_code
            ->create([
                'user_id' => $request->user()->id,
                'village_id' => $request->village_id,
                'type' => 'owner',
                'appartment_id' => $appartment->id,
            ]);

            return response()->json([
                'message' => 'You add data success'
            ]);
        }
        else{
            if ($request->appartment_id) {
                $this->appartment_code
                ->where('type', 'owner')
                ->where('village_id', $request->village_id)
                ->where('appartment_id', $request->appartment_id)
                ->whereNull('code')
                ->delete();
            }
            $appartment_code = $this->appartment_code
            ->where('type', 'owner')
            ->where('village_id', $request->village_id)
            ->where('code', $request->code)
            ->whereNotNull('code')
            ->where('user_id', $request->user()->id)
            ->orWhere('type', 'renter')
            ->whereNotNull('code')
            ->where('from', '<=', date('Y-m-d'))
            ->where('to', '>=', date('Y-m-d'))
            ->where('village_id', $request->village_id)
            ->where('code', $request->code)
            ->where('user_id', $request->user()->id)
            ->first();
            if (!empty($appartment_code)) {
                return response()->json([
                    'message' => 'appartment already added'
                ]);
            }

            $appartment_code = $this->appartment_code
            ->where('type', 'owner')
            ->where('village_id', $request->village_id)
            ->where('code', $request->code)
            ->whereNull('user_id')
            ->orWhere('type', 'renter')
            ->where('from', '<=', date('Y-m-d'))
            ->where('to', '>=', date('Y-m-d'))
            ->where('village_id', $request->village_id)
            ->where('code', $request->code)
            ->whereNull('user_id')
            ->first();
            if (empty($appartment_code)) {
                return response()->json([
                    'message' => 'appartment is not found'
                ]);
            }
            CodeRequest::create([
                'user_id' => auth()->user()->id,
                'appartment_id' => $appartment_code->appartment_id,
                'code' => $request->code,
                'appartment_codes',
                'village_id' => $request->village_id,
                'status' => "pending",
                "appartment_codes" => []
            ]);
        }

        return response()->json([
            'message' => 'You add data success'
        ]);
    }

    public function pending_code_request(Request $request){
        $validator = Validator::make($request->all(), [
            'local' => 'required|in:en,ar',
        ]);
        if ($validator->fails()) { // if Validate Make Error Return Message Error
            $firstError = $validator->errors()->first();
            return response()->json([
                'errors' => $firstError,
            ],400);
        }
        
        $appartments = Appartment::
        whereHas("code_request", function($query) use($request){
            $query->where("user_id", $request->user()->id)
            ->where("status", "pending");
        })
        ->with([
            "village.zone", "village.package", "type", 
        ])
        ->get()
        ->uniqid("id")
        ->map(function($appartment) use($request) {
            return [
                'id' => $appartment->id,
                'unit' => $appartment->unit,
                'image' => $appartment->village->image_link,
                'cover_image' => $appartment->village->cover_image_link,
                'village_id' => $appartment->village_id,
                'village' => $appartment->village->name, 
                'type' => $request->local == 'en' ? $appartment?->type?->name : 
                $appartment?->type?->ar_name ?? $appartment?->type?->name, 
                'zone' => $request->local == 'en' ? $appartment?->village?->zone?->name
                : $appartment?->village?->zone?->ar_name ?? $appartment?->village?->zone?->name,
                'zone_id' => $appartment?->village?->zone_id,
                'rent_flag' => $appartment->type == 'renter' ? 1 : 0,
                'flag' => $appartment?->village?->from <= date('Y-m-d') && 
                $appartment?->village?->to >= date('Y-m-d') ? true : false,
                'pool_beach_flag' => $appartment?->village?->package?->beach_pool_module ? 1 : 0,
                'maintenance_flag' => $appartment?->village?->package?->maintenance_module ? 1 : 0,
                'entrance_status' => $appartment->entrance_status,
                'pool_status' => $appartment->pool_status,
                'beach_status' => $appartment->beach_status,
                'rent_code_status' => $appartment->rent_code_status,
                'selling_status' => $appartment->selling_status,
                'rent_status' => $appartment->rent_status,
                'visits_status' => $appartment->visits_status,
                'options_status' => $appartment->options_status,
                'all_status' => $appartment->all_status,    
            ];
        });

        return response()->json([
            "appartments" => $appartments
        ]);
    }
}
