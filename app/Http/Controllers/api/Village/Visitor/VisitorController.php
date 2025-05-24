<?php

namespace App\Http\Controllers\api\Village\Visitor;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\VisitVillage;

class VisitorController extends Controller
{
    public function __construct(private VisitVillage $visit_village){}

    public function view(Request $request){
        $visit_villages = $this->visit_village
        ->where('village_id', $request->user()->village_id) 
        ->get()
        ->map(function($item){
            return [
                'id' => $item->id,
                'unit' => $item?->appartment?->unit,
                'unit_type' => $item?->appartment?->unit?->type?->name,
                'user_name' => $item?->user?->name,
                'user_phone' => $item?->user?->phone,
                'visitor_type' => $item->visitor_type,
                'date' => $item->created_at->format('Y-m-d'),
                'time' => $item->created_at->format('H:i:s'),
            ];
        });

        return response()->json([
            'visit_requests' => $visit_villages
        ]);
    }
}
