<?php

namespace App\Http\Controllers\api\Village\Visitor;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\VisitVillage;
use App\Models\EntranceGate;

class VisitorController extends Controller
{
    public function __construct(private VisitVillage $visit_village,
    private EntranceGate $gate){}

    public function view(Request $request){
        $visit_villages = $this->visit_village
        ->where('village_id', $request->user()->village_id) 
        ->get()
        ->map(function($item){
            return [
                'id' => $item->id,
                'unit' => $item?->appartment?->unit,
                'unit_type' => $item?->appartment?->type?->name,
                'user_name' => $item?->user?->name,
                'user_phone' => $item?->user?->phone,
                'visitor_type' => $item->visitor_type,
                'date' => $item->created_at?->format('Y-m-d'),
                'time' => $item->created_at?->format('H:i:s'),
                'gate' => $item?->gate?->name,
                'user_type' => $item->user_type
            ];
        });

        return response()->json([
            'visit_requests' => $visit_villages
        ]);
    }
}
