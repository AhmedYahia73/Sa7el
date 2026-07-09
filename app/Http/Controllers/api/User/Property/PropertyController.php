<?php

namespace App\Http\Controllers\api\User\Property;

use App\Events\NotificationEvent;
use App\Http\Controllers\Controller;
use App\Models\Appartment;
use App\Models\AppartmentCode;
use App\Models\CodeRequest;
use App\Models\Notification;
use App\Models\Zone;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

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
                    "status_open" => $item->type != "renter" ? true :
                    $item->from <= now() && $item->to >= now(),
                    "from" => $item->from,
                    "to" => $item->to,
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
                    "status_open" => $item->type != "renter" ? true :
                    $item->from <= now() && $item->to >= now(),
                    "from" => $item->from,
                    "to" => $item->to,
                ];
            }
        });

        return response()->json([
            'appartment' => $appartment,
            'zones' => $zones
        ]);
    }
    
    public function my_new_property(Request $request){
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
        ->where('to', '>=', now())
        ->get()
        ->map(function($item) use($request){
            $appartment = $item->appartment;
            if (empty($appartment)) {
                return [
                    'id' => null,
                    'unit' => null,
                    'image' => $item->village->image_link,
                    'unit_type' => $item->type,
                    'cover_image' => $item->village->cover_image_link,
                    'village_id' => $item->village_id,
                    'village' => $item->village->name, 
                    'type' => null,
                    'zone' => $request->local == 'en' ? $item?->village?->zone?->name
                    : $item?->village?->zone?->ar_name ?? $item?->village?->zone?->name,
                    'zone_id' => $item?->village?->zone_id,
                    'rent_flag' => $item->type == 'renter' ? 1 : 0,
                    'flag' => $item?->village?->from >= now() && 
                    $item?->village?->to <= now() && 
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
                    "status_open" => $item->type != "renter" ? true :
                    $item->from <= now() && $item->to >= now(),
                    "from" => $item->from,
                    "to" => $item->to,
                    "open_status" => ($item->from <= now() && $item->to >= now()) || 
                    $item->type == "owner" ? true : false,
                ];
            } else {
                return [
                    'id' => $appartment->id,
                    'unit' => $appartment->unit,
                    'unit_type' => $item->type,
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
                    'flag' => $appartment?->village?->from <= now() && 
                    $appartment?->village?->to >= now() && 
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
                    "status_open" => $item->type != "renter" ? true :
                    $item->from <= now() && $item->to >= now(),
                    "from" => $item->from,
                    "to" => $item->to,
                    "open_status" => ($item->from <= now() && $item->to >= now()) || 
                    $item->type == "owner" ? true : false,
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
            'local' => 'required|in:en,ar', 
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
            ->where('from', '<=', now())
            ->where('to', '>=', now())
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
            ->where('code', $request->code) 
            ->firstOrFail(); 
            if($appartment_code->village_id != $request->village_id){
                return response()->json([
                    'errors' => 'This code does not belong to this village'
                ],403);
            }
            $appartment_count = $this->appartment_code
            ->where('type', 'owner')
            ->where('village_id', $request->village_id)
            ->where('code', $request->code)
            ->whereNotNull('user_id')
            ->orWhere('type', 'renter')
            ->where('to', '>=', now())
            ->where('village_id', $request->village_id)
            ->where('code', $request->code)
            ->whereNotNull('user_id')
            ->count(); 
            $code_requests = CodeRequest::
            where("appartment_id", $appartment_code->appartment_id ?? null)
            ->where("code", $request->code)
            ->where("village_id", $request->village_id)
            ->where("status", "pending")
            ->count();
            if (($code_requests ?? 0 ) + $appartment_count >= ($appartment_code?->people ?? 0)) {
                if($request->local == "ar"){
                    return response()->json([
                        'errors' => 'لقد تم الوصول إلى الحد الأقصى لعدد المستخدمين المسموح بهم لهذه الوحدة.'
                    ], 404);
                }
                else{
                    return response()->json([
                        'errors' => 'The maximum number of users allowed for this unit has been reached.'
                    ], 404);
                }
            }
            if($appartment_code->type == "renter"){
                  
                $appartment_code_item = AppartmentCode::
                where("code", $appartment_code->code)
                ->where("appartment_id", $appartment_code->appartment_id)
                ->whereNull("user_id")
                ->first();
                $appartment_code_item->user_id = auth()->user()->id;
                $appartment_code_item->save(); 

                return response()->json([
                    'message' => 'You add data success'
                ]);
            }
            $notification = "قام " . auth()->user()->name . " بادخال كود  برقم " . $request->code . "من الابليكشن";
            $data = [
                'village_id' => $request->village_id,
                'code_request_id' => null,
                'login_request_id' => null,
                "type" => "admin", // user, admin
                'notification' => $notification,
            ];
            Notification::create($data);
            NotificationEvent::dispatch($data);
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
        ->whereDoesntHave("code_request", function($query) use($request){
            $query->where("user_id", $request->user()->id)
            ->where("status", "approve");
        })
        ->with([
            "village.zone", "village.package", "type", 
        ])
        ->get()
        ->unique("id")
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
                'flag' => $appartment?->village?->from <= now() && 
                $appartment?->village?->to >= now() ? true : false,
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

    public function rent_images(Request $request){
        $validator = Validator::make($request->all(), [
            'code' => 'required',
        ]);
        if ($validator->fails()) { // if Validate Make Error Return Message Error
            $firstError = $validator->errors()->first();
            return response()->json([
                'errors' => $firstError,
            ],400);
        }

        $rent_images = AppartmentCode::
        where("code", $request->code)
        ->with("rent_images")
        ->first()?->rent_images;

        return response()->json([
            "rent_images" => $rent_images
        ]);
    }
}
