<?php

namespace App\Http\Controllers\api\Village\Entrance;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\VisitVillage;
use App\Models\EntranceBeach;
use App\Models\EntrancePool;

class EntranceController extends Controller
{
    public function __construct(private VisitVillage $gate,
    private EntranceBeach $beach, private EntrancePool $pool){}

    public function entrance_gate(Request $request){
        $gate = $this->gate
        ->where('village_id', $request->user()->village_id)
        ->get()
        ->map(function($item){
            return [
                'time' => $item->updated_at->format('H:i:s') ?? null,
                'gate' => $item?->gate?->name,
                'gate_location' => $item?->gate?->location,
                'user_name' => $item?->user?->name,
                'user_phone' => $item?->user?->phone,
                'user_email' => $item?->user?->email,
                'appartment' => $item?->appartment?->unit,
                'appartment_type' => $item?->appartment?->type?->name ?? null,
                'date' => $item?->created_at?->format('Y-m-d') ?? null,
            ];
        });

        return response()->json([
            'gate' => $gate
        ]);
    }

    public function entrance_beach(Request $request){
        $beach = $this->beach
        ->where('village_id', $request->user()->village_id)
        ->get()
        ->map(function($item){
            return [
                'time' => $item->time,
                'beach' => $item?->beach?->name, 
                'user_name' => $item?->user?->name,
                'user_phone' => $item?->user?->phone,
                'user_email' => $item?->user?->email,
                'date' => $item?->created_at?->format('Y-m-d') ?? null,
            ];
        });

        return response()->json([
            'beach' => $beach
        ]);
    }

    public function entrance_pool(Request $request){
        $pool = $this->pool
        ->where('village_id', $request->user()->village_id)
        ->get()
        ->map(function($item){
            return [
                'time' => $item->time,
                'pool' => $item?->pool?->name, 
                'user_name' => $item?->user?->name,
                'user_phone' => $item?->user?->phone,
                'user_email' => $item?->user?->email,
                'date' => $item?->created_at?->format('Y-m-d') ?? null,
            ];
        });

        return response()->json([
            'pool' => $pool
        ]);
    }
}
