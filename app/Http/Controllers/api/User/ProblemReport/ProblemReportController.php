<?php

namespace App\Http\Controllers\api\User\ProblemReport;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\trait\image;

use App\Models\ProblemReport as ProblemsReport;

class ProblemReportController extends Controller
{
    public function __construct(private ProblemsReport $problem_report){}
    use image;
    
    public function add_report(Request $request){
        $validator = Validator::make($request->all(), [
            'google_map' => 'required',
            'description' => 'required',
            'image' => 'required',
            'village_id' => 'required|exists:villages,id',
        ]);
        if ($validator->fails()) { // if Validate Make Error Return Message Error
            $firstError = $validator->errors()->first();
            return response()->json([
                'errors' => $firstError,
            ],400);
        }
        $reportRequest = $validator->validated();
        if ($request->has('image')) {
            $image_path =$this->storeBase64Image($request->image, '/images/problem_report');
            $reportRequest['image'] = $image_path;
        }
        $reportRequest['user_id'] = $request->user()->id;
        $this->problem_report
        ->create($reportRequest);

        return response()->json([
            'success' => 'You add data success'
        ]);
    }

    public function history(Request $request){
        $validator = Validator::make($request->all(), [
            'village_id' => 'required|exists:villages,id',
        ]);
        if ($validator->fails()) { // if Validate Make Error Return Message Error
            $firstError = $validator->errors()->first();
            return response()->json([
                'errors' => $firstError,
            ],400);
        }
        $problem_reports = $this->problem_report
        ->where('village_id', $request->village_id)
        ->where('user_id', $request->user()->id)
        ->get()
        ->map(function($item) use($request){
            return [
                'id' =>$item->id, 
                'google_map' =>$item->google_map,
                'description' =>$item->description,
                'image' =>$item->image_link,
                'status' =>$item->status,
            ];
        });

        return response()->json([
            'problem_reports' => $problem_reports
        ]);
    }

}
