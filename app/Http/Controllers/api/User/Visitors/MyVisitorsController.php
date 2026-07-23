<?php

namespace App\Http\Controllers\api\User\Visitors;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\Validator;

use App\Models\VisitVillage;

class MyVisitorsController extends Controller
{
    public function __construct(private VisitVillage $visit_village){}

    public function view(Request $request){
        $validator = Validator::make($request->all(), [
            'locale' => 'in:ar,en',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors(),
            ], 400);
        }

        $visitors_count = $this->visit_village
        ->where('user_id', $request->user()->id)
        ->whereDate('created_at', date('Y-m-d'))
        ->count();

        return response()->json([
            'visitors_count' => $visitors_count
        ]);
    }
}
