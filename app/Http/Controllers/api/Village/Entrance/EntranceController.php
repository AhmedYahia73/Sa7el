<?php

namespace App\Http\Controllers\api\Village\Entrance;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\VisitVillage;
use App\Models\UserBeach;
use App\Models\EntrancePool;

class EntranceController extends Controller
{
    public function __construct(private VisitVillage $gate,
    private UserBeach $beach, private EntrancePool $pool){}

    public function entrance_gate(Request $request){
        $gate = $this->gate
        ->where('village_id', $request->user()->village_id)
        ->get()
        ->map(function($item){
            return [
                'time' => $item?->created_at?->format('H:i:s'),
                'gate' => $item?->gate?->name,
                'gate_location' => $item?->gate?->location,
                'user_name' => $item?->user?->name,
                'user_phone' => $item?->user?->phone,
                'user_email' => $item?->user?->email,
                'date' => $item?->created_at?->format('Y-m-d') ?? null,
                'visitor_type' => $item->visitor_type,
                'user_type' => $item->user_type,
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
                'time' => $item?->created_at?->format('H:i:s'),
                'beach' => $item?->beach?->name, 
                'user_name' => $item?->user?->name,
                'user_phone' => $item?->user?->phone,
                'user_email' => $item?->user?->email,
                'date' => $item?->created_at?->format('Y-m-d') ?? null,
                'user_type' => $item->user_type,
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
                'time' => $item?->created_at?->format('H:i:s'),
                'pool' => $item?->pool?->name, 
                'user_name' => $item?->user?->name,
                'user_phone' => $item?->user?->phone,
                'user_email' => $item?->user?->email,
                'date' => $item?->created_at?->format('Y-m-d') ?? null,
                'user_type' => $item->user_type,
            ];
        });

        return response()->json([
            'pool' => $pool
        ]);
    }
}
