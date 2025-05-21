<?php

namespace App\Http\Controllers\api\User\Visitors;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\VisitVillage;

class MyVisitorsController extends Controller
{
    public function __construct(private VisitVillage $visit_village){}

    public function view(Request $request){
        $visitors_count = $this->visit_village
        ->where('user_id', $request->user()->id)
        ->whereDate('created_at', date('Y-m-d'))
        ->count();

        return response()->json([
            'visitors_count' => $visitors_count
        ]);
    }
}
