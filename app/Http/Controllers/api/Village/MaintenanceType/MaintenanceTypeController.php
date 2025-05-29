<?php

namespace App\Http\Controllers\api\Village\MaintenanceType;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

use App\Models\MaintenanceType;

class MaintenanceTypeController extends Controller
{
    public function __construct(private MaintenanceType $maintenance_type){}

    public function view(Request $request){
        $maintenance_types = $this->maintenance_type
        ->whereDoesntHave('village', function($query) use($request){
            $query->where('villages.id', $request->user()->village_id);
        })
        ->get();
        $my_maintenance_types = $this->maintenance_type
        ->whereHas('village', function($query) use($request){
            $query->where('villages.id', $request->user()->village_id);
        })
        ->with('village', function($query) use($request){
            $query->where('villages.id', $request->user()->village_id);
        })
        ->get()
        ->map(function($item){
            return [
                'id' => $item->id,
                'name' => $item->name,
                'image' => $item->image,
                'image_link' => $item->image_link,
                'ar_name' => $item->ar_name,
                'status' => $item->village[0]->pivot->status,
            ];
        });

        return response()->json([
            'maintenance_types' => $maintenance_types,
            'my_maintenance_types' => $my_maintenance_types,
        ]);
    }

    public function status(Request $request, $id){
        $validator = Validator::make($request->all(), [
            'status' => 'required|boolean',
        ]);
        if ($validator->fails()) { // if Validate Make Error Return Message Error
            return response()->json([
                'errors' => $validator->errors(),
            ],400);
        }

        $pivot = DB::table('maintenance_type_villages')
        ->where('maintenance_types_id', $id)
        ->where('village_id', $request->user()->village_id)
        ->update([
            'status' => $request->status
        ]);

        return response()->json([
            'success' => 'You update status success'
        ]);
    }

    public function add(Request $request){
        // maintenance_type_id,
        $validator = Validator::make($request->all(), [
            'maintenance_type_id' => 'required|exists:maintenance_types,id',
        ]);
        if ($validator->fails()) { // if Validate Make Error Return Message Error
            return response()->json([
                'errors' => $validator->errors(),
            ],400);
        }
        $maintenance_types = $this->maintenance_type
        ->where('id', $request->maintenance_type_id)
        ->first();
        $maintenance_types->village()
        ->attach($request->user()->village_id);

        return response()->json([
            'success' => 'You add maintenance success'
        ]);
    }

    public function delete(Request $request){
        // maintenance_type_id,
        $validator = Validator::make($request->all(), [
            'maintenance_type_id' => 'required|exists:maintenance_types,id',
        ]);
        if ($validator->fails()) { // if Validate Make Error Return Message Error
            return response()->json([
                'errors' => $validator->errors(),
            ],400);
        }
        $maintenance_types = $this->maintenance_type
        ->where('id', $request->maintenance_type_id)
        ->first();
        $maintenance_types->village()
        ->detach($request->user()->village_id);

        return response()->json([
            'success' => 'You add maintenance success'
        ]);
    }
    // village
}
