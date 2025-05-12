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
        ->with(['owner:id,name', 'appartment:id,unit,number_floors'])
        ->get();

        return response()->json([
            'visit_requests' => $visit_requests
        ]);
    }
}
