<?php

namespace App\Http\Controllers\api\Village\Visitor;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\VisitRequest;

class VisitorController extends Controller
{
    public function __construct(private VisitRequest $visit_request){}

    public function view(Request $request){
        $visit_requests = $this->visit_request
        ->where('village_id', $request->user()->village_id)
        ->with(['owner', 'appartment'])
        ->get()
        ->map(function($item){
            return [
                'id' => $item->id,
                'unit' => $item?->appartment?->unit,
                'unit_type' => $item?->appartment?->unit?->type?->name,
                'user_name' => $item?->owner?->name,
                'user_phone' => $item?->owner?->phone,
                'visit_type' => $item->visitor_type,
                'date' => $item->date,
                'time' => $item->time,
            ];
        });

        return response()->json([
            'visit_requests' => $visit_requests
        ]);
    }
}
