<?php

namespace App\Http\Controllers\api\Village\Problem;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\ProblemReport;
use App\Models\AppartmentCode;

class ProblemController extends Controller
{
    public function __construct(private ProblemReport $problem_report,
    private AppartmentCode $appartment){}

    public function view(Request $request){
        $problem_reports = $this->problem_report
        ->where('village_id', $request->user()->id)
        ->get()
        ->map(function($item) use($appartment, $request){
            $appartment = $this->appartment
            ->where('village_id', $request->user()->village_id)
            ->where('user_id', $request->user()->id)
            ->where('type', 'owner')
            ->orWhere('village_id', $request->user()->village_id)
            ->where('user_id', $request->user()->id)
            ->orderByDesc('id')
            ->first();
            return [
                'id' =>$item->id,
                'owner' =>$item?->user?->name,
                'google_map' =>$item->google_map,
                'description' =>$item->description,
                'image' =>$item->image_link,
                'status' =>$item->status,
                'owner_type' => $appartment?->type,
            ];
        });

        return response()->json([
            'problem_reports' => $problem_reports
        ]);
    }
}
