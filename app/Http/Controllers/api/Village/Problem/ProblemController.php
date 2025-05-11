<?php

namespace App\Http\Controllers\api\Village\Problem;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

use App\Models\ProblemReport;
use App\Models\AppartmentCode;

class ProblemController extends Controller
{
    public function __construct(private ProblemReport $problem_report,
    private AppartmentCode $appartment){}

    public function view(Request $request){
        $appartment = $this->appartment;
        $problem_reports = $this->problem_report
        ->where('village_id', $request->user()->id)
        ->get()
        ->map(function($item) use($appartment, $request){
            $appartment = $appartment
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

    public function status(Request $request, $id){
        $validator = Validator::make($request->all(), [
            'status' => 'required|boolean',
        ]);
        if ($validator->fails()) { // if Validate Make Error Return Message Error
            return response()->json([
                'errors' => $validator->errors(),
            ],400);
        }
        $this->problem_report
        ->where('id', $id)
        ->update([
            'status' => $request->status
        ]);

        return response()->json([
            'success' => $request->status ? 'active' : 'banned'
        ]);
    }
}
